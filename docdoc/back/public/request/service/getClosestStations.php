<?php

/**
 * Получение ближайших станций метро и времени пешком для клиники
 */

require_once dirname(__FILE__) . "/../../include/common.php";
require_once dirname(__FILE__) . "/../../lib/php/models/clinic.class.php";
require_once dirname(__FILE__) . "/../php/requestLib.php";

$clinicId = (isset($_GET['id'])) ? (int)$_GET['id'] : 0;

$stations = array();
$clinic = new Clinic($clinicId);
if (!empty($clinic->data)) {

	if (!empty($clinic->data['longitude']) && !empty($clinic->data['latitude'])) {
		$longitude = $clinic->data['longitude'];
		$latitude = $clinic->data['latitude'];
		$objects =
			getData(
				'http://geocode-maps.yandex.ru/1.x/?geocode=' .
				$longitude .
				',' .
				$latitude .
				'&kind=metro&results=3&format=json'
			);
		if (count($objects) > 0) {
			$stations = array();
			foreach($objects as $obj) {
				$row['name'] = str_replace('метро ', '', $obj->GeoObject->name);

				$tmp = explode(',', $obj->GeoObject->description);
				$lineName = !empty($tmp) ? str_replace(' линия', '', $tmp[0]) : '';
				$line = getMetroLine($lineName);
				$row['lineColor'] = !empty($line) ? $line['color'] : '000';

				$point = explode(' ', str_replace('метро ', '', $obj->GeoObject->Point->pos));
				$row['longitude'] = $point[0];
				$row['latitude'] = $point[1];
				$row['dist'] = getDistance($latitude, $longitude, $row['latitude'], $row['longitude']);
				$row['time'] = round(($row['dist']/1.4) / 60);
				$row['dist'] = ($row['dist'] > 1000) ? round($row['dist']/1000) . ' км' : $row['dist'] . ' м';

				array_push($stations, $row);
			}
		}
	}
}

echo json_encode($stations);
