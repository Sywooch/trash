<?php

class m140625_130645_create_partner_cost_table extends CDbMigration
{
	/**
	 * Таблица для партнерских цен
	 */
	public function up()
	{
		$this->execute(
			'CREATE TABLE `partner_cost` (
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			  `partner_id` int(11) DEFAULT NULL,
			  `service_id` int(11) NOT NULL,
			  `cost` decimal(10,6) NOT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `id` (`id`),
			  KEY `partner_id` (`partner_id`),
			  CONSTRAINT `partner_cost_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `partner` (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8'
		);

		$this->execute('insert into partner_cost (service_id, cost) values (1, 400)');
		$this->execute('insert into partner_cost (service_id, cost) values (2, 250)');
		$this->execute('insert into partner_cost (service_id, cost) values (3, 100)');
	}

	/**
	 * Откат
	 */
	public function down()
	{
		$this->execute('drop table partner_cost;');
	}
}
