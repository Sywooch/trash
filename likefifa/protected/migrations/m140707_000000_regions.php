<?php

/**
 * Файл класса m140707_000000_regions.
 *
 * Создает таблицы с регионами и городами
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @link    https://docdoc.megaplan.ru/task/1003365/card/
 * @package migrations
 */
class m140707_000000_regions extends CDbMigration
{

	/**
	 * Применяет миграцию в трансакции
	 *
	 * @return bool
	 */
	public function safeUp()
	{
		$this->_createRegionTable();
		$this->_createCityTable();
		$this->_deleteMoscowArea();
		$this->_applyNewAdminControllers();
	}

	/**
	 * Откатывает миграцию в трансакции
	 *
	 * @return bool
	 */
	public function safeDown()
	{
		$this->_createMoscowArea();
		$this->_deleteCityTable();
		$this->_deleteRegionTable();
		$this->_rollbackNewAdminControllers();
	}

	/**
	 * Создает таблицу с регионами
	 * Записывает в нее 2 региона (Москва и МО)
	 *
	 * @return void
	 */
	private function _createRegionTable()
	{
		$this->createTable(
			"regions",
			array(
				"id"                 => "pk",
				"prefix"             => "VARCHAR(8) NOT NULL",
				"name"               => "VARCHAR(32) NOT NULL",
				"name_genitive"      => "VARCHAR(32) NOT NULL",
				"name_prepositional" => "VARCHAR(32) NOT NULL",
				"is_active"          => "INT NOT NULL",
			),
			"ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
		);

		$this->createIndex("region_is_active", "regions", "is_active");

		$this->insert(
			"regions",
			array(
				"id"                 => 1,
				"prefix"             => "",
				"name"               => "Москва",
				"name_genitive"      => "Москвы",
				"name_prepositional" => "Москве",
				"is_active"          => 1,
			)
		);
		$this->insert(
			"regions",
			array(
				"id"                 => 2,
				"prefix"             => "mo.",
				"name"               => "Московская область",
				"name_genitive"      => "Московской области",
				"name_prepositional" => "Московской области",
				"is_active"          => 1,
			)
		);
	}

	/**
	 * Удаляет таблицу с регионами
	 *
	 * @return void
	 */
	private function _deleteRegionTable()
	{
		$this->dropIndex("region_is_active", "regions");
		$this->dropTable("regions");
	}

	/**
	 * Создает таблицу с городами
	 * Заполняет ее городами
	 * Удаляет старую таблицу
	 * Удаляет старые внешние ключи
	 *
	 * @return void
	 */
	private function _createCityTable()
	{
		$this->createTable(
			"cities",
			array(
				"id"                 => "pk",
				"region_id"          => "INT NOT NULL",
				"rewrite_name"       => "VARCHAR(32) NOT NULL",
				"name"               => "VARCHAR(32) NOT NULL",
				"name_genitive"      => "VARCHAR(32) NOT NULL",
				"name_prepositional" => "VARCHAR(32) NOT NULL",
				"is_active"          => "INT NOT NULL",
			),
			"ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
		);

		$this->createIndex("city_is_active", "cities", "is_active");
		$this->addForeignKey("city_region_id", "cities", "region_id", "regions", "id");

		$cities = Yii::app()->db->createCommand()
			->select('*')
			->from('moscow_area')
			->queryAll();
		foreach ($cities as $city) {
			$regionId = 1;
			if ($city["id"] > 1) {
				$regionId = 2;
			}
			$this->insert(
				"cities",
				array(
					"id"                 => $city["id"],
					"region_id"          => $regionId,
					"rewrite_name"       => $city["rewrite_name"],
					"name"               => $city["name"],
					"name_genitive"      => $city["genitive"],
					"name_prepositional" => $city["prepositional"],
					"is_active"          => $city["is_big"],
				)
			);
		}

		$this->dropForeignKey("master_city", "lf_master");
		$this->dropForeignKey("salon_city", "lf_salons");
	}

	/**
	 * Удаляет таблицу с городами
	 * Возобновляет старую таблицу
	 * Возобновляет старые внешние ключи
	 *
	 * @return void
	 */
	private function _deleteCityTable()
	{
		$this->addForeignKey("master_city", "lf_master", "city_id", "moscow_area", "id");
		$this->addForeignKey("salon_city", "lf_salons", "city_id", "moscow_area", "id");

		$this->dropIndex("city_is_active", "cities");
		$this->dropForeignKey("city_region_id", "cities");
		$this->dropTable("cities");
	}

	/**
	 * Удаляет старую таблицу с городами
	 *
	 * @return void
	 */
	private function _deleteMoscowArea()
	{
		$this->dropIndex("moscow_area_is_big", "moscow_area");
		$this->dropTable("moscow_area");
	}

	/**
	 * Создает старую таблицу с городами
	 *
	 * @return void
	 */
	private function _createMoscowArea()
	{
		$this->execute(
			"
			DROP TABLE IF EXISTS `moscow_area`;
			CREATE TABLE `moscow_area` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(512) NOT NULL,
			  `rewrite_name` varchar(512) DEFAULT NULL,
			  `is_big` int(11) NOT NULL,
			  `is_new` int(11) NOT NULL,
			  `genitive` varchar(64) DEFAULT NULL,
			  `prepositional` varchar(64) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `moscow_area_is_big` (`is_big`)
			) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;

			INSERT INTO `moscow_area` VALUES ('1', 'Москва', 'moscow', '0', '0', 'Москвы', 'Москве');
			INSERT INTO `moscow_area` VALUES ('2', 'Балашиха', 'balashiha', '1', '0', 'Балашихи', 'Балашихе');
			INSERT INTO `moscow_area` VALUES ('3', 'Видное', 'vidnoe', '0', '0', 'Видного', 'Видном');
			INSERT INTO `moscow_area` VALUES ('4', 'Апрелевка', 'aprelevka', '0', '0', 'Апрелевки', 'Апрелевке');
			INSERT INTO `moscow_area` VALUES ('5', 'Серпухов', 'serpuhov', '1', '0', 'Серпухова', 'Серпухове');
			INSERT INTO `moscow_area` VALUES ('7', 'Долгопрудный', 'dolgoprudnij', '0', '0', 'Долгопрудного', 'Долгопрудном');
			INSERT INTO `moscow_area` VALUES ('8', 'Домодедово', 'domodedovo', '1', '0', 'Домодедова', 'Домодедове');
			INSERT INTO `moscow_area` VALUES ('9', 'Железнодорожный', 'zheleznodorozhnij', '1', '0', 'Железнодорожного', 'Железнодорожном');
			INSERT INTO `moscow_area` VALUES ('10', 'Жуковский', 'zhukovskij', '0', '0', 'Жуковского', 'Жуковском');
			INSERT INTO `moscow_area` VALUES ('11', 'Зеленоград', 'zelenograd', '0', '0', 'Зеленограда', 'Зеленограде');
			INSERT INTO `moscow_area` VALUES ('12', 'Ивантеевка', 'ivanteevka', '0', '0', 'Ивантеевки', 'Ивантеевке');
			INSERT INTO `moscow_area` VALUES ('13', 'Климовск', 'klimovsk', '0', '0', 'Климовска', 'Климовске');
			INSERT INTO `moscow_area` VALUES ('14', 'Королев', 'korolev', '1', '0', 'Королева', 'Королеве');
			INSERT INTO `moscow_area` VALUES ('15', 'Котельники', 'kotelniki', '0', '0', 'Котельники', 'Котельники');
			INSERT INTO `moscow_area` VALUES ('16', 'Красногорск', 'krasnogorsk', '0', '0', 'Красногорска', 'Красногорске');
			INSERT INTO `moscow_area` VALUES ('18', 'Лобня', 'lobnya', '0', '0', 'Лобни', 'Лобне');
			INSERT INTO `moscow_area` VALUES ('20', 'Лыткарино', 'litkarino', '0', '0', 'Лыткарина', 'Лыткарине');
			INSERT INTO `moscow_area` VALUES ('21', 'Люберцы', 'ljubertsi', '1', '0', 'Люберец', 'Люберцах');
			INSERT INTO `moscow_area` VALUES ('22', 'Московский', 'moskovskij', '0', '0', 'Московского', 'Московском');
			INSERT INTO `moscow_area` VALUES ('23', 'Мытищи', 'mitishi', '1', '0', 'Мытищ', 'Мытищах');
			INSERT INTO `moscow_area` VALUES ('24', 'Ногинск', 'noginsk', '1', '0', 'Ногинска', 'Ногинске');
			INSERT INTO `moscow_area` VALUES ('25', 'Одинцово', 'odintsovo', '1', '0', 'Одинцова', 'Одинцове');
			INSERT INTO `moscow_area` VALUES ('26', 'Подольск', 'podolsk', '1', '0', 'Подольска', 'Подольске');
			INSERT INTO `moscow_area` VALUES ('27', 'Пушкино', 'pushkino', '1', '0', 'Пушкина', 'Пушкине');
			INSERT INTO `moscow_area` VALUES ('28', 'Раменское', 'ramenskoe', '1', '0', 'Раменского', 'Раменском');
			INSERT INTO `moscow_area` VALUES ('29', 'Реутов', 'reutov', '0', '0', 'Реутова', 'Реутове');
			INSERT INTO `moscow_area` VALUES ('30', 'Троицк', 'troitsk', '0', '0', 'Троицка', 'Троицке');
			INSERT INTO `moscow_area` VALUES ('31', 'Фрязино', 'fryazino', '0', '0', 'Фрязина', 'Фрязине');
			INSERT INTO `moscow_area` VALUES ('32', 'Фрязево', 'fryazevo', '0', '0', 'Фрязева', 'Фрязеве');
			INSERT INTO `moscow_area` VALUES ('33', 'Химки', 'himki', '1', '0', 'Химок', 'Химках');
			INSERT INTO `moscow_area` VALUES ('34', 'Щелково', 'shelkovo', '1', '0', 'Щелкова', 'Щелкове');
			INSERT INTO `moscow_area` VALUES ('35', 'Щербинка', 'sherbinka', '0', '0', 'Щербинки', 'Щербинке');
			INSERT INTO `moscow_area` VALUES ('36', 'Электросталь', 'elektrostal', '1', '0', 'Электростали', 'Электростали');
			INSERT INTO `moscow_area` VALUES ('37', 'Электроугли', 'elektrougli', '0', '0', 'Электроуглей', 'Электроуглях');
			INSERT INTO `moscow_area` VALUES ('38', 'Юбилейный', 'jubilejnij', '0', '0', 'Юбилейного', 'Юбилейном');
			INSERT INTO `moscow_area` VALUES ('39', 'Воскресенск', 'voskresensk', '1', '1', 'Воскресенска', 'Воскресенске');
			INSERT INTO `moscow_area` VALUES ('40', 'Дмитров', 'dmitrov', '1', '1', 'Дмитрова', 'Дмитрове');
			INSERT INTO `moscow_area` VALUES ('41', 'Клин', 'klin', '1', '1', 'Клина', 'Клине');
			INSERT INTO `moscow_area` VALUES ('42', 'Коломна', 'kolomna', '1', '1', 'Коломны', 'Коломне');
			INSERT INTO `moscow_area` VALUES ('43', 'Наро-Фоминск', 'naro-fominsk', '1', '1', 'Наро-Фоминска', 'Наро-Фоминске');
			INSERT INTO `moscow_area` VALUES ('44', 'Орехово-Зуево', 'orehovo-zuevo', '1', '1', 'Орехово-Зуево', 'Орехово-Зуево');
			INSERT INTO `moscow_area` VALUES ('45', 'Сергиев Посад', 'sergiev-posad', '1', '1', 'Сергиева Посада', 'Сергиевом Посаде');
			INSERT INTO `moscow_area` VALUES ('46', 'Солнечногорск', 'solnechnogorsk', '1', '1', 'Солнечногорска', 'Солнечногорске');
			INSERT INTO `moscow_area` VALUES ('47', 'Чехов', 'chehov', '1', '1', 'Чехова', 'Чехове');
			"
		);
	}

	/**
	 * Создает новые контроллеры для БО
	 *
	 * @return void
	 */
	private function _applyNewAdminControllers()
	{
		$this->update(
			"admin_controller",
			array(
				"rewrite_name" => "city",
			),
			"id = :id",
			array(":id" => 3)
		);
		$this->insert(
			"admin_controller",
			array(
				"name"         => "Регионы",
				"rewrite_name" => "region",
			)
		);
	}

	/**
	 * Откатывает до старых контроллеров для БО
	 *
	 * @return void
	 */
	private function _rollbackNewAdminControllers()
	{
		$this->update(
			"admin_controller",
			array(
				"rewrite_name" => "moscowArea",
			),
			"id = :id",
			array(":id" => 3)
		);
		$this->delete(
			"admin_controller",
			"rewrite_name = :rewrite_name",
			array(":rewrite_name" => "region")
		);
	}
}