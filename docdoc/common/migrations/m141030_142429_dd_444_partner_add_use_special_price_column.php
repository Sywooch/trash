<?php

class m141030_142429_dd_444_partner_add_use_special_price_column extends CDbMigration
{
	public function up()
	{
		$this->execute('ALTER TABLE partner ADD use_special_price TINYINT(1) DEFAULT false NOT NULL;');
	}

	public function down()
	{
		$this->execute('ALTER TABLE partner DROP COLUMN use_special_price');
	}
}
