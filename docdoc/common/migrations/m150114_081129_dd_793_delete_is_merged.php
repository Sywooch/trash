<?php

class m150114_081129_dd_793_delete_is_merged extends CDbMigration
{
	public function up()
	{
		$this->execute('ALTER TABLE api_clinic DROP is_merged;');
		$this->execute('ALTER TABLE api_doctor DROP is_merged;');
	}

	public function down()
	{
		$this->execute('alter table api_clinic add COLUMN is_merged tinyint(1) not null default 0;');
		$this->execute('alter table api_doctor add COLUMN is_merged tinyint(1) not null default 0;');
	}
}
