<?php

use dfs\docdoc\models\TipsModel;

/**
 * Изменения подсказок
 */
class m141107_114137_DD_500_tips_changes extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function up()
	{
		$this->delete('tips', 'name = "PositiveOpinion"');

		$this->update('doctor', [ 'update_tips' => 1 ]);
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$this->insert('tips', [
			'name'     => 'PositiveOpinion',
			'category' => TipsModel::CATEGORY_OPINION,
			'weight'   => 10,
			'color'    => null,
		]);
	}
}
