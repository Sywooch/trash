<?php

/**
 * Class m140702_121403_add_new_metro_stations
 * Задача #4064
 * Добавляет новые станции метро в БД
 */
class m140702_121403_add_new_metro_stations extends CDbMigration
{
	protected $data = [
		[
			'name'                => 'Лермонтовский проспект',
			'underground_line_id' => 9,
			'index'               => 20,
			'rewrite_name'        => 'lermontovskiy_prospekt'
		],
		[
			'name'                => 'Жулебино',
			'underground_line_id' => 9,
			'index'               => 21,
			'rewrite_name'        => 'zhulebino'
		],
		[
			'name'                => 'Лесопарковая',
			'underground_line_id' => 12,
			'index'               => 6,
			'rewrite_name'        => 'lesoparkovaya'
		],
		[
			'name'                => 'Битцевский парк',
			'underground_line_id' => 12,
			'index'               => 7,
			'rewrite_name'        => 'bitsevskiy_park'
		],
		[
			'name'                => 'Деловой центр',
			'underground_line_id' => 3,
			'index'               => 10,
			'rewrite_name'        => 'delovoy_centr'
		],
		[
			'name'                => 'Парк победы',
			'underground_line_id' => 3,
			'index'               => 9,
			'rewrite_name'        => 'park_pobedi'
		],
	];

	public function up()
	{
		foreach ($this->data as $data) {
			Yii::app()->db->createCommand()->insert('underground_station', $data);
		}
	}

	public function down()
	{
		foreach ($this->data as $data) {
			UndergroundStation::model()->deleteAllByAttributes($data);
		}
	}
}