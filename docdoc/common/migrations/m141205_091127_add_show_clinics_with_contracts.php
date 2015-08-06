<?php

/**
 * Class m141205_091127_add_show_clinics_with_contracts
 */
class m141205_091127_add_show_clinics_with_contracts extends CDbMigration
{
	/**
	 * Добавляем поле - выводить клиники только с договорами
	 *
	 * @return bool|void
	 */
	public function up()
	{
		$this->addColumn('partner', 'show_clinics_with_contracts', 'TINYINT(1) DEFAULT 0 NOT NULL');
	}

	/**
	 * @return bool
	 */
	public function down()
	{
		$this->dropColumn('partner', 'show_clinics_with_contracts');
	}
}