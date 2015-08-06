<?php

/**
 * Class m140915_085822_DD_227_optimize_index
 * добавление индекса на clinic.id_city
 */
class m140915_085822_DD_227_optimize_index extends CDbMigration
{
	/**
	 * @return bool
	 */
	public function up()
	{
		$this->createIndex("city_idx", "clinic", "city_id");
		return true;
	}

	/**
	 * @return bool
	 */
	public function down()
	{
		$this->dropIndex("city_idx", "clinic");

		return true;
	}
}