<?php

use dfs\docdoc\models\PhoneModel;

/**
 * Class m150127_135324_DD_847_add_partner_new_design
 */
class m150127_135324_DD_847_add_partner_new_design extends CDbMigration
{
	/**
	 * Добавление нового партнера
	 *
	 * @return bool|void
	 */
	public function up()
	{
		$phone = PhoneModel::model()->byNumber('74951254417')->find();

		if (is_null($phone)) {
			$this->insert('phone', [
				'id' => 500,
				'number' => 74951254417
			]);
		}

		$this->insert('partner', [
			'id' => 428,
			'name' => 'docdoc.design2',
			'login' => 'docdoc.design2',
			'contact_email' => 'design2@docdoc.ru',
			'phone_id' => !is_null($phone) ? $phone->id : 500,
			'city_id' => 1,
		]);
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->delete('partner', 'id = 428');
		$this->delete('phone', 'number = 74951254417');
	}

}