<?php
use dfs\docdoc\models\DoctorClinicModel;

function getClinicListXML($cityId = 1)
{
	$xml = '';

	$sql = "SELECT t1.id AS Id, clinic_seo_title AS Name, rewrite_spec_name AS Alias
                FROM sector t1
                INNER JOIN doctor_sector t2 ON t2.sector_id=t1.id
                INNER JOIN doctor_4_clinic t3 ON t3.doctor_id=t2.doctor_id and t3.type = " . DoctorClinicModel::TYPE_DOCTOR . "
                INNER JOIN clinic t4 ON t4.id=t3.clinic_id
                WHERE t4.city_id=" . $cityId . "
                GROUP BY t1.id";
	$result = query($sql);
	$data = array();
	while ($row = fetch_array($result))
		array_push($data, $row);

	$xml .= '<ClinicList>' . arrayToXML($data) . '</ClinicList>';

	return $xml;
}

function getStationListXML($cityId = 1)
{
	$xml = '';

	$xml .= '<StationList>' . arrayToXML(Doctor::getStations($cityId)) . '</StationList>';

	return $xml;
}

function getDistrictListXML($cityId = 1)
{
	$xml = '';

	if ($cityId == 1) {
		$xml .= '<AreaList>';
		$xml .= arrayToXML(getAreas($cityId));
		$xml .= '</AreaList>';

	} else {
		$xml .= '<DistrictList>' . arrayToXML(getDistricts($cityId)) . '</DistrictList>';
	}

	return $xml;
}

function getRegCityListXML($cityId = 1)
{
	$xml = '';

	$xml .= '<RegCityList>' . arrayToXML(getRegCities($cityId)) . '</RegCityList>';

	return $xml;
}

function getAreas($cityId = 1)
{
	$data = array();

	$sql = "SELECT
                    id AS areaId, name AS areaName, rewrite_name AS areaAlias
                FROM area_moscow
                ORDER BY areaId";
	$result = query($sql);

	$i = 0;
	while ($row = fetch_object($result)) {
		$data[$i]['Id'] = $row->areaId;
		$data[$i]['Name'] = $row->areaName;
		$data[$i]['Alias'] = $row->areaAlias;

		$districts = getDistrictsData($cityId, $row->areaId);

		$data[$i]['DistrictList'] = array();
		foreach ($districts as $district) {
			array_push($data[$i]['DistrictList'], array('Id' => $district['Id'], 'Name' => $district['Name'], 'Alias' => $district['Alias']));
		}

		$i++;
	}

	return $data;
}

function getDistrictsData($cityId, $areaId = 0)
{
	$data = array();
	$sqlAdd = "id_city=$cityId";

	if ($areaId <> 0)
		$sqlAdd .= " AND id_area=$areaId";

	$sql = "SELECT id, name, rewrite_name AS alias
                FROM district
                WHERE
                    " . $sqlAdd . "
                ORDER BY name";

	$result = query($sql);
	$i = 0;
	while ($row = fetch_object($result)) {
		$data[$i]['Id'] = $row->id;
		$data[$i]['Name'] = $row->name;
		$data[$i]['Alias'] = $row->alias;
		$i++;
	}

	return $data;
}

function getRegCities($cityId)
{
	$data = array();

	$sql = "SELECT id, name, rewrite_name AS alias
                FROM reg_city
                WHERE city_id=" . $cityId . "
                ORDER BY name";

	$result = query($sql);
	$i = 0;
	while ($row = fetch_object($result)) {
		$data[$i]['Id'] = $row->id;
		$data[$i]['Name'] = $row->name;
		$data[$i]['Alias'] = $row->alias;
		$i++;
	}

	return $data;
}

?>
