<?php

class m140912_081145_dd_237_rating_and_rating_strategy extends CDbMigration
{
	public function safeUp()
	{
		$this->execute(
			"CREATE TABLE `rating_strategy` (
				`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`name` VARCHAR(20) NOT NULL,
				`chance` INT(11) NOT NULL DEFAULT '0',
				`params` VARCHAR(255) NOT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `id` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$this->execute(
			"CREATE TABLE `rating` (
				`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				`object_id` INT(11) NOT NULL,
				`object_type` INT(11) NOT NULL,
				`strategy_id` BIGINT(20) UNSIGNED NOT NULL,
				`rating_value` REAL NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				UNIQUE KEY `id` (`id`),
				UNIQUE KEY `object_id_object_type_strategy_id_index` (`object_id`,`object_type`,`strategy_id`),
				KEY `rating_ibfk_1` (`strategy_id`),
				KEY `rating_value_index` (`rating_value`),
				CONSTRAINT `rating_ibfk_1` FOREIGN KEY (`strategy_id`) REFERENCES `rating_strategy` (`id`) ON DELETE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);

		$this->execute("INSERT INTO rating_strategy (id, name, chance) VALUES (1, 'default', 50)");
		$this->execute("INSERT INTO rating_strategy (id, name, chance) VALUES (2, 'multiply', 100)");
	}

	public function safeDown()
	{
		$this->dropTable('rating');
		$this->dropTable('rating_strategy');
	}
}
