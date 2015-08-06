<?php

use dfs\docdoc\models\ServiceModel;


/**
 * Деление партнёрской цены по городам
 */
class m141031_082716_DD_398_partner_cost_by_city extends CDbMigration
{
	// partner_id, service_id, city_id, cost
	protected $_prices = [
		[ null, ServiceModel::TYPE_SUCCESSFUL_DOCTOR_REQUEST, 2, 250 ],
	];


	public function up()
	{
		$this->addColumn('partner_cost', 'city_id', 'int unsigned NULL');

		$this->dropIndex('partner_cost_unique', 'partner_cost');
		$this->createIndex('partner_cost_unique', 'partner_cost', 'partner_id, service_id, city_id', true);

		foreach ($this->_prices as $item) {
			$this->insert('partner_cost', [
					'partner_id' => $item[0],
					'service_id' => $item[1],
					'city_id'    => $item[2],
					'cost'       => $item[3],
				]);
		}
	}

	public function down()
	{
		$this->delete('partner_cost', 'city_id > 0');

		$this->dropIndex('partner_cost_unique', 'partner_cost');
		$this->createIndex('partner_cost_unique', 'partner_cost', 'partner_id, service_id', true);

		$this->dropColumn('partner_cost', 'city_id');
	}
}
