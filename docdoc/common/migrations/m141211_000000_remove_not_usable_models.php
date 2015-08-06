<?php

/**
 * Class m141211_000000_remove_not_usable_models
 *
 * Убераем лишние таблици
 */
class m141211_000000_remove_not_usable_models
	extends CDbMigration
{
	/**
	 * @return bool
	 */
	public function up()
	{
		$this->dropTable('promo_text');
		$this->dropTable('promo_text_zone');
		$this->dropTable('promo_zone');

		return true;
	}

	/**
	 * @return bool
	 */
	public function down()
	{
		$sql =
<<<SQL
	--
	-- Table structure for table `promo_text`
	--
	CREATE TABLE `promo_text` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`disabled` int(1) NOT NULL,
		`name` varchar(512) NOT NULL,
		`text` text NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='промо-блоки';

	--
	-- Table structure for table `promo_text_zone`
	--
	CREATE TABLE `promo_text_zone` (
		`promo_zone_id` int(11) NOT NULL,
		`promo_text_id` int(11) NOT NULL,
		PRIMARY KEY (`promo_zone_id`,`promo_text_id`),
		KEY `promo_text_zone_ibfk_2` (`promo_text_id`),
		CONSTRAINT `promo_text_zone_ibfk_1` FOREIGN KEY (`promo_zone_id`) REFERENCES `promo_zone` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
		CONSTRAINT `promo_text_zone_ibfk_2` FOREIGN KEY (`promo_text_id`) REFERENCES `promo_text` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='связи между промо-блоками и промо-зонами';

	--
	-- Table structure for table `promo_zone`
	--
	CREATE TABLE `promo_zone` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`code` varchar(16) NOT NULL,
		`disabled` int(1) NOT NULL,
		`name` varchar(512) NOT NULL,
		PRIMARY KEY (`id`),
		UNIQUE KEY `code` (`code`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='промо-зоны';
SQL;

		$this->execute($sql);

		return true;
	}
}