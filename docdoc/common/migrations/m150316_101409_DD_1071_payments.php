<?php

/**
 * создание таблицы для поступлений
 */
class m150316_101409_DD_1071_payments extends CDbMigration
{
	public function up()
	{
		$this->execute(
			"CREATE TABLE `clinic_payment` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`clinic_billing_id` INT(11) NOT NULL,
				`payment_date` DATE NOT NULL  COMMENT 'Дата перевода',
				`sum` float(10,2) NOT NULL DEFAULT 1 COMMENT 'сумма',
				PRIMARY KEY (`id`),
				INDEX `clinic_billing_idx` (`clinic_billing_id` ASC))
				ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Поступления';"
		);

		$this->alterColumn("clinic_billing", "recieved_sum", " float(10,2) NULL DEFAULT 0");
	}

	public function down()
	{
		$this->dropTable("clinic_payment");
	}
}
