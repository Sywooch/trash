<?php
/**
 * Created by PhpStorm.
 * User: ndunaev
 * Date: 07.11.14
 * Time: 14:26
 */

namespace dfs\docdoc\objects;

/**
 * Class Coordinate
 *
 * @package dfs\docdoc\objects
 */
class Coordinate {

	/**
	 * радиус Земли, м
	 *
	 * @var float
	 */
	public $earthRadius = 6371000;

	/**
	 * Максимальный радиус поиска в километрах
	 */
	const MAX_RADIUS = 10000;

	/**
	 * широта
	 *
	 * @var float
	 */
	private $_lat = null;

	/**
	 * долгота
	 *
	 * @var float
	 */
	private $_lng = null;

	/**
	 * @param float $lat
	 * @param float $lng
	 */
	function __construct($lat, $lng) {
		$this->_lat = $this->validate($lat) ? $lat : null;
		$this->_lng = $this->validate($lng) ? $lng : null;
	}

	/**
	 * валидация коорлдинаты
	 *
	 * @param float $coord
	 *
	 * @return bool
	 */
	function validate($coord)
	{
		return is_numeric($coord);
	}

	/**
	 * Проверка валидны координаты или нет
	 *
	 * @return bool
	 */
	function isValid()
	{
		return $this->_lat !== null && $this->_lng !== null;
	}

	/**
	 * Прибавление к долготе расстояния
	 *
	 * @param float $lng
	 * @param float $distance
	 * @return float
	 */
	public static function lngPlusDistance($lng, $distance)
	{
		//1км  примерно равен 0,009 долготы
		return $lng + $distance * 0.009;
	}

	/**
	 * Прибавление к широте расстояния
	 *
	 * @param float $lat
	 * @param float $distance
	 * @return float
	 */
	public static function latPlusDistance($lat, $distance)
	{
		//на широте Москвы 1км примерно равен 0,009 широты
		//для других широт будет погрешность
		//но чтобы не использовать громозкие вычисления упростим до этого
		return $lat + $distance * 0.009;

	}

	/**
	 * Получение координат прямоугольника, полученных из метода getBounds ЯндексКарт
	 *
	 * $bound = [
	 * 		//левый верхний угол прямоугольника
	 * 		0 => [
	 * 			0 => 'долгота', 1 => 'широта'
	 * 		],
	 * 		//правый нижний кгол прямоугольника
	 * 		1 => [
	 * 			0 => 'долгота', 1 => 'широта'
	 * 		],
	 * ]
	 * @param array $bound
	 *
	 * return [
	 * 		//левый верхний угол прямоугольника
	 * 		0 => [
	 * 			0 => 'ШИРОТА', 1 => 'ДОЛГОТА'
	 * 		],
	 * 		//правый нижний кгол прямоугольника
	 * 		1 => [
	 * 			0 => 'ШИРОТА', 1 => 'ДОЛГОТА'
	 * 		],
	 * ]
	 *
	 * @return array
	 */
	public static function yandexBounds($bound)
	{
		$coords = null;
		if (is_array($bound) && count($bound) == 2
			&& isset($bound[0][0]) && isset($bound[0][1])
			&& isset($bound[1][0]) && isset($bound[1][1])
		) {
			$coords[0][0] = $bound[0][1];
			$coords[0][1] = $bound[0][0];
			$coords[1][0] = $bound[1][1];
			$coords[1][1] = $bound[1][0];
		}

		return $coords;
	}

	/**
	 * Расстояние до заданной точки, м
	 *
	 * @param float $lat широта заданной точки
	 * @param float $lng долгота заданной точки
	 *
	 * @return float
	 */
	public function getDistance($lat, $lng) {
		$deltaLat = deg2rad($this->_lat) - deg2rad($lat);
		$deltaLong = deg2rad($this->_lng) - deg2rad($lng);
		$a = sin($deltaLat / 2) * sin($deltaLat / 2) + cos(deg2rad($this->_lat)) * cos(deg2rad($lat)) * sin($deltaLong / 2) * sin($deltaLong / 2);
		$c = 2 * atan2(sqrt($a), sqrt(1 - $a));
		$d = $this->earthRadius * $c;

		return $d;
	}

} 