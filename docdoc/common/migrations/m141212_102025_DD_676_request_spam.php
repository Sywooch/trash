<?php

/**
 * Class m141212_102025_DD_676_request_spam
 */
class m141212_102025_DD_676_request_spam extends CDbMigration
{
	/**
	 * Добавляем таблицу для спама по заявкам
	 *
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute("
			CREATE TABLE `request_spam` (
			  `req_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `id_city` int(11) DEFAULT '1',
			  `client_name` varchar(255) NOT NULL,
			  `client_phone` varchar(255) NOT NULL,
			  `req_created` int(10) unsigned NOT NULL,
			  `req_status` tinyint(3) unsigned NOT NULL DEFAULT '1',
			  `req_departure` tinyint(1) DEFAULT '0',
			  `req_sector_id` int(11) DEFAULT '0',
			  `diagnostics_id` int(4) DEFAULT '0',
			  `req_doctor_id` int(11) NOT NULL DEFAULT '0',
			  `req_type` tinyint(4) unsigned NOT NULL,
			  `kind` tinyint(1) NOT NULL DEFAULT '0',
			  `source_type` int(3) DEFAULT '1',
			  `clinic_id` int(11) DEFAULT NULL,
			  `date_admission` int(11) DEFAULT NULL,
			  `appointment_time` int(11) DEFAULT NULL,
			  `age_selector` enum('multy','child','adult') DEFAULT 'multy',
			  `client_comments` text,
			  `partner_id` int(11) DEFAULT NULL,
			  `date_record` datetime DEFAULT NULL,
			  `enter_point` varchar(20) DEFAULT NULL COMMENT 'Точка входа, в которой была создана заявка',
			  `token` char(32) DEFAULT NULL,
			  PRIMARY KEY (`req_id`),
			  KEY `client_phone` (`client_phone`),
			  KEY `token` (`token`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8
		");
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->dropTable('request_spam');
	}
}