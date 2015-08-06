<?php

/**
 * Находит фейковые отзывы
 *
 * Class m140919_072502_fake_opinions
 */
class m140919_072502_fake_opinions extends CDbMigration
{
	public function up()
	{
		$opinions = Yii::app()->db->createCommand(
			'SELECT master_id, count(*), t.sid FROM lf_opinion t
			WHERE t.master_id IS NOT NULL AND t.sid IS NOT NULL
			GROUP BY t.master_id, t.sid
			HAVING count(*) > 2'
		)->queryAll();
		foreach ($opinions as $opinion) {
			Yii::app()->db->createCommand()->update(
				'lf_opinion',
				['warning_level' => 2],
				'master_id = :master_id and sid = :sid',
				[
					':master_id' => $opinion['master_id'],
					':sid'       => $opinion['sid']
				]
			);
		}
	}

	public function down()
	{
		echo "m140919_072502_fake_opinions does not support migration down.\n";
		return false;
	}
}