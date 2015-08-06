<?php

/**
 * Class m150225_133705_DD_959_request_stream
 */
class m150225_133705_DD_959_request_stream extends CDbMigration
{
	private $cityTimeZone = [
		'ekb' => 2,
		'nsk' => 3,
		'perm' => 2,
		'samara' => 1,
	];


	public function up()
	{
		$this->addColumn('city', 'time_zone', 'TINYINT NOT NULL DEFAULT "0"');

		$this->addColumn('user', 'operator_stream', 'TINYINT NOT NULL DEFAULT "0"');

		foreach ($this->cityTimeZone as $name => $time) {
			$this->update('city', ['time_zone' => $time], 'rewrite_name = "' . $name . '"');
		}
	}

	public function down()
	{
		$this->dropColumn('city', 'time_zone');

		$this->dropColumn('user', 'operator_stream');
	}
}
