<?php

class m140801_091608_add_comagic_log_table extends CDbMigration
{
	public function up()
	{
		$this->execute(
			'
				CREATE TABLE `comagic_log` (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`numa` varchar(20) NOT NULL,
				`numb` varchar(20) NOT NULL,
				`ac_id` int(11) NOT NULL,
				`call_date` datetime(6) NOT NULL,
				`wait_time` int(11) NOT NULL,
				`duration` int(11) NOT NULL,
				`status` varchar(20) NOT NULL,
				`utm_source` varchar(30) NOT NULL,
				`utm_medium` varchar(50) NOT NULL,
				`utm_term` varchar(50) NOT NULL,
				`utm_content` varchar(50) NOT NULL,
				`utm_campaign` varchar(50) NOT NULL,
				`os_service_name` varchar(50) NOT NULL,
				`os_campaign_id` varchar(50) NOT NULL,
				`os_ad_id` varchar(50) NOT NULL,
				`os_source_id` varchar(50) NOT NULL,
				`session_start` varchar(50) NOT NULL,
				`visitor_id` int(11) NOT NULL,
				`search_engine` varchar(50) NOT NULL,
				`search_query` varchar(255) NOT NULL,
				`file_link` varchar(255) NOT NULL,
				`ua_client_id` int(11) NOT NULL,
				`page_url` varchar(255) NOT NULL,
				`referrer` varchar(255) NOT NULL,
				`ef_id` varchar(255) NOT NULL,
				`request_id` int(11) DEFAULT NULL,
				`checked_time` datetime(6) DEFAULT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `id` (`id`),
				UNIQUE KEY `numa_call_date_index` (`numa`,`call_date`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			'
		);
	}

	public function down()
	{
		$this->execute('drop table comagic_log;');
	}
}
