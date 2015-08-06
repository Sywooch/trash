<?php

use dfs\docdoc\models\TipsModel;

/**
 * m141020_091807_DD_385_tips_fix
 * таблицы для подсказок
 */
class m141020_091807_DD_385_tips_fix extends CDbMigration
{
	// name, category, weight, color
	protected $_tips = [
		[ 'RequestsInfo', TipsModel::CATEGORY_REQUEST, 10, null ],
		[ 'PositiveOpinion', TipsModel::CATEGORY_OPINION, 10, null ],
		[ 'OpinionInfo', TipsModel::CATEGORY_OPINION, 10, null ],
	];

	/**
	 * @return bool|void
	 */
	public function up()
	{
		$this->truncateTable('tips_message');
		$this->delete('tips');

		$this->addColumn('tips_message', 'values', 'varchar(500) NULL');

		foreach ($this->_tips as $item) {
			$this->insert('tips', [
					'name'     => $item[0],
					'category' => $item[1],
					'weight'   => $item[2],
					'color'    => $item[3],
				]);
		}

		$this->update('doctor', [ 'update_tips' => 1 ]);
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->dropColumn('tips_message', 'values');
	}
}