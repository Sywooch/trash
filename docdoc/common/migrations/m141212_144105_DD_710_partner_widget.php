<?php

/**
 * Class m141212_144105_DD_710_partner_widget
 */
class m141212_144105_DD_710_partner_widget extends CDbMigration
{
	/**
	 * Добавляем признак того, что договор с клиникой подписан
	 * Обновляем признак для существующих клиник
	 *
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute(
			"CREATE TABLE `partner_widget` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`partner_id` INT(11) NOT NULL,
				`widget` varchar(50) NOT NULL,
				`json_config` text DEFAULT NULL,
				`is_used` tinyint(1) DEFAULT 1,
				PRIMARY KEY (`id`),
				INDEX `partner_widget_idx` (`partner_id` ASC, `widget` ASC),
				KEY `partner_fk` (`partner_id`),
				CONSTRAINT `partner_fk` FOREIGN KEY (`partner_id`) REFERENCES `partner` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Настройки партнерских виджетов';"
		);
	}

	/**
	 * @return bool
	 */
	public function down()
	{
		$this->dropTable('partner_widget');
	}
}