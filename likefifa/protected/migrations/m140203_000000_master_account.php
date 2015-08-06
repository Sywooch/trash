<?php

/**
 * m140203_000000_master_account class file.
 *
 * Создает аккаунты для мастеров, если нет аккаунта
 *
 * @author   Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see      https://docdoc.megaplan.ru/task/1003070/card/
 * @package  migrations
 */
class m140203_000000_master_account extends CDbMigration
{

	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$masters = LfMaster::model()->findAll();
		if ($masters) {
			foreach ($masters as $model) {
				if (!$model->account_id) {
					$model->getAccount();
				}
			}
		}
	}

}