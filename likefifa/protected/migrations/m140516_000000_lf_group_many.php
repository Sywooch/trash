<?php

/**
 * m140516_000000_lf_group_many class file.
 *
 * Во множественном числе
 *
 * @author   Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see      https://docdoc.megaplan.ru/task/1002975/card/
 * @package  migrations
 */
class m140516_000000_lf_group_many extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->addColumn("lf_group", "many", "VARCHAR(128) NOT NULL");

		$this->update("lf_group", array("many" => "визажисты"), "id = :id", array(":id" => 1));
		$this->update("lf_group", array("many" => "парикмахеры"), "id = :id", array(":id" => 2));
		$this->update("lf_group", array("many" => "мастера ногтевого сервиса"), "id = :id", array(":id" => 3));
		$this->update("lf_group", array("many" => "массажисты"), "id = :id", array(":id" => 4));
		$this->update("lf_group", array("many" => "косметологи"), "id = :id", array(":id" => 5));
		$this->update("lf_group", array("many" => "мастера по пирсингу"), "id = :id", array(":id" => 6));
		$this->update(
			"lf_group",
			array("many" => "мастера по татуировкам"),
			"id = :id",
			array(
				":id" => $this->_getTattooId()
			)
		);
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropColumn("lf_group", "many");
	}

	/**
	 * Получает идентификатор тату
	 *
	 * @return int
	 */
	private function _getTattooId()
	{
		$row = Yii::app()->db->createCommand()
			->select('id')
			->from('lf_group')
			->order('id DESC')
			->queryRow();
		if ($row) {
			return $row["id"];
		}

		return 7;
	}
}