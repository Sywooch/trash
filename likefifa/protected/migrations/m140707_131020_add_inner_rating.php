<?php

class m140707_131020_add_inner_rating extends CDbMigration
{
	public function up()
	{
		$this->addColumn('lf_master', 'rating_inner', 'FLOAT NULL DEFAULT NULL AFTER `rating`');
		$this->execute("ALTER TABLE `lf_master` ADD INDEX `master_rating` (rating DESC, rating_inner ASC);");
	}

	public function down()
	{
		$this->dropIndex('master_rating', 'lf_master');
		$this->dropColumn('lf_master', 'rating_inner');
	}
}