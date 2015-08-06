<?php

use dfs\docdoc\models\TipsModel;

/**
 * m140929_090220_DD_267_tips
 * таблицы для подсказок
 */
class m140929_090220_DD_267_tips extends CDbMigration
{
	// name, category, weight, color, text
	protected $_tips = [
		[ 'LastRecordWas', TipsModel::CATEGORY_REQUEST, 10, null, 'Последняя запись была {date}' ],
		[ 'TodayRecNum', TipsModel::CATEGORY_REQUEST, 10, null, 'Сегодня записалось {count_patients}' ],
		[ 'MonthRecNum', TipsModel::CATEGORY_REQUEST, 10, null, 'В этом месяце было {count_records}' ],
		[ 'TotalRecNum', TipsModel::CATEGORY_REQUEST, 10, null, 'Было {count_records} к этому врачу' ],
		[ 'LastOpinion', TipsModel::CATEGORY_OPINION, 10, null, 'Последний отзыв получен {date}' ],
		[ 'TotalOpinionNum', TipsModel::CATEGORY_OPINION, 10, null, 'Получено {count_reviews}' ],
		[ 'TheBest', TipsModel::CATEGORY_THE_BEST, 10, null, 'Входит в топ-10' ],
	];

	/**
	 * @return bool|void
	 */
	public function up()
	{
		$this->createTable(
			'tips',
			[
				'id' => 'pk',
				'name' => 'varchar(50) NOT NULL',
				'category' => 'tinyint unsigned NOT NULL',
				'weight' => 'int NOT NULL',
				'color' => 'char(7) NULL',
			],
			'ENGINE=InnoDB DEFAULT CHARSET=utf8'
		);
		$this->createIndex('name_key', 'tips', 'name', true);

		$this->createTable(
			'tips_message',
			[
				'tips_id' => 'int NOT NULL',
				'record_id' => 'int NOT NULL',
				'weight' => 'tinyint NOT NULL',
				'message' => 'varchar(250) NOT NULL',
			],
			'ENGINE=InnoDB DEFAULT CHARSET=utf8'
		);
		$this->addPrimaryKey('pk', 'tips_message', 'record_id, tips_id');
		$this->addForeignKey('tips_id_key', 'tips_message', 'tips_id', 'tips', 'id', 'CASCADE');

		foreach ($this->_tips as $item) {
			$this->insert('tips', [
					'name'     => $item[0],
					'category' => $item[1],
					'weight'   => $item[2],
					'color'    => $item[3],
				]);
		}

		$this->addColumn('doctor', 'update_tips', 'tinyint(1) unsigned NOT NULL DEFAULT 1');
		$this->createIndex('update_tips_key', 'doctor', 'update_tips');
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->dropIndex('update_tips_key', 'doctor');
		$this->dropColumn('doctor', 'update_tips');
		$this->dropTable('tips_message');
		$this->dropTable('tips');
	}
}