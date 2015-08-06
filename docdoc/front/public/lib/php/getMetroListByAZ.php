<?php
    function getMetroListByAZ_XML ($cityId = 1) {
    	$xml = "";
    	$cityId = intval ($cityId);

		$sql = "
			SELECT t1.id, t1.name, substr(t1.name,1,1) as FL
			FROM underground_station t1, underground_line t2
			WHERE t1.underground_line_id = t2.id AND t2.city_id = {$cityId}
			ORDER BY t1.name ASC
		";

	    $result = query($sql);
	    if (num_rows($result) > 0) {
	    	$xml = "<MetroListByAZ>";
	        while ($row = fetch_object($result)) {
	            $xml .= "<Element letter = '".$row->FL."' id = '".$row->id."'>".$row->name."</Element>";
	        }
	        $xml .= "</MetroListByAZ>";
	    }
    
    	return $xml;
    }

/**
 * Получает XML из метро в алфавитном порядке
 *
 * @param int $cityId идентификатор города
 *
 * @return string
 */
function getMetroAlpabet_XML($cityId = 1)
{
	$xml = "";
	$cityId = intval($cityId);

	$sql = "
		SELECT substr(t1.name,1,1) as FL
		FROM underground_station t1, underground_line t2
		WHERE t1.underground_line_id = t2.id AND t2.city_id = {$cityId}
		GROUP BY FL
		ORDER BY FL ASC
	";

	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml = "<MetroAlpabet>";
		while ($row = fetch_object($result)) {
			$xml .= "<Element letter = '" . $row->FL . "'/>";
		}
		$xml .= "</MetroAlpabet>";
	}

	return $xml;
}

function getDistrict_XML ($cityId = 1) {
	$xml = "";
	$cityId = intval ($cityId);

	$sql = "SELECT
	    			t1.id, t1.name, t1.id_area, t3.id as station_id, t3.name as station_name
	            FROM district t1
	            LEFT JOIN district_has_underground_station t2 ON (t2.id_district = t1.id)
	            LEFT JOIN underground_station t3 ON (t2.id_station = t3.id)
	            WHERE
	            	t1.id_city = ".$cityId."
	            ORDER BY t1.name ASC";

	$result = Yii::app()->db->cache(3600)->createCommand($sql)->queryAll();
	$districts = [];
	$stations = [];

	foreach ($result as $r) {
		$districts[$r['id']] = $r;
		$stations[$r['id']][] = "<Element id = '".$r['station_id']."'>".$r['station_name']."</Element>";
	}


	foreach ($districts as $district_id => $d) {
		$xml = "<Distinct>";
		$xml .= "<Element id = '" . $district_id . "'>";
		$xml .= "<Title>" . $d['name'] . "</Title>";
		$xml .= "<IdArea>" . $d['id_area'] . "</IdArea>";

		if (isset($stations[$district_id])) {
			$xml .= "<MetroList>" . implode("", $stations[$district_id]) . "</MetroList>";
		}


		$xml .= "</Element>";
		$xml .= "</Distinct>";
	}

	return $xml;
}
    
    
    
    function getMetro_XML ($district) {
    	$xml = "";
    
    	$sql = "SELECT 
	    			t1.id, t1.name
	            FROM underground_station t1, district_has_underground_station t2
	            WHERE 
	            	t1.id = t2.id_station
	            	AND
	            	t2.id_district = ".$district."
	            ORDER BY t1.name ASC";
    	
	    $result = query($sql);
	    if (num_rows($result) > 0) {
	    	$xml = "<MetroList>";
	        while ($row = fetch_object($result)) {
	            $xml .= "<Element id = '".$row->id."'>".$row->name."</Element>";
	        }
	        $xml .= "</MetroList>";
	    }
    
    	return $xml;
    }
    
    
    
    function getArea_XML () {
    	$xml = "";
    
    	$sql = "SELECT 
	    			t1.id, t1.name, t1.full_name
	            FROM area t1
	            ORDER BY t1.name ASC";
    	
	    $result = query($sql);
	    if (num_rows($result) > 0) {
	    	$xml = "<AreaList>";
	        while ($row = fetch_object($result)) {
	            $xml .= "<Element id = '".$row->id."'>".$row->name."</Element>";
	        }
	        $xml .= "</AreaList>";
	    }
    
    	return $xml;
    }
?>