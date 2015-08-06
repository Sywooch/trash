<?php

/**
 * Class m140825_082610_comagic_numa_nullable_fix
 */
class m140825_082610_comagic_numa_nullable_fix extends CDbMigration
{
	/**
	 * @return bool|void
	 */
	public function up()
	{
		$this->execute("ALTER TABLE comagic_log MODIFY COLUMN numa VARCHAR(20) NULL;");
		$this->execute("UPDATE comagic_log set numa = null where numa='' or numa like 'undefined%'");
	}

	/**
	 * @return bool|void
	 */
	public function down()
	{
		$command = $this->getDbConnection()->createCommand("select * from comagic_log where numa is null");

		$i = 0;

		foreach($command->queryAll() as $row){
			$numa = "undefined-" . $i++;
			$this->execute("update comagic_log set numa='$numa' where id={$row['id']}");
		}

		$this->execute("ALTER TABLE comagic_log MODIFY COLUMN numa VARCHAR(20) NOT NULL;");
	}
}
