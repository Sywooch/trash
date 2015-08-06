<?php

/**
 * Class m141210_102916_DD_620_clinic_request_limit
 */
class m141210_102916_DD_620_clinic_request_limit extends CDbMigration
{
	/**
	 * Таблица с лимитом на кол-во записей в клинику
	 *
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute("
			CREATE TABLE `clinic_request_limit` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`group_uid` int(11) DEFAULT NULL,
				`limit` smallint(5) DEFAULT 0,
				`date_notice` date DEFAULT NULL,
				`clinic_contract_id` int(11) DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `date_notice` (`date_notice`),
				CONSTRAINT `fk_clinic_request_limit_1` FOREIGN KEY (`clinic_contract_id`) REFERENCES `clinic_contract` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='лимиты на кол-во заявок по клиникам'
		");
	}

	/**
	 * @return bool
	 */
	public function down()
	{
		$this->dropTable('clinic_request_limit');
	}
}