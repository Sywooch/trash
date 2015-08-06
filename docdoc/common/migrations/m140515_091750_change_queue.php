<?php

class m140515_091750_change_queue extends CDbMigration
{

	/**
	 * добавление статуса в очереди и изменение типа поля SIP
	 */
	public function safeUp()
	{
		$sips = array(
			101 => 'callcenter',
			102 => 'callcenter',
			103 => 'callcenter',
			104 => 'callcenter',
			105 => 'callcenter',
			106 => 'callcenter',
			107 => 'callcenter',
			108 => 'callcenter',
			109 => 'callcenter',
			110 => 'callcenter',
			111 => 'callcenter',
			112 => 'callcenter',
			113 => 'callcenter',
			114 => 'callcenter',
			115 => 'callcenter',
			118 => 'callcenter',
			119 => 'callcenter',
			201 => 'testq',
			202 => 'testq',
			203 => 'testq',
			204 => 'testq',
			205 => 'testq',
		);

		$this->execute("ALTER TABLE `queue`
				ADD COLUMN `status` TINYINT(1) DEFAULT 0 AFTER `asteriskPool`,
				MODIFY COLUMN `SIP` INT(11),
				MODIFY COLUMN `user_id` INT(11) NULL DEFAULT NULL,
				ADD INDEX `user_id` (user_id)
				");

		$command = \Yii::app()
			->db
			->createCommand()
			->select("SIP, user_id")
			->from("queue");
		$items = $command->queryAll();
		foreach ($items as $item) {
			$this->execute("UPDATE queue SET status = 1 WHERE SIP = {$item['SIP']}");
			unset($sips[$item['SIP']]);
		}

		foreach ($sips as $sip => $name) {
			$this->execute("INSERT INTO `queue` VALUES ({$sip}, '0000-00-00 00:00:00', null, '{$name}', 0)");
		}

	}

	public function safeDown()
	{
		$this->execute("DELETE FROM `queue` WHERE status = 0");
		$this->execute("ALTER TABLE `queue` DROP COLUMN `status`");
		$this->execute("ALTER TABLE `queue` MODIFY COLUMN `SIP` VARCHAR(4)");
		$this->execute("ALTER TABLE `queue` DROP INDEX `user_id`");
	}

}