<?php

class m141105_120706_dd_449_partner_request_kind extends CDbMigration
{
	public function up()
	{
		$this->execute('ALTER TABLE partner ADD request_kind int DEFAULT 0 NOT NULL;');
	}

	public function down()
	{
		$this->execute('ALTER TABLE partner DROP COLUMN request_kind;');
	}
}
