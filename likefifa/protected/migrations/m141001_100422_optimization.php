<?php

/**
 * Class m141001_100422_optimization
 */
class m141001_100422_optimization extends CDbMigration
{
	public function up()
	{
		$this->createIndex('lf_opinion_allowed', 'lf_opinion', 'allowed');
	}

	public function down()
	{
		$this->dropIndex('lf_opinion_allowed', 'lf_opinion');
	}
}