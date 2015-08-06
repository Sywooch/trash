<?php

namespace dfs\docdoc\components;

/**
 * Class YandexGeoApi
 *
 * Компонент для работы с geo-API Яндекса
 *
 * @package dfs\docdoc\components
 */
class YandexGeoApi extends \CApplicationComponent
{
	const URL_YANDEX_API_GEOCODE = 'http://geocode-maps.yandex.ru/1.x/';

	/**
	 * Получение данных через api яндекса
	 *
	 * @param array $params
	 *
	 * @return mixed
	 */
	public function getGeocodeData(array $params)
	{
		$params['format'] = 'json';

		$ch = curl_init();
		curl_setopt_array($ch, [
				CURLOPT_URL => self::URL_YANDEX_API_GEOCODE . '?' . http_build_query($params),
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HEADER => false,
				CURLOPT_HTTPHEADER => array('Content-type: application/json'),
				CURLOPT_POST => false,
			]);

		$response = json_decode(curl_exec($ch));
		curl_close($ch);

		return $response->response->GeoObjectCollection->featureMember;
	}

	/**
	 * Получить гео-координаты станции метро
	 *
	 * @param string $city
	 * @param string $line
	 * @param string $station
	 *
	 * @return array | null
	 */
	public function getStationGeoCoordinates($city, $line, $station)
	{
		$point = null;

		$objects = $this->getGeocodeData([ 'geocode' => $city . ', линия ' . $line . ', метро ' . $station ]);
		foreach ($objects as $obj) {
			if (strpos($obj->GeoObject->name, 'метро') !== false) {
				$point = explode(' ', $obj->GeoObject->Point->pos);
				break;
			}
		}

		if ($point === null && isset($objects[0])) {
			$point = explode(' ', $objects[0]->GeoObject->Point->pos);
		}
		if ($point === null || !isset($point[0], $point[1])) {
			return null;
		}
		return [
			'latitude' => $point[1],
			'longitude' => $point[0],
		];
	}


	/**
	 * Получить гео-координаты прямоугольника описывающего улицу
	 *
	 * @param string $city
	 * @param string $street
	 *
	 * @return array | null
	 */
	public function getStreetGeoCoordinates($city, $street)
	{
		$objects = $this->getGeocodeData([ 'geocode' => $city . ', ' . $street ]);
		if (empty($objects)) {
			return null;
		}
		$envelop = $objects[0]->GeoObject->boundedBy->Envelope;

		list($left, $bottom) = explode(' ', $envelop->lowerCorner);
		list($right, $top) = explode(' ', $envelop->upperCorner);

		if ($left == 0 || $right == 0 || $bottom == 0 || $top == 0) {
			return null;
		}
		return [
			'left' => $left,
			'right' => $right,
			'bottom' => $bottom,
			'top' => $top,
		];
	}
}
