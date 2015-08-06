<?php

/**
 * @package migrations
 */
class m150309_000000_DD_1013_clinic_billing extends CDbMigration
{
	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function safeUp()
	{
		$this->execute(
			"CREATE TABLE `clinic_billing` (
				`id` INT(10) NOT NULL AUTO_INCREMENT,
				`clinic_id` INT(11) NOT NULL,
				`billing_date` DATE NOT NULL  COMMENT 'Дата отчетного периода',
				`status` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'статус получения денег',
				`clinic_contract_id` INT(11) NOT NULL COMMENT 'контракт',
				`start_sum` INT(11) NOT NULL COMMENT 'сумма биллинга на 1 число периода',
				`start_requests` INT(11) NOT NULL COMMENT 'заявок в биллинге на 1 число периода',
				`agreed_sum` INT(11) NULL COMMENT 'Согласованная сумма',
				`agreed_requests` INT(11) NULL  COMMENT 'Согласовано заявок',
				`recieved_sum` INT(11) NULL DEFAULT 0 COMMENT 'Полученная сумма',
				`changedata_date` DATETIME NULL  COMMENT 'Дата обновления информации по согласованным заявкам',
				PRIMARY KEY (`id`),
				INDEX `billing_date_idx` (`billing_date` ASC))
				ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Биллинг клиник';"
		);
	}

	/**
	 * Применяет down
	 *
	 * @return void
	 */
	public function safeDown()
	{
		$this->dropTable("clinic_billing");
	}
}