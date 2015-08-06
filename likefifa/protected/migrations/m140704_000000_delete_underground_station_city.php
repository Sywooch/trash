<?php

/**
 * Файл класса m140704_000000_delete_underground_station_city.
 *
 * Удаляет старые связи с городами и метро
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003365/card/
 * @package migrations
 */
class m140704_000000_delete_underground_station_city extends CDbMigration
{

	/**
	 * Применяет миграцию в трансакции
	 *
	 * @return bool
	 */
	public function safeUp()
	{
		$this->dropForeignKey("city_station", "underground_station_city");
		$this->dropForeignKey("station_city", "underground_station_city");
		$this->dropTable("underground_station_city");
	}

	/**
	 * Откатывает миграцию в трансакции
	 *
	 * @return bool
	 */
	public function safeDown()
	{
		$this->execute(
			"
			DROP TABLE IF EXISTS `underground_station_city`;
			CREATE TABLE `underground_station_city` (
			  `underground_station_id` int(11) NOT NULL,
			  `city_id` int(11) NOT NULL,
			  PRIMARY KEY (`underground_station_id`,`city_id`),
			  KEY `city_station` (`city_id`),
			  CONSTRAINT `city_station` FOREIGN KEY (`city_id`) REFERENCES `moscow_area` (`id`)
			  	ON DELETE CASCADE ON UPDATE CASCADE,
			  CONSTRAINT `station_city` FOREIGN KEY (`underground_station_id`) REFERENCES `underground_station` (`id`)
			  	ON DELETE CASCADE ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			INSERT INTO `underground_station_city` VALUES ('71', '2');
			INSERT INTO `underground_station_city` VALUES ('72', '2');
			INSERT INTO `underground_station_city` VALUES ('90', '2');
			INSERT INTO `underground_station_city` VALUES ('176', '2');
			INSERT INTO `underground_station_city` VALUES ('41', '3');
			INSERT INTO `underground_station_city` VALUES ('46', '3');
			INSERT INTO `underground_station_city` VALUES ('101', '3');
			INSERT INTO `underground_station_city` VALUES ('102', '3');
			INSERT INTO `underground_station_city` VALUES ('50', '4');
			INSERT INTO `underground_station_city` VALUES ('51', '4');
			INSERT INTO `underground_station_city` VALUES ('52', '4');
			INSERT INTO `underground_station_city` VALUES ('179', '4');
			INSERT INTO `underground_station_city` VALUES ('71', '5');
			INSERT INTO `underground_station_city` VALUES ('72', '5');
			INSERT INTO `underground_station_city` VALUES ('180', '5');
			INSERT INTO `underground_station_city` VALUES ('6', '7');
			INSERT INTO `underground_station_city` VALUES ('124', '7');
			INSERT INTO `underground_station_city` VALUES ('128', '7');
			INSERT INTO `underground_station_city` VALUES ('143', '7');
			INSERT INTO `underground_station_city` VALUES ('150', '7');
			INSERT INTO `underground_station_city` VALUES ('41', '8');
			INSERT INTO `underground_station_city` VALUES ('101', '8');
			INSERT INTO `underground_station_city` VALUES ('102', '8');
			INSERT INTO `underground_station_city` VALUES ('37', '9');
			INSERT INTO `underground_station_city` VALUES ('71', '9');
			INSERT INTO `underground_station_city` VALUES ('72', '9');
			INSERT INTO `underground_station_city` VALUES ('90', '9');
			INSERT INTO `underground_station_city` VALUES ('105', '9');
			INSERT INTO `underground_station_city` VALUES ('176', '9');
			INSERT INTO `underground_station_city` VALUES ('37', '10');
			INSERT INTO `underground_station_city` VALUES ('68', '10');
			INSERT INTO `underground_station_city` VALUES ('57', '11');
			INSERT INTO `underground_station_city` VALUES ('58', '11');
			INSERT INTO `underground_station_city` VALUES ('124', '11');
			INSERT INTO `underground_station_city` VALUES ('156', '11');
			INSERT INTO `underground_station_city` VALUES ('27', '12');
			INSERT INTO `underground_station_city` VALUES ('57', '12');
			INSERT INTO `underground_station_city` VALUES ('58', '12');
			INSERT INTO `underground_station_city` VALUES ('71', '13');
			INSERT INTO `underground_station_city` VALUES ('72', '13');
			INSERT INTO `underground_station_city` VALUES ('180', '13');
			INSERT INTO `underground_station_city` VALUES ('27', '14');
			INSERT INTO `underground_station_city` VALUES ('57', '14');
			INSERT INTO `underground_station_city` VALUES ('58', '14');
			INSERT INTO `underground_station_city` VALUES ('81', '14');
			INSERT INTO `underground_station_city` VALUES ('33', '15');
			INSERT INTO `underground_station_city` VALUES ('68', '15');
			INSERT INTO `underground_station_city` VALUES ('76', '15');
			INSERT INTO `underground_station_city` VALUES ('127', '15');
			INSERT INTO `underground_station_city` VALUES ('71', '16');
			INSERT INTO `underground_station_city` VALUES ('72', '16');
			INSERT INTO `underground_station_city` VALUES ('84', '16');
			INSERT INTO `underground_station_city` VALUES ('143', '16');
			INSERT INTO `underground_station_city` VALUES ('156', '16');
			INSERT INTO `underground_station_city` VALUES ('6', '18');
			INSERT INTO `underground_station_city` VALUES ('111', '18');
			INSERT INTO `underground_station_city` VALUES ('124', '18');
			INSERT INTO `underground_station_city` VALUES ('150', '18');
			INSERT INTO `underground_station_city` VALUES ('37', '20');
			INSERT INTO `underground_station_city` VALUES ('68', '20');
			INSERT INTO `underground_station_city` VALUES ('76', '20');
			INSERT INTO `underground_station_city` VALUES ('37', '21');
			INSERT INTO `underground_station_city` VALUES ('71', '21');
			INSERT INTO `underground_station_city` VALUES ('72', '21');
			INSERT INTO `underground_station_city` VALUES ('149', '22');
			INSERT INTO `underground_station_city` VALUES ('179', '22');
			INSERT INTO `underground_station_city` VALUES ('181', '22');
			INSERT INTO `underground_station_city` VALUES ('27', '23');
			INSERT INTO `underground_station_city` VALUES ('57', '23');
			INSERT INTO `underground_station_city` VALUES ('58', '23');
			INSERT INTO `underground_station_city` VALUES ('81', '23');
			INSERT INTO `underground_station_city` VALUES ('71', '24');
			INSERT INTO `underground_station_city` VALUES ('72', '24');
			INSERT INTO `underground_station_city` VALUES ('105', '24');
			INSERT INTO `underground_station_city` VALUES ('16', '25');
			INSERT INTO `underground_station_city` VALUES ('17', '25');
			INSERT INTO `underground_station_city` VALUES ('50', '25');
			INSERT INTO `underground_station_city` VALUES ('51', '25');
			INSERT INTO `underground_station_city` VALUES ('52', '25');
			INSERT INTO `underground_station_city` VALUES ('85', '25');
			INSERT INTO `underground_station_city` VALUES ('103', '25');
			INSERT INTO `underground_station_city` VALUES ('179', '25');
			INSERT INTO `underground_station_city` VALUES ('71', '26');
			INSERT INTO `underground_station_city` VALUES ('72', '26');
			INSERT INTO `underground_station_city` VALUES ('180', '26');
			INSERT INTO `underground_station_city` VALUES ('27', '27');
			INSERT INTO `underground_station_city` VALUES ('57', '27');
			INSERT INTO `underground_station_city` VALUES ('58', '27');
			INSERT INTO `underground_station_city` VALUES ('37', '28');
			INSERT INTO `underground_station_city` VALUES ('71', '29');
			INSERT INTO `underground_station_city` VALUES ('72', '29');
			INSERT INTO `underground_station_city` VALUES ('90', '29');
			INSERT INTO `underground_station_city` VALUES ('188', '29');
			INSERT INTO `underground_station_city` VALUES ('149', '30');
			INSERT INTO `underground_station_city` VALUES ('57', '31');
			INSERT INTO `underground_station_city` VALUES ('58', '31');
			INSERT INTO `underground_station_city` VALUES ('176', '31');
			INSERT INTO `underground_station_city` VALUES ('57', '32');
			INSERT INTO `underground_station_city` VALUES ('58', '32');
			INSERT INTO `underground_station_city` VALUES ('71', '32');
			INSERT INTO `underground_station_city` VALUES ('72', '32');
			INSERT INTO `underground_station_city` VALUES ('57', '33');
			INSERT INTO `underground_station_city` VALUES ('58', '33');
			INSERT INTO `underground_station_city` VALUES ('111', '33');
			INSERT INTO `underground_station_city` VALUES ('124', '33');
			INSERT INTO `underground_station_city` VALUES ('57', '34');
			INSERT INTO `underground_station_city` VALUES ('58', '34');
			INSERT INTO `underground_station_city` VALUES ('176', '34');
			INSERT INTO `underground_station_city` VALUES ('24', '35');
			INSERT INTO `underground_station_city` VALUES ('71', '35');
			INSERT INTO `underground_station_city` VALUES ('72', '35');
			INSERT INTO `underground_station_city` VALUES ('180', '35');
			INSERT INTO `underground_station_city` VALUES ('71', '36');
			INSERT INTO `underground_station_city` VALUES ('72', '36');
			INSERT INTO `underground_station_city` VALUES ('71', '37');
			INSERT INTO `underground_station_city` VALUES ('72', '37');
			INSERT INTO `underground_station_city` VALUES ('27', '38');
			INSERT INTO `underground_station_city` VALUES ('57', '38');
			INSERT INTO `underground_station_city` VALUES ('58', '38');
			INSERT INTO `underground_station_city` VALUES ('81', '38');
		"
		);
	}
}