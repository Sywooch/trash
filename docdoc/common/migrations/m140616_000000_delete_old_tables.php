<?php

/**
 * Файл класса m140616_000000_delete_old_tables.
 *
 * Удаляет старые неиспользуемые таблицы
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003862/card/
 * @package common.migrations
 */
class m140616_000000_delete_old_tables extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		if (Yii::app()->db->schema->getTable("sedule_exeption")) {
			$this->dropTable("sedule_exeption");
		}

		if (Yii::app()->db->schema->getTable("shedule_appointment")) {
			$this->dropTable("shedule_appointment");
		}

		if (Yii::app()->db->schema->getTable("shedule_rules")) {
			$this->dropTable("shedule_rules");
		}

		if (Yii::app()->db->schema->getTable("request_shedule")) {
			$this->dropTable("request_shedule");
		}

		if (Yii::app()->db->schema->getTable("doctor_shedule_week")) {
			$this->dropTable("doctor_shedule_week");
		}

		if (Yii::app()->db->schema->getTable("sector_4_remote_api")) {
			$this->dropIndex("sector_id", "sector_4_remote_api");
			$this->dropIndex("sector", "sector_4_remote_api");
			$this->dropTable("sector_4_remote_api");
		}
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->execute("
			DROP TABLE IF EXISTS `sedule_exeption`;
			CREATE TABLE `sedule_exeption` (
				`doctor_id` int(11) NOT NULL DEFAULT '0',
				`clinic_id` int(11) NOT NULL DEFAULT '0',
				`exeption_date` date NOT NULL,
				`description` text,
				PRIMARY KEY (`doctor_id`,`clinic_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Исключения в расписании';
		");

		$this->execute("
			DROP TABLE IF EXISTS `shedule_appointment`;
			CREATE TABLE `shedule_appointment` (
				`doctor_id` int(11) NOT NULL,
				`time_appointment` int(11) NOT NULL,
				`status` int(11) NOT NULL DEFAULT '0',
				`range_time` int(11) NOT NULL DEFAULT '0',
				PRIMARY KEY (`doctor_id`,`time_appointment`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");

		$this->execute("
			DROP TABLE IF EXISTS `shedule_rules`;
			CREATE TABLE `shedule_rules` (
				`week` tinyint(4) NOT NULL,
				`hour` tinyint(4) NOT NULL,
				`doctor_id` int(11) NOT NULL DEFAULT '0',
				`clinic_id` int(11) NOT NULL DEFAULT '0',
				PRIMARY KEY(`week`, `hour`, `doctor_id`, `clinic_id`)
			) ENGINE=INNODB  DEFAULT CHARACTER SET = utf8 COMMENT = 'Правила расписаний';
		");

		$this->execute("
			DROP TABLE IF EXISTS `request_shedule`;
			CREATE TABLE `request_shedule` (
				`req_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
				`week` tinyint(4) NOT NULL DEFAULT '0',
				`hour` tinyint(4) NOT NULL DEFAULT '0',
				`doctor_id` int(11) NOT NULL DEFAULT '0',
				`clinic_id` int(11) NOT NULL DEFAULT '0',
				`crDate` datetime,
				`type` enum('front','back') DEFAULT 'front',
				`status` enum('active','whait','canceled') DEFAULT 'whait',
				PRIMARY KEY(`week`, `hour`, `doctor_id`, `clinic_id`, `req_id`)
			) ENGINE=INNODB  DEFAULT CHARACTER SET = utf8  COMMENT = 'Запись на время';
		");

		$this->execute("
			DROP TABLE IF EXISTS `doctor_shedule_week`;
			CREATE TABLE `doctor_shedule_week` (
				`doctor_id` int(11) NOT NULL,
				`day` int(11) NOT NULL,
				`data` text,
				UNIQUE KEY `doctor_id` (`doctor_id`,`day`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");

		$this->execute("
			DROP TABLE IF EXISTS `sector_4_remote_api`;
				CREATE TABLE `sector_4_remote_api` (
				`sector_id` int(11) NOT NULL,
				`sector_api_id` varchar(50) NOT NULL,
				`api_id` int(11) NOT NULL,
				UNIQUE KEY `sector_id` (`sector_id`,`sector_api_id`),
				UNIQUE KEY `sector` (`sector_id`,`api_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
	}
}