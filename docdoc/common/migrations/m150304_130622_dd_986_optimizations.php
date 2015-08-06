<?php

class m150304_130622_dd_986_optimizations extends CDbMigration
{
	public function safeUp()
	{
		$this->execute('CREATE INDEX closest_station_id_index ON closest_station (closest_station_id);');
		$this->execute('CREATE INDEX chance_index ON rating_strategy (chance);');
		$this->execute('ALTER TABLE diagnostica4clinic ADD CONSTRAINT `fk_d4c_2_cl` FOREIGN KEY (clinic_id) REFERENCES clinic (id);');
	}

	public function safeDown()
	{
		$this->execute("ALTER TABLE closest_station DROP INDEX `closest_station_id_index`");
		$this->execute("ALTER TABLE rating_strategy DROP INDEX `chance_index`");
		$this->execute("ALTER TABLE `diagnostica4clinic` DROP FOREIGN KEY `fk_d4c_2_cl`");
	}
}