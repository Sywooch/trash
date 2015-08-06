<?php

/**
 * Файл класса m140819_061732_DD_155_add_billing.
 *
 * Добавление флага в биллинге эта заявка или нет
 *
 */
class m140819_061732_DD_155_add_billing extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->addColumn("request", "billing_status", "TINYINT(1) DEFAULT 0");

		$this->execute(
			"CREATE TABLE `clinic_contract_cost` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`service_id` INT(11) NULL COMMENT 'sector_id/diagnostic_id в зависимости от kind',
				`cost` FLOAT(6,2) NULL COMMENT 'стоимость заявки',
				`clinic_contract_id` INT(11) NULL COMMENT 'id контракта клиники',
				`from_num` SMALLINT(5) NULL COMMENT 'начальное количество заявок',
			PRIMARY KEY (`id`),
				INDEX `fk_clinic_contract_cost_1_idx` (`clinic_contract_id` ASC),
			CONSTRAINT `fk_clinic_contract_cost_1`
				FOREIGN KEY (`clinic_contract_id`)
				REFERENCES `clinic_contract` (`id`)
				ON DELETE CASCADE
				ON UPDATE CASCADE)
			ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='стоимость услуг в контракте клиники'"
		);

		$this->dropColumn('clinic_contract', 'rules');

	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropColumn("request", "billing_status");
		$this->addColumn('clinic_contract', 'rules', 'TEXT');
		$this->dropTable('clinic_contract_cost');
	}
}