<?php

/**
 * Class m140715_064910_add_rating_composite
 * Добавляет поля для хранения сборного рейтинга
 */
class m140715_064910_add_rating_composite extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('lf_master', 'rating_inner', 'INT NULL DEFAULT NULL');
		$this->addColumn('lf_master', 'rating_composite', 'FLOAT(11, 7) NOT NULL DEFAULT 0 AFTER `rating_inner`');

		$this->createIndex('master_rating_inner', 'lf_master', 'rating_inner', true);
		$this->createIndex('master_rating_composite', 'lf_master', 'rating_composite');

		$this->execute("update lf_master set rating_composite = IF(rating_inner IS NOT NULL, 1/rating_inner + rating/999999, rating/999999)");
	}

	public function down()
	{
		$this->dropIndex('master_rating_inner', 'lf_master');
		$this->dropIndex('master_rating_composite', 'lf_master');
		$this->dropColumn('lf_master', 'rating_composite');

		$this->alterColumn('lf_master', 'rating_inner', 'FLOAT NULL DEFAULT NULL ');
	}
}