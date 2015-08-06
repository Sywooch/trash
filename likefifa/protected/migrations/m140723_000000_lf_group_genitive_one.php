<?php

/**
 * m140723_000000_lf_group_genitive_one class file.
 *
 * Родительный падеж для специализации в единственном числе
 *
 * @author   Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see      https://docdoc.megaplan.ru/task/1003600/card/
 * @package  migrations
 */
class m140723_000000_lf_group_genitive_one extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->addColumn("lf_group", "genitive_one", "VARCHAR(128) NOT NULL");

		$this->update("lf_group", array("genitive_one" => "визажиста"), "id = :id", array(":id" => 1));
		$this->update("lf_group", array("genitive_one" => "парикмахера"), "id = :id", array(":id" => 2));
		$this->update("lf_group", array("genitive_one" => "мастера ногтевого сервиса"), "id = :id", array(":id" => 3));
		$this->update("lf_group", array("genitive_one" => "массажиста"), "id = :id", array(":id" => 4));
		$this->update("lf_group", array("genitive_one" => "косметолога"), "id = :id", array(":id" => 5));
		$this->update("lf_group", array("genitive_one" => "мастера по пирсингу"), "id = :id", array(":id" => 6));
		$this->update("lf_group", array("genitive_one" => "мастера по татуировкам"), "id = :id", array(
				":id" => $this->_getTattooId()
			));
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropColumn("lf_group", "genitive_one");
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