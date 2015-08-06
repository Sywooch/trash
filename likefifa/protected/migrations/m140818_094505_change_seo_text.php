<?php

/**
 * Добавляет колонку page для сео текстов
 *
 * Class m140818_094505_change_seo_text
 */
class m140818_094505_change_seo_text extends CDbMigration
{
	public function up()
	{
		$this->addColumn('lf_seo_text', 'page', 'MEDIUMINT NULL DEFAULT NULL');
	}

	public function down()
	{
		$this->dropColumn('lf_seo_text', 'page');
	}
}