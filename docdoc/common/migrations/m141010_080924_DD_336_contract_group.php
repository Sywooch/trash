<?php
use dfs\docdoc\models\ClinicContractCostModel;
/**
 * Class m141010_080924_DD_336_contract_group
 */
class m141010_080924_DD_336_contract_group extends CDbMigration
{
	/**
	 * Добавление группы услуг
	 *
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute(
			"CREATE TABLE `contract_group` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`name` VARCHAR(50) NOT NULL,
				`kind` TINYINT NOT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `id` (`id`),
				KEY `contract_group_kind` (`kind`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$this->execute(
			"CREATE TABLE `contract_group_service` (
				`contract_group_id` INT(11) NOT NULL,
				`service_id` INT(11) NOT NULL,
				PRIMARY KEY (`contract_group_id`,`service_id`),
				KEY `contract_group_id` (`contract_group_id`),
				KEY `service_id` (`service_id`),
				CONSTRAINT `contract_group_service_ibfk_1` FOREIGN KEY (`contract_group_id`) REFERENCES `contract_group` (`id`) ON DELETE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$this->insert('contract_group', ['id' => 1, 'name' => 'Все специальности', 'kind' => 0]);
		$this->insert('contract_group', ['id' => 2, 'name' => 'Все диагностики', 'kind' => 1]);

		$this->insert('contract_group', ['id' => 3, 'name' => 'Стоматология / Хирургия', 'kind' => 0]);
		$this->insert('contract_group', ['id' => 4, 'name' => 'МРТ / КТ', 'kind' => 1]);
		$this->insert('contract_group', ['id' => 5, 'name' => 'Электроэнцефалография', 'kind' => 1]);

		$this->insert('contract_group_service', ['contract_group_id' => 1, 'service_id' => 0]);
		$this->insert('contract_group_service', ['contract_group_id' => 2, 'service_id' => 0]);

		$this->insert('contract_group_service', ['contract_group_id' => 3, 'service_id' => 86]);
		$this->insert('contract_group_service', ['contract_group_id' => 3, 'service_id' => 90]);

		$this->insert('contract_group_service', ['contract_group_id' => 4, 'service_id' => 19]);
		$this->insert('contract_group_service', ['contract_group_id' => 4, 'service_id' => 21]);
		$this->insert('contract_group_service', ['contract_group_id' => 5, 'service_id' => 179]);

		$this->addColumn('clinic_contract_cost', 'group_uid', 'INT(11) NOT NULL');

		//все тарифы запихиваем в уникальные группы
		//мрт и кт засовываем в одну группу
		$tariffs = ClinicContractCostModel::model()->with(['tariff', 'tariff.contract'])->findAll();

		foreach ($tariffs as $t) {

			$groupUid = null;

			if ($t->tariff->contract->kind == 0) {
				$groupUid =  in_array($t->service_id, [86, 90]) ? 3 : 1;
			}

			if ($t->tariff->contract->kind == 1) {

				$groupUid = 2;
				if (in_array($t->service_id, [19, 21])) {
					$groupUid = 4;
				}

				if (in_array($t->service_id, [179])) {
					$groupUid = 5;
				}
			}
			$t->updateByPk($t->id, ['group_uid' => $groupUid]);
		}

	}

	public function down()
	{
		$this->dropTable('contract_group_service');
		$this->dropTable('contract_group');
		$this->dropColumn('clinic_contract_cost', 'group_uid');
	}
}
