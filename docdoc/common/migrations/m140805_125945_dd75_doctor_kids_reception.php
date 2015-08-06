<?php

/**
 * Файл класса m140805_125945_dd75_doctor_kids_reception.
 */
class m140805_125945_dd75_doctor_kids_reception extends CDbMigration
{
	/**
	 * Применяет миграцию
	 *
	 * @return void
	 */
	public function up()
	{
		$this->addColumn('doctor', 'kids_reception', 'int(1) NOT NULL DEFAULT 0');
		$this->addColumn('doctor', 'kids_age_from', 'int(4)');
		$this->addColumn('doctor', 'kids_age_to', 'int(4)');

		$this->createIndex('kids_reception', 'doctor', 'kids_reception');
	}

	/**
	 * Откатывает миграцию
	 *
	 * @return void
	 */
	public function down()
	{
		$this->dropIndex('kids_reception', 'doctor');

		$this->dropColumn('doctor', 'kids_reception');
		$this->dropColumn('doctor', 'kids_age_from');
		$this->dropColumn('doctor', 'kids_age_to');
	}
}