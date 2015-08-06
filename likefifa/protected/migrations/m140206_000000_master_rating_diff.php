<?php

/**
 * m140206_000000_master_rating_diff class file.
 *
 * Разница в рейтинге мастера, выставляемая оператором
 *
 * @author   Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see      https://docdoc.megaplan.ru/task/1002979/card/
 * @package  migrations
 */
class m140206_000000_master_rating_diff extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->addColumn("lf_master", "rating_diff", "FLOAT NOT NULL");
	}

	/**
	 * Откатывает миграцию миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropColumn('lf_master', 'rating_diff');
	}
}