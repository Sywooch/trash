<?php
use dfs\docdoc\models\RequestModel;

/**
 * Файл класса m140812_121701_DD_138_billing
 */
class m140812_121701_DD_138_billing extends CDbMigration
{
	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{

		$this->execute("ALTER TABLE contract_dict ENGINE=InnoDB");

		//Создаем таблицу с тарифами клиник
		$this->execute("
			CREATE TABLE `clinic_contract` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`clinic_id` INT(11) NOT NULL,
			`contract_id` TINYINT NOT NULL,
			`rules` TEXT NULL,
			PRIMARY KEY (`id`),
			INDEX `fk_clinic_contract_1_idx` (`clinic_id` ASC),
			INDEX `fk_clinic_contract_2_idx` (`contract_id` ASC),
			CONSTRAINT `fk_clinic_contract_1`
				FOREIGN KEY (`clinic_id`)
				REFERENCES `clinic` (`id`)
				ON DELETE CASCADE
				ON UPDATE CASCADE,
			CONSTRAINT `fk_clinic_contract_2`
				FOREIGN KEY (`contract_id`)
				REFERENCES `contract_dict` (`contract_id`)
				ON DELETE RESTRICT
				ON UPDATE RESTRICT)
			ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='контракты клиники'
		");

		//добавляем столбец kind с типом заявок
		$this->addColumn('contract_dict', 'kind', 'tinyint(1) DEFAULT null');

		$this->execute("UPDATE contract_dict SET kind = " . RequestModel::KIND_DOCTOR . " WHERE isClinic='yes'");
		$this->execute("UPDATE contract_dict SET kind = " . RequestModel::KIND_DIAGNOSTICS . " WHERE isDiagnostic='yes'");

		//заливка таблицы contract_dict данными
		$this->execute("
			INSERT INTO clinic_contract (clinic_id, contract_id, rules)
				SELECT c.id, cs.contract_id, '{}'
				FROM clinic c
				INNER JOIN clinic_settings cs ON cs.settings_id = c.settings_id
				WHERE c.settings_id IS NOT NULL
		");


		$this->execute("TRUNCATE TABLE clinic_contract");

		$this->execute("
			INSERT INTO clinic_contract (clinic_id, contract_id, rules)
				SELECT c.id, ds.contract_id, '{}'
				FROM clinic c
				INNER JOIN diagnostica_settings ds ON ds.settings_id = c.diag_settings_id
				WHERE c.diag_settings_id IS NOT NULL AND contract_id != 0
		");

		//добавляем столбец со стоимостью заявки
		$this->execute("ALTER TABLE `request`
			CHANGE COLUMN `appointment_price` `request_cost` INT(11) NULL DEFAULT NULL
			COMMENT 'Стоимость заяви в биллинге'");

		$this->execute("UPDATE request SET request_cost = NULL");

		//на основании date_record будут выбираться интервалы для заявок в билинге
		//сейчас есть заявки с записью, но с пустым date_record
		//для таких заявок проставляем date_record = req_created
		$this->execute("
			UPDATE
				request
			SET
				date_record =  FROM_UNIXTIME(req_created)
			WHERE
				date_record IS NULL AND date_admission IS NOT NULL
				AND date_admission > 0
		");


		//добавляем новый тариф
		$this->execute("
			INSERT INTO
				contract_dict (contract_id, title, description, isClinic, isDiagnostic, kind)
			VALUES (7, 'Диагностика. Онлайн-запись', null, 'no', 'yes', '" . RequestModel::KIND_DIAGNOSTICS . "')"
		);

	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropTable("clinic_contract");
		$this->dropColumn("contract_dict", "kind");
		$this->execute("ALTER TABLE `request`
			CHANGE COLUMN `request_cost` `appointment_price` INT(11) NOT NULL DEFAULT '800'
		");
		$this->execute("UPDATE request SET appointment_price = 800");
		$this->execute("DELETE FROM contract_dict WHERE contract_id = 7");
	}
}