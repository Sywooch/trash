<?php

use dfs\docdoc\models\SectorModel;
use dfs\docdoc\models\DoctorClinicModel;

/**
 *
 * Справочник заболеваний
 */
function getIllnessXML($columns = 3)
{
	$xml = "";

	$sql = "SELECT
					il.id, il.name, 
					il.rewrite_name 
				FROM `illness` il
				WHERE 
					il.is_hidden = 0
				ORDER BY name";
	$result = query($sql);

	$count = num_rows($result);
	if ($count > 0) {
		$xml .= "<IllnessList>";
		$items = array();
		while ($row = fetch_object($result)) {
			$items[] = $row;
		}

		$itemList = getGroupsByAlphabet($items);
		$countInColumns = floor((count($itemList) + $count) / $columns);

		$i = 0;
		$xml .= '<Group>';
		foreach ($itemList as $group) {
			if ($i >= $countInColumns) {
				$xml .= '</Group><Group>';
				$i = 0;
			}
			$i += count($group);
			$i++;
			$xml .=
				'<Group title="' .
				mb_substr($group[0]->name, 0, 1, 'utf-8') .
				'" char="' .
				mb_substr($group[0]->name, 0, 1, 'utf-8') .
				'">';
			foreach ($group as $item) {
				$xml .= '<Element id="' . $item->id . '">';
				$xml .= "<Id>" . $item->id . "</Id>";
				$xml .= "<Name>" . $item->name . "</Name>";
				$xml .= "<RewriteName>" . $item->rewrite_name . "</RewriteName>";
				$xml .= "<FirstLetter>" . strtoupper(substr($item->name, 0, 2)) . "</FirstLetter>";
				$xml .= "</Element>";
			}
			$xml .= '</Group>';
		}
		$xml .= '</Group>';

		$xml .= "</IllnessList>";
	}

	return $xml;
}

/**
 * Получает список специальностей
 *
 * @param array $params Не используется
 * @param null  $city   Идентификатор города
 *
 * @return string
 */
function getSpecializationListXML($params = array(), $city = null)
{
	$city = $city ?: Yii::app()->city->getCityId();
	$specs = SectorModel::getItemsByCity($city);

	return !empty($specs) ? "<SectorList>" . arrayToXML($specs) . "</SectorList>" : "";
}

/**
 * Получение xml со списком заболеваний по выбранной специальности
 *
 * @param int $sectorId
 * @param int $columns
 *
 * @return string
 */
function getIllnessLikeXML($sectorId, $columns = 2)
{
	$xml = "";
	$data = array();
	$sectorId = intval($sectorId);

	$sql = "SELECT
	    			il.id, 
	    			il.name, 
	    			il.rewrite_name 
	    		FROM illness il
	            WHERE 
	            	il.sector_id = " . $sectorId . "
		            AND 
		            is_hidden = 0
	            ORDER BY il.name";
	$result = query($sql);

	$count = num_rows($result);
	$countInColumn = ceil($count / $columns);
	if ($count > 0) {
		while ($row = fetch_array($result)) {
			array_push($data, $row);
		}
	}

	if (!empty($data)) {
		$data = array_chunk($data, $countInColumn);
		$xml .= "<IllnessLikeList>";
		foreach ($data as $group) {
			$xml .= '<Group>';
			foreach ($group as $item) {
				$xml .= '<Element id="' . $item['id'] . '">';
				$xml .= '<Name>' . $item['name'] . '</Name>';
				$xml .= '<RewriteName>' . $item['rewrite_name'] . '</RewriteName>';
				$xml .= '</Element>';
			}
			$xml .= '</Group>';
		}
		$xml .= "</IllnessLikeList>";
	}

	return $xml;

}

function getArticlesGroupXML($columns = 3)
{
	$xml = "";
	$data = array();

	$sql = "SELECT
	    			art.id AS Id,
	    			art.name AS Name,
	    			art.rewrite_name AS RewriteName,
	    			art.sector_id AS Sector,
	    			(SELECT count(article.id) FROM article article WHERE article.article_section_id = art.id) as Count
	    		FROM article_section art
	            ORDER BY art.name";
	$result = query($sql);

	$count = num_rows($result);
	$countInColumn = ceil($count / $columns);
	if ($count > 0) {
		while ($row = fetch_array($result)) {
			array_push($data, $row);
		}
	}
	$data = array_chunk($data, $countInColumn);
	if (!empty($data)) {
		$xml .= "<ArticleGroupList>";
		foreach ($data as $group) {
			$xml .= '<Group>';
			foreach ($group as $item) {
				$xml .= '<Element id="' . $item['Id'] . '">';
				$xml .= '<Name>' . $item['Name'] . '</Name>';
				$xml .= '<RewriteName>' . $item['RewriteName'] . '</RewriteName>';
				$xml .= '<Sector>' . $item['Sector'] . '</Sector>';
				$xml .= '<Count>' . $item['Count'] . '</Count>';
				$xml .= '</Element>';
			}
			$xml .= '</Group>';
		}
		$xml .= "</ArticleGroupList>";
	}

	return $xml;
}

function getArticlesNoGroupXML()
{
	$xml = "";

	$sql = "SELECT 
	    			art.id, 
	    			art.name, 
	    			art.rewrite_name,
	    			art.description
	    		FROM article art
	    		WHERE 
	    			article_section_id = 0
	    			AND
	    			disabled = 0
	            ORDER BY art.id";
	$result = query($sql);

	if (num_rows($result) > 0) {
		$xml .= "<ArticleNoGroupList>";
		while ($row = fetch_object($result)) {
			$xml .= '<Element id="' . $row->id . '">';
			$xml .= '<Name><![CDATA[' . $row->name . ']]></Name>';
			$xml .= '<RewriteName>' . $row->rewrite_name . '</RewriteName>';
			$xml .= '<Description><![CDATA[' . $row->description . ']]></Description>';
			$xml .= '</Element>';
		}
		$xml .= "</ArticleNoGroupList>";
	}

	return $xml;

}

function getAlphabetXML()
{
	$xml = "";
	$alphabet = "А Б В Г Д Е Ё Ж З И К Л М Н О П Р С Т У Ф Х Ц Ч Ш Щ Э Ю Я";
	$alphabet = explode(" ", $alphabet);

	$xml .= "<Alphabet>";
	for ($i = 0; $i < count($alphabet); $i++) {
		$xml .= "<Element>" . $alphabet[$i] . "</Element>";
	}
	$xml .= "</Alphabet>";

	return $xml;

}

/**
 * Получение групп специальностей
 *
 * @param int  $city               Город
 * @param int  $columnCount        Количество коллонок
 * @param null $activeSpecialityId Выбранная специальность
 *
 * @return string XML
 */
function specialityGroupListXML($city, $columnCount = 3, $activeSpecialityId = null)
{
	$xml = "";

	$sectorModel = SectorModel::model()
		->active()
		->simple()
		->inCity($city)
		->visible()
		->ordered()
		->cache(3600);

	$sectors = $sectorModel->findAll();

	if ($sectors) {
		$xml .= "<SpecialityList>";

		// Получаем группы специальностей по алфавиту
		$sectorList = getGroupsByAlphabet($sectors);

		// Определяем кол-во оступов, которые будут отображаться на странице, убираем отступы в начале и конце каждой колонки
		$countIndents = count($sectorList) - $columnCount;

		// Определяем кол-во строк в колонке
		$countInColumns = floor(($countIndents + count($sectors)) / $columnCount);
		//$countInColumns = 8;
		// Получаем массив, разбитый по колонкам и группам
		$columns = getItemsByColumns($sectorList, $countInColumns);

		while (count($columns) > $columnCount) {
			$columns = getItemsByColumns($sectorList, $countInColumns++);
		}
		while (count($columns) < $columnCount && $countInColumns >= 0) {
			$columns = getItemsByColumns($sectorList, $countInColumns--);
		}

		foreach ($columns as $column) {
			$xml .= '<Group>';
			foreach ($column as $group) {
				$xml .= '<Group char="' . mb_substr($group[0]->name, 0, 1, 'utf-8') . '">';
				foreach ($group as $item) {
					$selected = $activeSpecialityId == $item->id
						? " selected='1'"
						: '';

					$xml .= "<Element id='{$item->id}'{$selected}>";
					$xml .= '<Name>' . $item->name . '</Name>';
					$xml .= '<RewriteName>' . $item->rewrite_name . '</RewriteName>';
					$xml .= '</Element>';
				}
				$xml .= '</Group>';
			}
			$xml .= '</Group>';
		}

		$xml .= "</SpecialityList>";
	}

	return $xml;
}

/**
 * Получение массива разбитого по колонкам
 *
 * @param array $groups группы элементов
 * @param int $count количество строк в колонке
 *
 * @return array
 */
function getItemsByColumns($groups, $count)
{
	$result = array();
	$cnt = 0;
	$col = 0;
	foreach ($groups as $group) {
		// Добавляем строки с элементами
		$cnt += count($group);
		// Добавляем пустую строку
		$cnt++;
		if ($cnt > $count) {
			$cnt = count($group);
			$col++;
		}
		$result[$col][] = $group;
	}

	return $result;
}

/*
 * Получение станций метро
 * @param array $ids
 * @return array
 */
function getStations($ids)
{
	$data = array();

	if (count($ids) > 0) {

		$ids = array_map(
			function ($v) {
				return (int)$v;
			},
			$ids
		);

		$ids = implode(',', $ids);

		$sql = "SELECT id AS Id, rewrite_name AS Alias, name AS Name
						FROM underground_station
						WHERE id IN ($ids)";

		$result = query($sql);
		while ($row = fetch_array($result)) {
			array_push($data, $row);
		}
	}

	return $data;
}

/**
 * Получает станцию метро по абривиатуре
 *
 * @param string $alias абривиатура станции метро
 * @param int    $city  идентификатор города
 *
 * @return string[]
 */
function getStationByAlias($alias, $city)
{
	$data = array();

	$sql = "
		SELECT t1.id AS Id, t1.rewrite_name AS Alias, t1.name AS Name
		FROM underground_station AS t1
		LEFT JOIN underground_line AS t2 ON t2.id = t1.underground_line_id
		WHERE t1.rewrite_name = '{$alias}' AND t2.city_id = {$city}
		LIMIT 1
	";
	$result = query($sql);
	if (num_rows($result) == 1) {
		$data = fetch_array($result);
	}

	return $data;
}

function getStationsByParams($params)
{
	$data = array();

	if (isset($params['area'])) {
		$sql = "SELECT station_id AS Id FROM area_underground_station WHERE area_id=" . (int)$params['area'];
	} elseif (isset($params['district'])) {
		$sql =
			"SELECT id_station AS Id FROM district_has_underground_station WHERE id_district=" .
			(int)$params['district'];
	} elseif (isset($params['regCity'])) {
		$sql =
			"SELECT station_id AS Id FROM underground_station_4_reg_city WHERE reg_city_id=" . (int)$params['regCity'];
	}
	$result = query($sql);
	while ($row = fetch_array($result)) {
		$data[] = $row['Id'];
	}

	return $data;
}

function getArea($alias)
{
	$data = array();

	$sql = "SELECT id AS Id, name AS Name, full_name AS FullName, rewrite_name AS Alias
					FROM area_moscow t1
					WHERE rewrite_name='$alias'";
	$result = query($sql);
	if (num_rows($result) == 1) {
		$data = fetch_array($result);
	}

	return $data;
}

function getRegCity($alias)
{
	$data = array();

	$sql = "SELECT id AS Id, name AS Name, rewrite_name AS Alias
					FROM reg_city
					WHERE rewrite_name='$alias'";
	$result = query($sql);
	if (num_rows($result) == 1) {
		$data = fetch_array($result);
	}

	return $data;
}

function getSpeciality($alias)
{
	$data = array();

	$sql = "SELECT
						id AS Id, rewrite_name AS Alias,
						rewrite_spec_name AS SpecAlias, name AS Name,
						LOWER(name) AS NameInLower, spec_name AS Specialization,
						clinic_seo_title AS ClinicName
					FROM sector
					WHERE rewrite_name='" . $alias . "' OR rewrite_spec_name='" . $alias . "'";
	$result = query($sql);
	if (num_rows($result) == 1) {
		$data = fetch_array($result);
		$data['ClinicInGenitive'] = mb_strtolower(RussianTextUtils::wordInGenitive($data['ClinicName'], true));
		$data['InGenitive'] = RussianTextUtils::wordInGenitive($data['Name']);
		$data['InGenitiveLC'] = RussianTextUtils::wordInGenitive($data['NameInLower']);
		$data['InGenitivePlural'] = RussianTextUtils::wordInGenitive($data['Name'], true);
		$data['InGenitivePluralLC'] = RussianTextUtils::wordInGenitive($data['NameInLower'], true);
		$data['InPlural'] = RussianTextUtils::wordInNominative($data['Name'], true);
		$data['InPluralLC'] = RussianTextUtils::wordInNominative($data['NameInLower'], true);
	}

	return $data;
}

function getSpecialityById($id)
{
	$data = array();

	$sql = "SELECT id AS Id, rewrite_name AS Alias, name AS Name, LOWER(name) AS NameInLower  
					FROM sector
					WHERE id='" . $id . "'";
	$result = query($sql);
	if (num_rows($result) == 1) {
		$data = fetch_array($result);
		$data['InGenitive'] = RussianTextUtils::wordInGenitive($data['Name']);
		$data['InGenitivePlural'] = RussianTextUtils::wordInGenitive($data['Name'], true);
		$data['InPlural'] = RussianTextUtils::wordInNominative($data['Name'], true);
		$data['InPluralLC'] = RussianTextUtils::wordInNominative($data['NameInLower'], true);
	}

	return $data;
}

function checkSpecialityInCity($id, $cityId)
{
	$sql = "SELECT *
					FROM doctor_sector t1 
					INNER JOIN doctor t2 ON t2.id=t1.doctor_id
					INNER JOIN doctor_4_clinic t3 ON t3.doctor_id=t2.id and t3.type = " . DoctorClinicModel::TYPE_DOCTOR . "
					INNER JOIN clinic t4 ON t4.id=t3.clinic_id
					WHERE
						t1.sector_id=$id 
						AND t4.city_id=$cityId";
	$result = query($sql);
	if (num_rows($result) > 1) {
		return true;
	} else {
		return false;
	}
}

function getSectorList4ClinicXML($city, $columns = 3)
{
	$xml = "";
	$data = array();
	$city = intval($city);

	$sql = "SELECT
	    			sec.id AS Id,
	    			sec.spec_name AS Name,
	    			sec.rewrite_spec_name AS RewriteName,
	    			( 
	    				SELECT COUNT(DISTINCT(cl.id))
	    				FROM clinic cl, doctor_4_clinic d4c, doctor_sector d4s, doctor d
	    				WHERE 
	    					d4s.sector_id = sec.id
	    					AND
	    					d4c.doctor_id = d4s.doctor_id
	    					AND
	    					d4c.type = " . DoctorClinicModel::TYPE_DOCTOR . "
	    					AND
	    					d4c.clinic_id = cl.id
	    					AND
	    					d4c.doctor_id = d.id
	    					AND
	    					d.status = 3
	    					AND
	    					cl.status = 3
	    					AND
	    					cl.isClinic = 'yes'
	    					AND
	    					sec.is_double = 0
	    					AND
	    					cl.city_id = " . $city . "
	    			) AS ClinicCount
	    		FROM sector sec
	            GROUP BY sec.id
	            HAVING ClinicCount > 0
	            ORDER BY sec.spec_name";
	$result = query($sql);

	$count = num_rows($result);
	$countInColumn = ceil($count / $columns);
	if ($count > 0) {
		while ($row = fetch_array($result)) {
			array_push($data, $row);
		}
	}

	if ($countInColumn && $data) {
		$data = array_chunk($data, $countInColumn);
		$xml .= "<SpecializationList>";
		foreach ($data as $group) {
			$xml .= '<Group>';
			foreach ($group as $item) {
				$xml .= '<Element id="' . $item['Id'] . '">';
				$xml .= '<Id>' . $item['Id'] . '</Id>';
				$xml .= '<Name>' . $item['Name'] . '</Name>';
				$xml .= '<RewriteName>' . $item['RewriteName'] . '</RewriteName>';
				$xml .= '<ClinicCount>' . $item['ClinicCount'] . '</ClinicCount>';
				$xml .= '</Element>';
			}
			$xml .= '</Group>';
		}
		$xml .= "</SpecializationList>";
	}

	return $xml;
}

/**
 * Получение списка станций метро
 *
 * @param int   $cityId                     Город
 * @param int   $columns                    Количество колонов
 * @param int[] $activeMetroStationsIdsList Активные станции метро
 *
 * @return string XML
 */
function getMetroList($cityId = 1, $columns = 4, array $activeMetroStationsIdsList = array())
{
	$xml = "";
	$data = array();
	$cityId = intval($cityId);

	$sql = "
		SELECT t1.id, t1.name, substr(t1.name,1,1) as FL
		FROM underground_station t1, underground_line t2
		WHERE t1.underground_line_id = t2.id AND t2.city_id = {$cityId}
		ORDER BY t1.name ASC, t1.id ASC
	";

	$result = query($sql);

	$count = num_rows($result);

	if ($count > 0) {
		$xml = "<MetroListByAZ>";
		while ($row = fetch_object($result)) {
			array_push($data, $row);
		}

		$metroList = getGroupsByAlphabet($data);
		$countInColumn = floor((count($metroList) + $count) / $columns);

		$i = 0;
		$xml .= '<Group>';
		foreach ($metroList as $group) {
			if ($i >= $countInColumn) {
				$xml .= '</Group><Group>';
				$i = 0;
			}
			$i += count($group);
			$i++;
			$xml .= '<Group char="' . mb_substr($group[0]->name, 0, 1, 'utf-8') . '">';
			foreach ($group as $item) {
				$selected = in_array($item->id, $activeMetroStationsIdsList)
					? " selected='1'"
					: '';
				$xml .= "<Element letter='{$item->FL}' id='{$item->id}'{$selected}>{$item->name}</Element>";
			}
			$xml .= '</Group>';
		}
		$xml .= '</Group>';

		$xml .= "</MetroListByAZ>";
	}

	return $xml;
}

/**
 * Метод, который возвращает "Список районов" в поиске врачей
 *
 * @param int $cityId идентификатор города
 * @param int $columns количество колонок на виде
 * @return string возвращается xml
 */
function getDistrictList($cityId, $columns = 4)
{
	if ($cityId == 1) {
		$xml = '';
		$sql = "SELECT
				a.name AS 'okrug',
				a.id AS 'area_id',
				d.name AS 'rayon',
				d.id AS 'district_id',
				id_station AS 'station_id',
				us.name AS 'metro_name'
				FROM area_moscow a
				LEFT JOIN district d ON (a.id = d.id_area)
				LEFT JOIN district_has_underground_station AS dhus  ON (d.id = dhus.id_district)
				LEFT JOIN underground_station AS us ON (dhus.id_station = us.id)
				GROUP BY a.id, d.id
				ORDER BY a.id, d.name";
		$result = query($sql);
		$rows = fetch_all($result);
		$xml .= '<DistrictList>';
		$xml .= getGroupsXML($rows, $columns);
		$xml .= '</DistrictList>';
	} else {
		$xml = '<DistrictList>';

		$districtList = getDistricts($cityId);

		if (!empty($districtList)) {
			$countInColumn = ceil(count($districtList) / $columns);
			$districtsList = array_chunk($districtList, $countInColumn);

			foreach ($districtsList as $group) {

				$xml .= '<Group>';
				foreach ($group as $item) {
					$item['stations'] = implode(',', array_unique(getStationsIds($item['id'])));
					$xml .= "<Element id = '" . $item['id'] . "' stationsIdArray = '" . $item['stations'] . "'>";
					$xml .= "<Title>" . $item['name'] . "</Title>";
					$xml .= "<IdArea>" . $item['id_area'] . "</IdArea>";
					$xml .= getMetro_XML($item['id']);
					$xml .= "</Element>";
				}
				$xml .= '</Group>';
			}
		}

		$xml .= '</DistrictList>';
	}

	return $xml;
}

/**
 * Метод получения районов для городов отличных от Москвы
 *
 * @param int   $cityId           идентификатор города
 * @param int   $areaId      идентификатор округа
 * @return array            возвращает массив районов
 */
function getDistricts($cityId, $areaId = null)
{
	$data = array();
	$sqlAdd = !empty($areaId) ? ' AND id_area=' . $areaId : '';

	$sql = "SELECT
				t1.id, t1.name, t1.id_area, t1.rewrite_name AS alias
			FROM district t1
			WHERE
				t1.id_city = " . $cityId . $sqlAdd . "
			ORDER BY t1.name ASC";
	$result = query($sql);
	while ($row = fetch_array($result)) {
		array_push($data, $row);
	}

	return $data;
}

/**
 * Метод получения списка станций метро для района в городах отличных от Москвы
 *
 * @param int   $districtId       идентификатор района
 * @return array            возвращает массив из станций метро
 */
function getStationsIds($districtId)
{
	$data = array();

	$sql = "SELECT
				t1.id_station AS id
			FROM district_has_underground_station t1
			WHERE
				t1.id_district = " . $districtId;

	$result = query($sql);
	while ($row = fetch_array($result)) {
		$data[] = $row['id'];
	}

	return $data;
}

/**
 * Метод по формированию групп для DistrictList
 *
 * @param array     $rows         массив данных
 * @param int       $columns      количество колонок на виде
 * @return string   $xml  кусок кода с группами
 */
function getGroupsXML($rows, $columns)
{
	$xml = '';
	$okrugList = getOkrugList($rows);
	$i = 1;
	$xml .= '<Group>';
	foreach ($okrugList as $okrug) {
		$total = count(getOkrugList($rows));
		$countInColumn = floor($total / $columns);
		$xml .=
			'<Group title="' .
			$okrug .
			'" stationsIdArray = "' .
			implode(',', getMetroStationsForOkrug($okrug, $rows)) .
			'">';
		$groups = getGroups($okrug, $rows);
		foreach ($groups as $group) {
			$xml .= "<Element id = '" . $group[0] . "' stationsIdArray = '" . implode(',', getMetroStationsForGroup($rows, $group[1])) . "'>";
			$xml .= "<Title>" . $group[1] . "</Title>";
			$xml .= "<IdArea>" . $group[2] . "</IdArea>";
			$xml .= getMetroListXML($group[0], $rows);
			$xml .= "</Element>";
		}
		$xml .= '</Group>';
		if ($i >= $countInColumn) {
			$xml .= '</Group><Group>';
			$i = 0;
		}
		$i++;
	}
	$xml .= '</Group>';
	return $xml;
}

/**
 * Метод получения округов для xml
 *
 * @param string $okrug    округ
 * @param array  $rows     массив с данными
 * @return array    возвращает массив из округов
 */
function getGroups($okrug, $rows)
{
	$data = array();
	$group = array();
	$rayon = '';
	foreach ($rows as $row) {
		if ($row['okrug'] == $okrug && $row['rayon'] !== $rayon) {
			array_push($data, $row['district_id'], $row['rayon'], $row['area_id']);
			$group [] = $data;
			$data = array();
			$rayon = $row['rayon'];
		}
	}
	return $group;
}

/**
 * Метод получения станций метро для округов
 *
 * @param string    $okrug округ
 * @param array     $rows массив значений
 * @return array массив из станций
 */
function getMetroStationsForOkrug($okrug, $rows)
{
	$data = array();
	foreach ($rows as $row) {
		if ($row['okrug'] == $okrug) {
			array_push($data, $row['station_id']);
		}
	}
	return $data;
}

/**
 * Метод получения станций метро для района
 *
 * @param $rows массив данных
 * @param $rayon район
 * @return array возвращает массив станций метро для района
 */
function getMetroStationsForGroup($rows, $rayon)
{
	$data = array();
	foreach ($rows as $row) {
		if ($row['rayon'] == $rayon) {
			array_push($data, $row['station_id']);
		}
	}
	return $data;
}

/**
 * Метод выбирает округа
 *
 * @param array $rows массив данных
 * @return int возвращает округа
 */
function getOkrugList($rows)
{
	$data = array();
	$okrug = '';
	foreach ($rows as $row) {
		if ($row['okrug'] !== $okrug) {
			array_push($data, $row['okrug']);
			$okrug = $row['okrug'];
		}
	}
	return $data;
}

/**
 * Метод формирования xml со списком метро
 *
 * @param int   $district id района
 * @param array $rows массив данных для обработки
 * @return string возвращает xml
 */
function getMetroListXML($district, $rows)
{
	$xml = "";
	$xml .= "<MetroList>";
	foreach ($rows as $row) {
		if ($row['district_id'] == $district) {
			$xml .= "<Element id = '" . $row['station_id'] . "'>" . $row['metro_name'] . "</Element>";
		}
	}
	$xml .= "</MetroList>";
	return $xml;
}


function getGroupsByAlphabet($data)
{
	$firstChar = '';
	$itemList = array();
	$i = 0;

	foreach ($data as $item) {

		if (mb_substr($item->name, 0, 1, 'utf-8') != $firstChar) {
			$i++;
		}

		$itemList[$i][] = $item;
		$firstChar = mb_substr($item->name, 0, 1, 'utf-8');
	}

	return $itemList;
}

/**
 * Получение списка XML в виде колонок
 *
 * @param $items
 * @param int $columns
 *
 * @return string
 */
function getColumnsXML($items, $columns = 5)
{
	$xml = '';
	$countInColumns = count($items) / $columns;

	$i = 0;
	$xml .= '<Group>';
	foreach ($items as $item) {
		$xml .= '<Element>' . arrayToXML($item) . '</Element>';
		$i++;
		if ($i > $countInColumns) {
			$xml .= '</Group><Group>';
			$i = 0;
		}
	}
	$xml .= '</Group>';


	return $xml;
}
