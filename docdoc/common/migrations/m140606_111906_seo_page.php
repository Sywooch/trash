<?php

class m140606_111906_seo_page extends CDbMigration
{
	/**
	 * создание таблицы с SEO данными для страниц
	 */
	public function safeUp()
	{
		$this->execute("CREATE TABLE `page` (
			`id` INT NOT NULL AUTO_INCREMENT,
			`url` VARCHAR(1024) NOT NULL  COMMENT 'Url страницы',
			`h1` VARCHAR(1024) NOT NULL  COMMENT 'Основной заголовок страницы',
			`title` VARCHAR(1024) NOT NULL  COMMENT 'title страницы',
			`keywords` TEXT NULL  COMMENT 'meta-keywords для страницы',
			`description` TEXT NULL  COMMENT 'meta-description для страницы',
			`seo_text_top` TEXT NULL  COMMENT 'верхний seo текст',
			`seo_text_bottom` TEXT NULL  COMMENT 'нижний seo текст',
			`is_show` TINYINT(1) NULL DEFAULT 1  COMMENT 'Флаг показывать/не показывать',
			`id_city` INT(10) NULL DEFAULT 0  COMMENT 'ID города',
			`site` TINYINT(3) NULL DEFAULT 1  COMMENT 'Сайт 1 - docdoc, 2 - diagnostica',
			PRIMARY KEY (`id`),
			INDEX `search_idx` (`site` ASC, `is_show` ASC, `url`(100) ASC)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='SEO описание страниц'");

		$this->execute("ALTER TABLE `city`
			ADD COLUMN `title_genitive` VARCHAR(50) NULL AFTER `prefix`,
			ADD COLUMN `title_prepositional` VARCHAR(50) NULL AFTER `title_genitive`,
			ADD COLUMN `has_diagnostic` TINYINT(1) NULL DEFAULT 0 AFTER `title_prepositional`
		");


		$this->execute("UPDATE `city` SET
			`title_genitive` = 'Москвы',
			`title_prepositional` = 'Москве',
			`has_diagnostic` = 1
			WHERE id_city=1
		");

		$this->execute("UPDATE `city` SET
			`title_genitive` = 'Санкт-Петербурга',
			`title_prepositional` = 'Санкт-Петербурге',
			`has_diagnostic` = 1
			WHERE id_city=2
		");
	}

	public function safeDown()
	{
		$this->execute("DROP TABLE IF EXISTS `page`");

		$this->execute("ALTER TABLE `city`
			DROP COLUMN `has_diagnostic`,
			DROP COLUMN `title_prepositional`,
			DROP COLUMN `title_genitive`");
	}
}