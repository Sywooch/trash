<?php
/**
 * Получает XML из метро в алфавитном порядке
 *
 * @param int $cityId идентификатор города
 *
 * @return string
 */
function getMetroListByAZ_XML($cityId = 1)
{
	$xml = "";
	$cityId = intval($cityId);

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
			$xml .= "<Element letter = '" . $row->FL . "' id = '" . $row->id . "'>" . $row->name . "</Element>";
		}
		$xml .= "</MetroListByAZ>";
	}

	return $xml;
}

/**
 * Получает XML списка метро в алфавитном порядке по идентификатору города
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
	    			t1.id, t1.name, t1.id_area
	            FROM district t1
	            WHERE 
	            	t1.id_city = ".$cityId."
	            ORDER BY t1.name ASC";
    	
	    $result = query($sql);
	    if (num_rows($result) > 0) {
	    	$xml = "<Distinct>";
	        while ($row = fetch_object($result)) {
	            $xml .= "<Element id = '".$row->id."'>";
	            $xml .= "<Title>".$row->name."</Title>";
	            $xml .= "<IdArea>".$row->id_area."</IdArea>";
	            $xml .= getMetro_XML($row->id);
	            $xml .= "</Element>";
	        }
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