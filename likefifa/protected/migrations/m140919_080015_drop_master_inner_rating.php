<?php

/**
 * Удаляет внутренний рейтинг мастеров
 *
 * Class m140919_080015_drop_master_inner_rating
 */
class m140919_080015_drop_master_inner_rating extends CDbMigration
{
	public function up()
	{
		$this->dropColumn('lf_master', 'rating_inner');
	}

	public function down()
	{
		$this->addColumn('lf_master', 'rating_inner', 'FLOAT NULL DEFAULT NULL AFTER `rating`');
		$this->execute("ALTER TABLE `lf_master` ADD INDEX `master_rating` (rating DESC, rating_inner ASC);");
	}
}