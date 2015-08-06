<?php

class m140710_190218_request_record_for_replaced_phone extends CDbMigration
{
	public function up()
	{
		$this->execute('ALTER TABLE request_record ADD replaced_phone varchar(15) DEFAULT null;');

		$this->execute(
			"CREATE TABLE `call_log` (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `ext_id` varchar(20) NOT NULL,
			  `start_time` datetime NOT NULL,
			  `duration` time NOT NULL,
			  `ani` varchar(12) NOT NULL,
			  `did` varchar(12) NOT NULL,
			  `tariff_duration` time NOT NULL,
			  `tariff` decimal(10,4) NOT NULL,
			  `cost` decimal(10,4) NOT NULL,
			  `application_type_id` varchar(10) NOT NULL,
			  `sort` varchar(10) NOT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `id` (`id`),
			  UNIQUE KEY `unique_ext_id` (`ext_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);
	}

	public function down()
	{
		$this->execute('ALTER TABLE request_record drop column replaced_phone;');

		$this->execute('drop table `call_log`;');
	}
}
