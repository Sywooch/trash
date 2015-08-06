<?php

/**
 * m140731_000001_lf_work_likes class file.
 *
 * Флаги для вывода работ на главную страницу
 *
 * @author   Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see      https://docdoc.megaplan.ru/task/1003635/card/
 * @package  migrations
 */
class m140731_000001_lf_work_likes extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->addColumn("lf_work", "not_main", "INT NOT NULL");
		$this->createIndex("lf_work_not_main", "lf_work", "not_main");

		$this->addColumn("lf_work", "sort", "INT NOT NULL");
		$this->createIndex("lf_work_sort", "lf_work", "sort");

		$this->update("lf_work", array("sort" => 180), "id = :id", array(":id" => 4606));
		$this->update("lf_work", array("sort" => 170), "id = :id", array(":id" => 7990));
		$this->update("lf_work", array("sort" => 160), "id = :id", array(":id" => 5422));
		$this->update("lf_work", array("sort" => 150), "id = :id", array(":id" => 16128));
		$this->update("lf_work", array("sort" => 140), "id = :id", array(":id" => 4867));
		$this->update("lf_work", array("sort" => 130), "id = :id", array(":id" => 5916));
		$this->update("lf_work", array("sort" => 120), "id = :id", array(":id" => 5375));
		$this->update("lf_work", array("sort" => 110), "id = :id", array(":id" => 5423));
		$this->update("lf_work", array("sort" => 100), "id = :id", array(":id" => 3444));
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropIndex("lf_work_not_main", "lf_work");
		$this->dropColumn("lf_work", "not_main");

		$this->dropIndex("lf_work_sort", "lf_work");
		$this->dropColumn("lf_work", "sort");
	}
}