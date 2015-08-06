<?php

/**
 * Файл класса m140710_073933_request_enter_point.
 *
 * Добавление в таблицу request поля для хранения точки входа
 */
class m140710_073933_request_enter_point extends CDbMigration
{
	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function safeUp()
	{
		//добавляем столбец
		$this->execute("ALTER TABLE `request` ADD COLUMN `enter_point` varchar(20) NULL COMMENT 'Точка входа, в которой была создана заявка'");
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function safeDown()
	{
		$this->execute(
			"ALTER TABLE `request`
						DROP COLUMN `enter_point`"
		);
	}
}