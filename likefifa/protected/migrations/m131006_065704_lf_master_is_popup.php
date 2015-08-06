<?php

/**
 * Задача https://docdoc.megaplan.ru/task/1002071/card/
 * Поле необходимо для вывода (или нет) на экран сообщения в ЛК о подарке (2000 руб.)
 */
class m131006_065704_lf_master_is_popup extends CDbMigration
{

	public function up()
	{
		$this->addColumn('lf_master', 'is_popup', 'int');
	}

	public function down()
	{
		$this->dropColumn('lf_master', 'is_popup');
	}

}