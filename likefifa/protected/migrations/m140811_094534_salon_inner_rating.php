<?php

/**
 * Внутренний рейтинг у салонов
 * Class m140811_094534_salon_inner_rating
 */
class m140811_094534_salon_inner_rating extends CDbMigration
{
	public function up()
	{
		$this->addColumn('lf_salons', 'rating_inner', 'INT NULL DEFAULT NULL');

		$this->execute("ALTER TABLE `lf_salons` ADD INDEX `salon_rating` (rating DESC, rating_inner ASC);");

		$this->addColumn('lf_salons', 'rating_composite', 'FLOAT(11, 7) NOT NULL DEFAULT 0 AFTER `rating_inner`');

		$this->createIndex('salon_rating_inner', 'lf_salons', 'rating_inner', true);
		$this->createIndex('salon_rating_composite', 'lf_salons', 'rating_composite');

		$this->execute("update lf_salons set rating_composite = IF(rating_inner IS NOT NULL, 1/rating_inner + rating/999999, rating/999999)");
	}

	public function down()
	{
		$this->dropIndex('salon_rating_inner', 'lf_salons');
		$this->dropIndex('salon_rating_composite', 'lf_salons');
		$this->dropColumn('lf_salons', 'rating_composite');

		$this->alterColumn('lf_salons', 'rating_inner', 'FLOAT NULL DEFAULT NULL ');

		$this->dropIndex('salon_rating', 'lf_salons');
		$this->dropColumn('lf_salons', 'rating_inner');
	}
}