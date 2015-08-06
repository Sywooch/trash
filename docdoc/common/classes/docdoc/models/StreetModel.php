<?php

namespace dfs\docdoc\models;

use CDbCriteria;
use dfs\docdoc\extensions\TextUtils;

/**
 * Файл класса StreetModel
 *
 * Модель для работы с улицами
 *
 * @package dfs.docdoc.models
 *
 * @property int        $street_id     ID
 * @property int        $city_id       Город
 * @property string     $title         Название улицы
 * @property string     $rewrite_name  Название на латинице
 * @property float      $bound_left    Левая граница
 * @property float      $bound_right   Правая граница
 * @property float      $bound_top     Верхняя граница
 * @property float      $bound_bottom  Нижняя граница
 * @property int        $type          Тип
 * @property string     $search_title  Название для поиска
 *
 * @property CityModel  $city          модель города
 */
class StreetModel extends \CActiveRecord
{
	/**
	 * радиус (км) для поиска клиник рядом с улицей
	 */
	const DISTANCE_EXTENDED_BOUND = 5;

	const TYPE_STREET = 1;
	const TYPE_AVENUE = 2;
	const TYPE_ALLEY = 3;
	const TYPE_BOULEVARD = 4;
	const TYPE_EMBANKMENT = 5;
	const TYPE_LANE = 6;
	const TYPE_PASSAGE = 7;
	const TYPE_HIGHWAY = 8;
	const TYPE_SQUARE = 9;
	const TYPE_DEADLOCK = 10;
	const TYPE_GLADE = 11;

	/**
	 * @var array
	 */
	static private $_types = [
		'проспект' => self::TYPE_AVENUE,
		'пр' => self::TYPE_AVENUE,
		'аллея' => self::TYPE_ALLEY,
		'бульвар' => self::TYPE_BOULEVARD,
		'б р' => self::TYPE_BOULEVARD,
		'набережная' => self::TYPE_EMBANKMENT,
		'наб' => self::TYPE_EMBANKMENT,
		'переулок' => self::TYPE_LANE,
		'пер' => self::TYPE_LANE,
		'проезд' => self::TYPE_PASSAGE,
		'шоссе' => self::TYPE_HIGHWAY,
		'ш' => self::TYPE_HIGHWAY,
		'площадь' => self::TYPE_SQUARE,
		'пл' => self::TYPE_SQUARE,
		'тупик' => self::TYPE_DEADLOCK,
		'просека' => self::TYPE_GLADE,
		'просек' => self::TYPE_GLADE,
		'улица' => self::TYPE_STREET,
		'улицы' => self::TYPE_STREET,
		'ул' => self::TYPE_STREET,
	];

	/**
	 * @var array
	 */
	static private $_clearWords = [ '-й', '-я' ];

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return StreetModel the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Возвращает имя связанной таблицы базы данных
	 *
	 * @return string
	 */
	public function tableName()
	{
		return 'street_dict';
	}

	/**
	 * Возвращает правила проверки для атрибутов модели
	 *
	 * @return string[]
	 */
	public function rules()
	{
		return array(
			array('city_id', 'required'),
			array('title, rewrite_name', 'required'),
			array('title, rewrite_name', 'length', 'max' => 100),
			array('title, rewrite_name', 'filter', 'filter' => 'strip_tags'),
		);
	}

	/**
	 * Возвращает связи между объектами
	 *
	 * @return string[]
	 */
	public function relations()
	{
		return array(
			'city' => array(
				self::BELONGS_TO,
				'dfs\docdoc\models\CityModel',
				'city_id'
			),
		);
	}

	/**
	 * Возвращает подписей полей
	 *
	 * @return string[]
	 */
	public function attributeLabels()
	{
		return array(
			'street_id'     => 'ID',
			'city_id'       => 'Город',
			'title'         => 'Название улицы',
		);
	}

	/**
	 * Получить полное название улицы (со словом "улица")
	 *
	 * @return string
	 */
	public function getFullTitle()
	{
		return $this->title . ($this->type == self::TYPE_STREET ? ' улица' : '');
	}

	/**
	 * Поиск по городу
	 *
	 * @param integer $city
	 *
	 * @return $this
	 */
	public function inCity($city)
	{
		$this->getDbCriteria()->mergeWith(array(
				'condition' => 't.city_id = :city',
				'params'    => array(':city' => $city),
			));

		return $this;
	}

	/**
	 * Поиск по rewrite_name
	 *
	 * @param string $alias
	 *
	 * @return $this
	 */
	public function searchByAlias($alias)
	{
		$this->getDbCriteria()->mergeWith(array(
				'condition' => 't.rewrite_name = :rewrite_name',
				'params'    => array(':rewrite_name' => $alias),
			));

		return $this;
	}

	/**
	 * Поиск по названию улицы, используя вспомогательные поля type и search_title
	 *
	 * @param string $title
	 *
	 * @return $this
	 */
	public function searchTitle($title)
	{
		$info = self::getSearchInfo($title);

		$this->getDbCriteria()->mergeWith([
				'condition' => '(t.search_title = :search_title AND t.type = :type) OR t.rewrite_name = :rewrite_name',
				'params'    => [
					':type' => $info['type'],
					':search_title' => $info['search_title'],
					':rewrite_name' => TextUtils::rewriteName(self::normalizeTitle($title)),
				],
			]);

		return $this;
	}

	/**
	 * Фильтр улиц имеющие клиники
	 *
	 * @param int | null $specialityId
	 *
	 * @return $this
	 */
	public function hasClinics($specialityId = null)
	{
		$criteria = new CDbCriteria();
		$criteria->join = 'INNER JOIN clinic as c ON (c.street_id = t.street_id AND c.status = 3 AND c.isClinic = "yes" AND c.isPrivatDoctor = "no")';
		$criteria->join .= ' INNER JOIN doctor_4_clinic d4c ON (d4c.clinic_id = c.id and d4c.type = ' . DoctorClinicModel::TYPE_DOCTOR . ')';

		if ($specialityId !== null) {
			$criteria->join .= ' INNER JOIN doctor_sector t3 ON (t3.doctor_id = d4c.doctor_id AND sector_id = :sector_id)';
			$criteria->params = [
				':sector_id' => $specialityId,
			];
		}

		$criteria->group = 't.street_id';

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Установить rewrite_name на основе title
	 *
	 * @return bool
	 */
	public function updateRewriteName()
	{
		$this->rewrite_name = TextUtils::rewriteName($this->title);

		return true;
	}

	/**
	 * Определить координаты границ для улицы
	 *
	 * @return bool
	 */
	public function updateBound()
	{
		$bound = \Yii::app()->yandexGeoApi->getStreetGeoCoordinates($this->city->title, $this->getFullTitle());

		if ($bound === null) {
			return false;
		}
		$this->bound_left = $bound['left'];
		$this->bound_right = $bound['right'];
		$this->bound_bottom = $bound['bottom'];
		$this->bound_top = $bound['top'];

		return true;
	}

	/**
	 * Создание новой улицы и установка необходимых данных
	 *
	 * @param integer $cityId
	 * @param string $title
	 *
	 * @return StreetModel
	 */
	static public function newStreet($cityId, $title)
	{
		$street = new StreetModel();

		$info = self::getSearchInfo($title);

		$street->city_id = $cityId;
		$street->title = self::normalizeTitle($title);
		$street->type = $info['type'];
		$street->search_title = $info['search_title'];
		$street->updateRewriteName();
		$street->updateBound();

		return $street;
	}

	/**
	 * Параметры для поиска улицы
	 *
	 * @param string $title
	 *
	 * @return array
	 */
	static public function getSearchInfo($title)
	{
		$type = null;
		$text = ' ' . TextUtils::reductionName($title, ' ', self::$_clearWords) . ' ';

		foreach (self::$_types as $pattern => $t) {
			if (preg_match('/ +' . $pattern . ' +/', $text, $matches)) {
				$text = str_replace($matches[0], '', $text);
				$type = $t;
				break;
			}
		}

		return [
			'type' => $type,
			'search_title' => str_replace(' ', '', $text),
		];
	}

	/**
	 * Нормализация названия улицы
	 *
	 * @param string $title
	 *
	 * @return string
	 */
	static public function normalizeTitle($title)
	{
		$text = ' ' . $title . ' ';

		foreach ([ 'улица ', 'улицы ', 'ул ', 'ул.' ] as $pattern) {
			$text = preg_replace('/ +' . $pattern . ' */ui', ' ', $text);
		}

		return trim($text, ' ,');
	}
}
