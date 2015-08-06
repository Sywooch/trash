<?php
use dfs\docdoc\models\RequestModel;
/**
 * m140926_091603_DD_247_fix_is_hot
 *
 */
class m140926_091603_DD_247_fix_is_hot extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function up()
	{
		$updateIsHot = [
			RequestModel::STATUS_RECORD,
			RequestModel::STATUS_CAME,
			RequestModel::STATUS_REJECT,
			RequestModel::STATUS_REMOVED,
			RequestModel::STATUS_ACCEPT,
			RequestModel::STATUS_PROCESS
		];
		$this->execute("UPDATE request SET is_hot = 0
			WHERE is_hot=1 AND req_status IN (" .implode(",", $updateIsHot) . ")");
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{

	}
}