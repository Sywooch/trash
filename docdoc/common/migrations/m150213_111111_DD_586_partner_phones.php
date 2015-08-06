<?php

use dfs\docdoc\models\PartnerModel;

/**
 * Телефоны для партнеров
 *
 * @package migrations
 */
class m150213_111111_DD_586_partner_phones extends CDbMigration
{

	/**
	 * @return void
	 */
	public function up()
	{
		$this->createTable(
			"partner_phones",
			array(
				"partner_id" => "INT NOT NULL",
				"city_id"    => "INT NOT NULL",
				"phone_id"   => "INT NOT NULL",
			),
			"ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
		);

		$this->addPrimaryKey("partner_phones_pk", "partner_phones", "partner_id, city_id, phone_id");

		$this->addForeignKey("partner_phones_partner_id", "partner_phones", "partner_id", "partner", "id");
		$this->addForeignKey("partner_phones_city_id", "partner_phones", "city_id", "city", "id_city");
		$this->addForeignKey("partner_phones_phone_id", "partner_phones", "phone_id", "phone", "id");

		$criteria = new CDbCriteria();
		$criteria->condition = "t.phone_id IS NOT NULL";
		foreach (PartnerModel::model()->findAll($criteria) as $model) {
			$this->insert(
				"partner_phones",
				[
					"partner_id" => $model->id,
					"city_id"    => $model->city_id,
					"phone_id"   => $model->phone_id
				]
			);
		}
	}

	/**
	 * @return void
	 */
	public function down()
	{
		$this->dropTable("partner_phones");
	}
}