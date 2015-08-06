<?php

class m140618_135305_city_add_column_search_type extends CDbMigration
{
	/**
	 * Добавляется колонка search_type для таблицы city
	 * И проставляется значение для москвы и питера
	 * @return bool|void
	 */
	public function up()
	{
		$this->addColumn("city", "search_type", "INT NOT NULL DEFAULT 1");
		$this->execute("update `city` set `search_type` = 3 where `id_city` = 1");
		$this->execute("update `city` set `search_type` = 2 where `id_city` = 2");
	}

	/**
	 * Удаляется колонка seatch_type из таблицы city
	 * @return bool|void
	 */
	public function down()
	{
		$this->dropColumn("city", "search_type");
	}

}
