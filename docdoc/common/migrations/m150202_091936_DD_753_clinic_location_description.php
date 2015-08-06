<?php

/**
 * Добавление новых полей, тексты как добраться пешком и на машине
 */
class m150202_091936_DD_753_clinic_location_description extends CDbMigration
{
	public function up()
	{
		$this->addColumn('clinic', 'way_on_foot', 'TEXT DEFAULT NULL');
		$this->addColumn('clinic', 'way_on_car', 'TEXT DEFAULT NULL');

		$this->truncateTable('img_clinic');
	}

	public function down()
	{
		$this->dropColumn('clinic', 'way_on_foot');
		$this->dropColumn('clinic', 'way_on_car');
	}
}
