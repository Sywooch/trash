<?php

class m140709_083325_create_table_clinic_partner_cost extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute(
			'CREATE TABLE `clinic_partner_phone` (
			  `clinic_id` int(11) NOT NULL,
			  `partner_id` int(11) NOT NULL,
			  `phone_id` int(11) NOT NULL,
			  PRIMARY KEY (`clinic_id`,`partner_id`),
			  KEY `clinic_id` (`clinic_id`),
			  KEY `partner_id` (`partner_id`),
			  KEY `phone_id` (`phone_id`),
			  CONSTRAINT `clinic_partner_phone_ibfk_1` FOREIGN KEY (`clinic_id`) REFERENCES `clinic` (`id`),
			  CONSTRAINT `clinic_partner_phone_ibfk_2` FOREIGN KEY (`partner_id`) REFERENCES `partner` (`id`),
			  CONSTRAINT `clinic_partner_phone_ibfk_3` FOREIGN KEY (`phone_id`) REFERENCES `phone` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
		);
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->execute('drop table `clinic_partner_phone`;');
	}
}
