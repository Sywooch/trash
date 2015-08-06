<?php

/**
 * Class m150121_082936_DD_815_rename_seller_role
 */
class m150121_082936_DD_815_rename_seller_role extends CDbMigration
{
	/**
	 * Переименовал название роли
	 *
	 * @return bool|void
	 */
	public function up()
	{
		$this->update('user_right_dict', ['title' => 'Продавец'], 'right_id = 6');
	}

	/**
	 * @return bool
	 */
	public function down()
	{
		$this->update('user_right_dict', ['title' => 'Барыга'], 'right_id = 6');
	}
}