<?php

/**
 * m140722_000000_admin_transactions class file.
 *
 * Добавляет контроллер в БО для просмотра транзакций
 *
 * @author   Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see      https://docdoc.megaplan.ru/task/1003707/card/
 * @package  migrations
 */
class m140722_000000_admin_transactions extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->insert("admin_controller", array("name" => "Транзакции", "rewrite_name" => "transactions"));
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$row = Yii::app()->db->createCommand()
			->select('id')
			->from('admin_controller')
			->order('id DESC')
			->queryRow();
		if ($row) {
			$this->delete(
				"admin_controller",
				"id = :id",
				array(":id" => $row["id"])
			);
		}
	}
}