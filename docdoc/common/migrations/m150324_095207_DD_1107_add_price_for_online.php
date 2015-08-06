<?php

/**
 * Class m150324_095207_DD_1107_add_price_for_online
 */
class m150324_095207_DD_1107_add_price_for_online extends CDbMigration
{
	/**
	 * Цена на запись онлайн для диагностики и включение цен в карточке клиники
	 *
	 * @return bool|void
	 */
	public function up()
	{
		$this->addColumn("diagnostica4clinic", "price_for_online", "float(9,2) DEFAULT '0.00'");
		$this->addColumn("clinic", "discount_online_diag", "tinyint(2) NOT NULL DEFAULT '0'");
	}

	public function down()
	{
		$this->dropColumn("diagnostica4clinic", "price_for_online");
		$this->dropColumn("clinic", "discount_online_diag");
	}

}