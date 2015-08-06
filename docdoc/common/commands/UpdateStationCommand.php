<?php

use dfs\common\components\console\Command;
use dfs\docdoc\models\CityModel;
use dfs\docdoc\models\StationModel;
use dfs\docdoc\models\UndergroundLineModel;

/**
 * Обновление данных станций метро
 *
 * Примеры команд:
 *    ./yiic updateStation geoCoordinates
 *    ./yiic updateStation closest --city=moscow
 *    ./yiic updateStation cianDataJs --city=spb
 *
 */
class UpdateStationCommand extends Command
{
	const URL_YANDEX_API_GEOCODE = 'http://geocode-maps.yandex.ru/1.x/';
	const URL_CIAN_METRO_DATA = 'http://www.cian.ru/nd/search/global/metro/metro.php?city_id=%s';
	const DIR_METRO_DATA_JS = '/front/public/js/metro/%s/data.js.json';

	/**
	 * Список городов в которых есть метро
	 *
	 * @var array
	 */
	static private $_cities = [
		'moscow' => [
			'title' => 'Москва',
			'max_priority' => 20,
			'closest_on_line' => 8,
		],
		'spb' => [
			'title' => 'Санкт-Петербург',
			'max_priority' => 6,
			'closest_on_line' => 5,
		],
		'ekb' => [
			'title' => 'Екатеринбург',
			'max_priority' => 6,
			'closest_on_line' => 0,
		],
		'nsk' => [
			'title' => 'Новосибирск',
			'max_priority' => 6,
			'closest_on_line' => 0,
		],
		'nn' => [
			'title' => 'Нижний Новгород',
			'max_priority' => 6,
			'closest_on_line' => 0,
		],
		'kazan' => [
			'title' => 'Казань',
			'max_priority' => 6,
			'closest_on_line' => 0,
		],
		'samara' => [
			'title' => 'Самара',
			'max_priority' => 6,
			'closest_on_line' => 0,
		],
	];

	/**
	 * @var array
	 */
	static private $_stationAliases = [
		// Москва
		'бульвар адмирала ушакова' => 'бульвар адм. ушакова',
		'бульвар дмитрия донского' => 'бульвар дм. донского',
		'преображенская площадь' => 'преображенская пл.',
		'улица старокачаловская' => 'ул. старокачаловская',
		'улица академика янгеля' => 'ул. академика янгеля',
		'пл. революции' => 'площадь революции',
		'волгоградский проспект' => 'волгоградский пр-т',
		// Питер
		'выборская' => 'выборгская',
		'комендантский проспект' => 'комендантский пр-т',
		'елизаровcкая' => 'елизаровская',
		'черная речка' => 'чёрная речка',
		'пл. александра невского – 1' => 'пл. александра невского-1',
		'Площадь Александра Невского 1' => 'пл. александра невского-1',
		'пл. александра невского – 2' => 'пл. александра невского-2',
		'Площадь Александра Невского 2' => 'пл. александра невского-2',
		'технологический институт – 2' => 'Технологический ин-т',
		'технологический институт – 1' => 'Технологический ин-т',
		'Технологический Институт' => 'Технологический ин-т',
		'Библиотека имени Ленина' => 'Библиотека им. Ленина',
		'Бухаресткая' => 'Бухарестская',
		// Казань
		'Площадь Габдуллы Тукая' => 'Площадь Тукая',
	];

	/**
	 * @var array
	 */
	static private $_lineAliases = [
		'Люблинско-Дмитровская' => 'Люблинская',
		'Фрунзенско-Приморская' => 'Фрунзенская',
		'Первая' => [
			'ekb' => 'Первая Екатеринбург',
			'samara' => 'Первая Самара',
		],
		'Центральная' => 'Центральная линия',
	];

	/**
	 * @var array
	 */
	static private $_excludeLines = [
		'Московская монорельсовая транспортная система',
		'Нижегородская канатная дорога',
	];

	/**
	 * Станции, которые не учитываются в поиске ближайших станции на одной линии (исключение для Калининской ветки)
	 *
	 * @var array
	 */
	static private $_excludeLineStations = [256, 257];

	/**
	 * @var StationModel[]
	 */
	private $_stations = [];
	/**
	 * @var array
	 */
	private $_stationsByName = [];
	/**
	 * @var array
	 */
	private $_lines = [];
	/**
	 * @var array
	 */
	private $_linesByName = [];
	/**
	 * @var array | null
	 */
	private $_lineStations = null;

	/**
	 * Обновление координат
	 */
	public function actionGeoCoordinates()
	{
		$this->log('---- Обновление координат -----');

		foreach (StationModel::model()->findAll() as $station) {
			if ($this->updateStationGeoCoordinates($station)) {
				$station->save();
				$this->log($station->id . ' - ' . $station->undergroundLine->name . ' - ' . $station->name . ' (' . $station->latitude . ' ' . $station->longitude . ')');
			}
		}
	}

	/**
	 * Определение ближайших станций, обновление талицы closest_station
	 *
	 * @param string $city
	 */
	public function actionClosest($city)
	{
		$cityId = $this->findCityId($city);
		if ($cityId === null) {
			return;
		}

		$this->log('---- Заливка ближайших станций -----');

		$maxPriority = self::$_cities[$city]['max_priority'];

		$this->loadStations($cityId);

		foreach ($this->_stations as $station) {
			echo '------- ' . $station->id . ' - ' . $station->name . ' (' . $station->undergroundLine->name . ')---------' . PHP_EOL;

			if ($station->latitude == 0 || $station->longitude == 0) {
				$this->log('Не установленны гео-координаты для станции ' . $station->id . ' "' . $station->name . '"');
				continue;
			}

			$closestStations = $this->findClosestStations($station, $city);

			if (count($closestStations) > 1)
			{
				$data = [];
				$lineIds = [];

				// Для самой станции ставим priority = 0
				$data[] = [ $station->id, $station->id, 0 ];
				$lineIds[$station->underground_line_id][] = $station->id;
				unset($closestStations[$station->id]);

				//Если определение ближайших станций ведется по линиям метро
				$priority = 1;
				$num = self::$_cities[$city]['closest_on_line'];
				if ($num > 0 && !$station->only_coord_search) {

					// Определяем станции переходы, priority = 1
					foreach ($closestStations as $id => $cs) {
						if ($cs['dist'] < 0.01 || $station->name === $cs['station']->name) {
							$data[] = [ $station->id, $id, $priority++ ];
							$lineIds[$cs['lineId']][] = $id;
							unset($closestStations[$id]);
						}
					}

					$stations = $this->findClosestLineStations($station, $lineIds, $num);
					foreach ($stations as $el) {
						if ($priority > $num) {
							break;
						}
						$id = $el['station']->id;
						if (!in_array($id, self::$_excludeLineStations)) {
							$data[] = [ $station->id, $id, $priority++ ];
						}
						unset($closestStations[$id]);
					}
				}

				foreach ($closestStations as $id => $cs) {
					if ($priority > $maxPriority) {
						break;
					}
					$data[] = [ $station->id, $id, $priority++ ];
				}

				$this->saveClosestStations($station, $data);
			}
		}

		// Добавление к ближайшим станциям, тех станций, которые являются станциями-переходами для уже найденых станций.
		// Например, если для Автозаводской была найдена ближайшая станция Новокузнецкая, то станция-переход Третьяковская тоже будет ближайшей с тем же приоритетом
		$sql = 'INSERT INTO closest_station (station_id, closest_station_id, priority)
			(
				SELECT cs1.station_id, cs2.closest_station_id, MIN(cs1.priority) as priority
				FROM closest_station AS cs1
					INNER JOIN closest_station AS cs2 ON (cs1.closest_station_id = cs2.station_id AND cs2.priority = 1)
					LEFT JOIN closest_station AS cs3 ON (cs3.station_id = cs1.station_id AND cs3.closest_station_id = cs2.closest_station_id)
				WHERE cs3.station_id IS NULL
				GROUP BY cs1.station_id, cs2.closest_station_id
			)';

		\Yii::app()->getDb()->createCommand($sql)->execute();
	}

	/**
	 * Переформирование файла data.js (маппинг id-ков станций)
	 *
	 * @param string $city
	 */
	public function actionCianDataJs($city)
	{
		$cityId = $this->findCityId($city);
		if ($cityId === null) {
			return;
		}

		$this->log('---- Обновление файла data.js -----');

		$content = json_decode(file_get_contents(sprintf(self::URL_CIAN_METRO_DATA, $city)), true);

		$this->loadStations($cityId);

		$stationIds = array();
		$branches = array();

		foreach ($content['branches'] as $item) {
			$line = $this->getLineByName($item['name'], $city, false);
			if ($line === null) {
				$line = $this->getLineByName($item['label_name'], $city, false);
			}

			if ($line === null) {
				$this->log('Не найдена линия метро ' . $item['id'] . ' "' . $item['name'] . '"');
			} else {
				$branches[$item['id']] = $line;
			}
		}

		$items = array();
		foreach ($content['stations'] as $item) {
			$branchId = reset($item['branch_ids']);
			$line = isset($branches[$branchId]) ? $branches[$branchId] : null;
			$station = $this->getStationByName($item['name'], $line, $city);
			if ($station !== null) {
				$stationIds[$item['id']] = $station->id;
				$item['id'] = $station->id;
				$items[] = $item;
			}
		}
		$content['stations'] = $items;

		if (isset($content['circle'])) {
			$content['circle'] = $this->replaceStationIds($content['circle'], $stationIds);
		}
		if (isset($content['inside'])) {
			$content['inside'] = $this->replaceStationIds($content['inside'], $stationIds);
		}

		$filename = sprintf(self::DIR_METRO_DATA_JS, $city);
		$result = file_put_contents(ROOT_PATH . $filename, json_encode($content, JSON_UNESCAPED_UNICODE));

		if ($result !== false) {
			$this->log('Файл перезаписан ' . $filename);
		} else {
			$this->log('Ошибка сохранения файла ' . $filename);
		}
	}

	/**
	 * @param string $city
	 *
	 * @return int | null
	 */
	private function findCityId($city)
	{
		if (empty(self::$_cities[$city])) {
			$this->log('Обновления доступны для городов: ' . implode(', ', array_keys(self::$_cities)));
			return null;
		}

		$city = CityModel::model()->find('title = :title', [
				':title' => self::$_cities[$city]['title']
			]);

		return $city->id_city;
	}

	/**
	 * Обновить гео-координаты станции
	 *
	 * @param StationModel $station
	 *
	 * @return bool
	 */
	private function updateStationGeoCoordinates($station)
	{
		$line = $station->undergroundLine;
		$point = \Yii::app()->yandexGeoApi->getStationGeoCoordinates($line->city->title, $line->name, $station->name);
		if ($point === null) {
			$this->log($station->id . ' - ' . $line->name . ' - ' . $station->name . ' !!! ERROR !!!');
			return false;
		}
		$station->latitude = $point['latitude'];
		$station->longitude = $point['longitude'];
		return true;
	}

	/**
	 * Загрузка всех линий и станций метро для города
	 *
	 * @param int $cityId
	 *
	 * @return $this
	 */
	private function loadStations($cityId)
	{
		$data = [];
		foreach (self::$_stationAliases as $k => $v) {
			$data[$this->reductionName($k)] = $this->reductionName($v);
		}
		self::$_stationAliases = $data;

		$data = [];
		foreach (self::$_lineAliases as $k => $v) {
			if (is_array($v)) {
				$items = [];
				foreach ($v as $c => $vv) {
					$items[$c] = $this->reductionName($vv);
				}
				$data[$this->reductionName($k)] = $items;
			} else {
				$data[$this->reductionName($k)] = $this->reductionName($v);
			}
		}
		self::$_lineAliases = $data;

		$result = UndergroundLineModel::model()->inCity($cityId)->findAll();
		foreach ($result as $item) {
			$this->_lines[$item->id] = $item;
			$this->_linesByName[$this->reductionName($item->name)] = $item;
		}

		$result = StationModel::model()->inCity($cityId)->findAll();
		foreach ($result as $item) {
			$this->_stations[$item->id] = $item;
			$this->_stationsByName[$item->underground_line_id][$this->reductionName($item->name)] = $item;
		}

		return $this;
	}

	/**
	 * Найти станцию по названию
	 *
	 * @param string $stationName
	 * @param UndergroundLineModel | string $line
	 * @param string $city
	 *
	 * @return StationModel | null
	 */
	private function getStationByName($stationName, $line, $city)
	{
		if ($line !== null && !is_object($line)) {
			$line = $this->getLineByName($line, $city);
		}

		$name = $this->reductionName($stationName);
		if (isset(self::$_stationAliases[$name])) {
			$name = self::$_stationAliases[$name];
		}

		if ($line === null || empty($this->_stationsByName[$line->id][$name])) {
			$this->log('Не найдена станция "' . $stationName . '", линия "' . ($line ? $line->name : '') . '"');
			return null;
		}
		return $this->_stationsByName[$line->id][$name];
	}

	/**
	 * Найти линию метро по названию
	 *
	 * @param string $lineName
	 * @param string $city
	 * @param bool   $isLog
	 *
	 * @return UndergroundLineModel | null
	 */
	private function getLineByName($lineName, $city, $isLog = true)
	{
		$name = $this->reductionName($lineName);
		if (isset(self::$_lineAliases[$name])) {
			if (is_array(self::$_lineAliases[$name])) {
				if (isset(self::$_lineAliases[$name][$city])) {
					$name = self::$_lineAliases[$name][$city];
				}
			} else {
				$name = self::$_lineAliases[$name];
			}
		}

		if (empty($this->_linesByName[$name])) {
			if ($isLog) {
				$this->log('Не найдена линия метро "' . $lineName . '"');
			}
			return null;
		}
		return $this->_linesByName[$name];
	}

	/**
	 * Список станций разбитый по линиям метро
	 *
	 * @return array
	 */
	private function getLineStations()
	{
		if ($this->_lineStations === null) {
			$this->_lineStations = [];
			foreach ($this->_stations as $station) {
				$this->_lineStations[$station->underground_line_id][] = $station;
			}
		}

		return $this->_lineStations;
	}

	/**
	 * Найти ближайшие станции метро с помощью Яндекса
	 *
	 * @param StationModel $station
	 * @param string $city
	 *
	 * @return array
	 */
	private function findClosestStations($station, $city)
	{
		$closestStations = array();

		$objects = \Yii::app()->yandexGeoApi->getGeocodeData([
				'geocode' => $station->longitude . ',' . $station->latitude,
				'kind' => 'metro',
				'results' => 20,
			]);

		if ($objects) {
			foreach ($objects as $obj) {
				$thoroughfare = $obj->GeoObject->metaDataProperty->GeocoderMetaData->AddressDetails->Country->AdministrativeArea->SubAdministrativeArea->Locality->Thoroughfare;

				$name = str_replace('метро ', '', $thoroughfare->Premise->PremiseName);
				$line = str_replace(' линия', '', $thoroughfare->ThoroughfareName);

				if (!in_array($line, self::$_excludeLines)) {
					$st = $this->getStationByName($name, $line, $city);
					if ($st) {
						$dist = sqrt(pow(($station->longitude - $st->longitude), 2) + pow(($station->latitude - $st->latitude), 2));

						$closestStations[$st->id] = array(
							'station' => $st,
							'lineId'  => $st->underground_line_id,
							'dist'    => $dist,
						);
					}
				}
			}
		} else {
			$this->log('Не найдены ближайшие метро для станции ' . $station->id . ' "' . $station->name . '"');
		}

		return $closestStations;
	}

	/**
	 * Поиск ближайших станций по линиям метро
	 *
	 * @param StationModel $station
	 * @param array $lineIds
	 * @param int $closestOnLine
	 *
	 * @return array
	 */
	private function findClosestLineStations($station, $lineIds, $closestOnLine)
	{
		$data = [];
		$isFull = false;
		$lineStations = $this->getLineStations();

		foreach ($lineIds as $lineId => $localStationIds) {
			foreach ($lineStations[$lineId] as $st) {
				if (in_array($st->id, $localStationIds)) {
					continue;
				}

				$dist = sqrt(pow(($station->longitude - $st->longitude), 2) + pow(($station->latitude - $st->latitude), 2));
				$isAdd = false;

				// echo ' ________ ' . $st->id . ' ' . $st->name . ' (' . $st->undergroundLine->name . '), ' . $dist . PHP_EOL;

				if ($isFull) {
					$x = null;
					foreach ($data as $key => $el) {

						if (($x === null && $dist < $el['dist']) || ($x !== null && $x['dist'] < $el['dist'])) {
							$x = $el;
						}
					}
					if ($x !== null) {
						$isAdd = true;
						unset($data[$x['station']->id]);
					}
				} else {
					$isAdd = true;
					$isFull = count($data) >= $closestOnLine;
				}

				if ($isAdd) {
					$data[$st->id] = [
						'dist' => $dist,
						'station' => $st,
					];
				}
			}
		}

		usort($data, function($a, $b) {
				return $a['dist'] == $b['dist'] ? 0 : ($a['dist'] < $b['dist'] ? -1 : 1);
			});

		return $data;
	}

	/**
	 * Сохранение связей с ближайшими станциями
	 *
	 * @param StationModel $station
	 * @param array $data
	 *
	 * @return $this
	 */
	private function saveClosestStations($station, array $data)
	{
		$connection = Yii::app()->getDb();

		$connection->createCommand('DELETE FROM closest_station WHERE station_id = :station_id')
			->bindValue(':station_id', $station->id)
			->execute();

		$values = [];
		foreach ($data as $item) {
			$values[] = '(' . $item[0] . ', ' . $item[1] . ', ' . $item[2] . ')';
		}
		$connection->createCommand('INSERT INTO closest_station VALUES ' . implode(', ', $values))->execute();

		return $this;
	}

	/**
	 * Удаление из названия всех символов кроме букв и цифр
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	private function reductionName($name)
	{
		return mb_strtolower(preg_replace('/([^А-Яa-яЁё\w\d])/i', '', $name));
	}

	/**
	 * Заменяем ид'ники станций на наши
	 *
	 * @param array $data
	 * @param array $stationIds
	 *
	 * @return array
	 */
	private function replaceStationIds(array $data, array $stationIds)
	{
		$items = array();
		foreach ($data as $value) {
			if (isset($stationIds[$value])) {
				$items[] = $stationIds[$value];
			}
			else {
				$this->log('Не найдена станция c id = ' . $value);
			}
		}
		return $items;
	}
}
