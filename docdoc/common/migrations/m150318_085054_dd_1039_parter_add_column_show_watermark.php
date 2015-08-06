<?php

class m150318_085054_dd_1039_parter_add_column_show_watermark extends CDbMigration
{
	public function up()
	{
		$this->execute('ALTER TABLE partner ADD show_watermark BOOLEAN DEFAULT true NOT NULL;');
	}

	public function down()
	{
		$this->dropColumn('partner', 'show_watermark');
	}
}