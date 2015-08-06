<?php

use dfs\docdoc\models\PartnerModel;

/**
 * Удаляет старый телефон для партнеров
 *
 * @package migrations
 */
class m150213_222222_DD_586_delete_partner_phone extends CDbMigration
{

	/**
	 * @return void
	 */
	public function up()
	{
		$this->dropColumn("partner", "phone_id");
	}

	/**
	 * @return void
	 */
	public function down()
	{
		$this->addColumn("partner", "phone_id", "INT");
		$partners = PartnerModel::model()->with(["phones"])->findAll();
		foreach ($partners as $partner) {
			if ($partner->phones) {
				$partner->phone_id = $partner->phones[0]->phone_id;
				$partner->save();
			}
		}
	}
}