<?php

require_once dirname(__FILE__) . "/../include/common.php";

set_time_limit(60);


function getYandexGeocodeData($url) {
	$ch = curl_init();
	curl_setopt_array($ch, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => false,
			CURLOPT_HTTPHEADER => array('Content-type: application/json'),
			CURLOPT_POST => false
		));

	$response = json_decode(curl_exec($ch));
	curl_close($ch);

	return $response->response->GeoObjectCollection->featureMember;
}

switch (isset($_GET['type']) ? $_GET['type'] : null) {
	// Установка координат для станций underground_station
	case 'coord':
		echo '---- Обновление координат -----<br><br>';
		$sql = "
			SELECT t1.id, t1.name AS station, t2.name AS line, t3.title AS city, t1.longitude, t1.latitude
			FROM underground_station t1
			INNER JOIN underground_line t2 ON t2.id = t1.underground_line_id
			INNER JOIN city t3 ON t3.id_city = t2.city_id
			ORDER BY t1.id
		";
		$result = query($sql);
		while($row = fetch_object($result)) {
			$objects = getYandexGeocodeData('http://geocode-maps.yandex.ru/1.x/?geocode='.$row->city.',+'.$row->line.',+'.$row->station.'&format=json');
			$point = explode(' ', str_replace('метро ', '', $objects[0]->GeoObject->Point->pos));
			$sql = "UPDATE underground_station SET longitude=".$point[0].", latitude=".$point[1]." WHERE id=".$row->id;
			$result1 = query($sql);
			echo $row->id.' - '.$row->line.' - '.$row->station.' ('.$point[0].' '.$point[1].')'.'<br>';
		}
		break;

	// Создание временной таблицы closest_station_temp (для сравнения с текущей) и заполнение данными
	case 'closest':
		echo '------- Заливка ближайших станций ---------<br><br>';

		$sql = "CREATE TABLE IF NOT EXISTS `closest_station_temp` (
				`station_id`  DOUBLE NOT NULL,
				`closest_station_id` DOUBLE NOT NULL,
				`priority` TINYINT(3) DEFAULT NULL,
				PRIMARY KEY (`station_id`, `closest_station_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$result = query($sql);

		$sqlAdd = '';

		if(isset($_GET['start']) && isset($_GET['count']))
			$sqlAdd .= ' LIMIT '.((int)$_GET['start'] - 1).','.(int)$_GET['count'];

		$sql = "SELECT t1.id, t1.name AS station, t1.longitude, t1.latitude, t1.index, t2.name AS line, t2.id AS lineId
                FROM underground_station t1
                INNER JOIN underground_line t2 ON t2.id=t1.underground_line_id
                WHERE
                    t1.longitude<>0
                    AND t1.latitude<>0
                ORDER BY t1.id
                 " . $sqlAdd;
		$result = query($sql);
		while($row = fetch_object($result)) {
			echo '------- '.$row->id.' - '.$row->station.' ('.$row->line.')---------<br>';

			$objects = getYandexGeocodeData('http://geocode-maps.yandex.ru/1.x/?geocode='.$row->latitude.','.$row->longitude.'&kind=metro&results=20&format=json');
			if (count($objects) > 0) {
				$stations = array();
				foreach($objects as $obj) {
					$thoroughfare = $obj->GeoObject->metaDataProperty->GeocoderMetaData->AddressDetails->Country->AdministrativeArea->SubAdministrativeArea->Locality->Thoroughfare;
					$point = explode(' ', str_replace('метро ', '', $obj->GeoObject->Point->pos));

					$stations[] = array(
						'name' => str_replace('метро ', '', $thoroughfare->Premise->PremiseName),
						'line' => str_replace(' линия', '', $thoroughfare->ThoroughfareName),
						'latitude' => $point[1],
						'longitude' => $point[0],
						'dist' => sqrt(pow(($row->longitude - $point[0]), 2) + pow(($row->latitude - $point[1]), 2)),
					);

					//echo $stations[$i]['line'].' - '.$stations[$i]['name'].' ('.$stations[$i]['latitude'].' '.$stations[$i]['longitude'].') - '.$stations[$i]['dist'].'<br>';
				}

				$sql = "DELETE FROM closest_station_temp WHERE station_id=" . $row->id;
				query($sql);

				$sql = "INSERT INTO closest_station_temp VALUES (".$row->id.",".$row->id.",0)";
				query($sql);

				/// 1. Обновление станций-пересадок
				echo '------- Станции пересадок<br>';
				for($i = 0; $i < 4; $i++) {
					if($stations[$i]['dist'] < 0.007) {
						$sql = "SELECT t1.id
                                FROM underground_station t1
                                INNER JOIN underground_line t2 ON t2.id=t1.underground_line_id
                                WHERE UPPER(t1.name) LIKE '%".strtoupper($stations[$i]['name'])."%'
                                    AND UPPER(t2.name) LIKE '%".strtoupper($stations[$i]['line'])."%'";
						$result1 = query($sql);
						while($row1 = fetch_object($result1)) {
							if($row->id <> $row1->id) {
								$sql = "INSERT INTO closest_station_temp VALUES (".$row->id.",".$row1->id.",1)";
								query($sql);

								echo $stations[$i]['name'].' ('.$stations[$i]['line'].')<br>';
							}
						}
					}
				}

				/// 2. Ближайшие на этой же ветке
				echo '------- Ближайшие на этой же ветке<br>';
				$startIndex = $row->index - 3;
				$endIndex = $row->index + 3;
				$sql = "SELECT t1.id, t1.name
                        FROM underground_station t1
                        WHERE t1.underground_line_id=".$row->lineId."
                            AND t1.id<>".$row->id."
                            AND t1.index BETWEEN ".$startIndex." AND ".$endIndex;
				$result2 = query($sql);
				while($row2 = fetch_object($result2)) {
					$sql = "DELETE FROM closest_station_temp WHERE station_id=".$row->id." AND closest_station_id=".$row2->id;
					query($sql);
					$sql = "INSERT INTO closest_station_temp VALUES (".$row->id.",".$row2->id.",2)";
					query($sql);

					echo $row2->name.'<br>';
				}

				/// 3. Ближайшие станции
				echo '------- Ближайшие станции<br>';
				$i = 3;
				foreach($stations as $item) {
					$sql = "SELECT t1.id
                                FROM underground_station t1
                                INNER JOIN underground_line t2 ON t2.id=t1.underground_line_id
                                WHERE UPPER(t1.name) LIKE '%".strtoupper($item['name'])."%'
                                    AND UPPER(t2.name) LIKE '%".strtoupper($item['line'])."%'";
					$result3 = query($sql);
					if(num_rows($result3) > 0){
						$row3 = fetch_object($result3);
						$sql = "SELECT * FROM closest_station_temp WHERE station_id=".$row->id." AND closest_station_id=".$row3->id;
						$result4 = query($sql);
						if(num_rows($result4) == 0 && $row->id <> $row3->id){
							$sql = "INSERT INTO closest_station_temp VALUES (".$row->id.",".$row3->id.",".$i.")";
							query($sql);

							$i++;

							echo $item['name'].' ('.$item['line'].')<br>';
						}
					}
				}

				echo '-----------------------<br><br>';
			}
		}
		break;

	// Переформирование файла data.js (маппинг id-ков станций)
	case 'cian':
		$stationsAliases = array(
			'бульвар адмирала ушакова' => 'бульвар адм. ушакова',
			'бульвар дмитрия донского' => 'бульвар дм. донского',
			'преображенская площадь' => 'преображенская пл.',
			'улица старокачаловская' => 'ул. старокачаловская',
			'улица академика янгеля' => 'ул. академика янгеля',
			'пл. революции' => 'площадь революции',
			'волгоградский проспект' => 'волгоградский пр-т',
			'бульвар рокоссовского' => 'улица подбельского',
		);
		$url = 'http://www.cian.ru/nd/search/global/metro/metro.php?city_id=moscow';
		$data = json_decode(file_get_contents($url), true);

		$lines = array();
		$stations = array();
		$st = array();

		$result = query('SELECT id, name FROM underground_line WHERE city_id = 1 ORDER BY id');
		while($item = fetch_object($result)) {
			$lines[$item->name] = $item->id;
		}

		$result = query('SELECT s.id, s.name, s.underground_line_id as line_id
			FROM underground_station as s
				INNER JOIN underground_line as l ON (s.underground_line_id = l.id AND l.city_id = 1)
			ORDER BY id');
		while($item = fetch_object($result)) {
			$stations[$item->line_id][mb_strtolower($item->name)] = $item->id;
			$st[$item->id] = $item;
		}

		$lineIds = array();
		$stationIds = array();
		$stationsNew = array();

		foreach ($data['branches'] as $item) {
			$id = null;
			if (isset($lines[$item['label_name']])) {
				$id = $lines[$item['label_name']];
			}
			elseif (isset($lines[$item['name']])) {
				$id = $lines[$item['name']];
			}
			$lineIds[$item['id']] = $id;
		}

		$items = array();
		foreach ($data['stations'] as $key => $item) {
			$branchId = reset($item['branch_ids']);
			$lineId = isset($lineIds[$branchId]) ? $lineIds[$branchId] : null;
			$name = mb_strtolower($item['name']);
			if (isset($stationsAliases[$name])) $name = $stationsAliases[$name];
			$id = isset($stations[$lineId][$name]) ? $stations[$lineId][$name] : null;
			if ($id) {
				$stationIds[$item['id']] = $id;
				$item['id'] = $id;
				$items[] = $item;
				unset($stations[$lineId][$name]);
				if (empty($stations[$lineId])) {
					unset($stations[$lineId]);
				}
			}
			else {
				$stationsNew[$item['id']] = [
					'id' => $item['id'],
					'name' => $name,
					'branch_ids' => $branchId,
					'line_id' => $lineId,
				];
			}
		}
		$data['stations'] = $items;

		// var_dump('!!!! circle !!!!');

		$items = array();
		foreach ($data['circle'] as $value) {
			if (isset($stationIds[$value])) {
				$items[] = $stationIds[$value];
				// echo $value . ' - [' . $stationIds[$value] . '] ' . $st[$stationIds[$value]]->name . "<br/>\n";
			}
			// else var_dump('unknown inside ' . $value);
		}
		$data['circle'] = $items;

		// var_dump('!!!! inside !!!!');

		$items = array();
		foreach ($data['inside'] as $value) {
			if (isset($stationIds[$value])) {
				$items[] = $stationIds[$value];
				// echo $value . ' - [' . $stationIds[$value] . '] ' . $st[$stationIds[$value]]->name . "<br/>\n";
			}
			// else var_dump('unknown inside ' . $value);
		}
		$data['inside'] = $items;

		// var_dump($stationsNew);
		// var_dump($stations);

		echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		break;

	default:
		echo 'Не выбрано действие!';
		break;
}
