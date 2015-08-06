<?php
use dfs\docdoc\models\ClinicModel;

function getDiagnosticListXML()
{
	$xml = "";

	$sql = "SELECT
                    id, rewrite_name,
                    CASE WHEN reduction_name = '' THEN name ELSE reduction_name
                    END AS name
                FROM diagnostica t1
                WHERE parent_id=0
                ORDER BY sort, name";

	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml .= '<DiagnosticList>';
		while ($row = fetch_object($result)) {
			$xml .= '<Element id="' . $row->id . '">';
			$xml .= '<Name>' . $row->name . '</Name>';
			$xml .= '<RewriteName>' . $row->rewrite_name . '</RewriteName>';
			$xml .= '</Element>';
		}
		$xml .= '</DiagnosticList>';
	}

	return $xml;
}

/**
 * Список центров
 *
 * @param array $params
 * @param bool $detailed
 * @return array
 */
function getDiagnosticList($params = array(), $detailed = true)
{
	$data = array();

	$limit = isset($params['step']) ? $params['step'] : 1000;
	$offset = isset($params['startPage']) ? $params['startPage'] : 0;
	$order = "t.sort4commerce, spPriceSort, t.id DESC";

	$diagnostica = !empty($params['diagnostica']) ? $params['diagnostica'] : null;
	$diagnostica = !empty($params['subDiagnostica']) ? $params['subDiagnostica'] : $diagnostica;

	$clinicModel = ClinicModel::model()
		->active()
		->inCity(Yii::app()->city->getCityId())
		->onlyDiagnostic();
	if (count($params) > 0) {

		if (!is_null($diagnostica)) {
			$clinicModel->searchByDiagnostics(array($diagnostica), false);
		} else {
			$clinicModel->with('diagnostics');
		}

		if (isset($params['coord']) && count($params['coord']) == 2) {
			$coord = $params['coord'];
			$clinicModel->searchByCoordinates($coord[0][1], $coord[0][0], $coord[1][1], $coord[1][0]);
		}

		if (isset($params['id']) && $params['id'] > 0) {
			$clinicModel->except(array($params['id']));
		}

		if (isset($params['sortRS']) && !empty($params['sortRS'])) {
			switch ($params['sortRS']) {
				case "asc" :
					$order = "sortPrice ASC, t.sort4commerce, t.id DESC";
					break;
				case "des" :
					$order = "sortPrice DESC, t.sort4commerce, t.id DESC";
					break;
				default:
					break;
			}
		}
	}

	$clinics = $clinicModel->findAll(array(
		'select' => "t.*,
					(CASE WHEN diagnostics_diagnostics.special_price > 0 THEN 0 ELSE 1 END) AS spPriceSort,
					(CASE
						WHEN diagnostics_diagnostics.special_price > 0
						THEN diagnostics_diagnostics.special_price
						ELSE diagnostics_diagnostics.price
					END) AS sortPrice",
		'order' => $order,
		'group' => 't.id',
		'limit' => $limit,
		'offset' => $offset,
		'together' => true
	));

	if (isset($params['id']) && $params['id'] > 0) {
		$selectedClinic = ClinicModel::model()->findByPk($params['id']);
		array_unshift($clinics, $selectedClinic);
	}

	foreach ($clinics as $clinic) {
		$tmp['Id'] = $clinic->id;
		$tmp['Long'] = $clinic->longitude;
		$tmp['Lat'] = $clinic->latitude;
		if ($detailed) {
			$tmp['Title'] = $clinic->name;
			$tmp['Rewrite'] = $clinic->rewrite_name;
			$tmp['URL'] = $clinic->url;
			$tmp['Address'] = $clinic->street . ", " . $clinic->house;
			$tmp['AddressMap'] = $clinic->street . ", " . $clinic->house;
			$tmp['Logo'] = $clinic->logoPath;
			$tmp['Descr'] = $clinic->shortDescription;
			$tmp['Metro'] = $clinic->getStations();
			$tmp['Diagnostics'] = $clinic->getDiagnostics($diagnostica);
		}
		array_push($data, $tmp);
	}
	return $data;
}

function getDiagnosticCenter($id, $params = array())
{
	$xml = "";
	$diagnosticList = "";
	$countId = 0;

	$id = intval($id);

	if ($id > 0) {
		$sql = "SELECT
						t1.id, t1.name,  
						CONCAT(t1.street, ', ', t1.house) AS address, t1.url,
						t1.latitude, t1.longitude, t1.rewrite_name,
						t1.logoPath, t1.shortDescription as descr,
						t1.asterisk_phone AS center_phone, t1.weekdays_open, t1.weekend_open, t1.saturday_open, t1.sunday_open
					FROM clinic  t1
					WHERE
						t1.id = " . $id;
		//echo $sql;
		$result = query($sql);
		if (num_rows($result) == 1) {
			$row = fetch_object($result);
			$xml .= "<DCenter id=\"" . $row ->id . "\">";
			$xml .= "<Title><![CDATA[" . $row ->name . "]]></Title>";
			$xml .= "<URL><![CDATA[" . $row ->url . "]]></URL>";
			$xml .= "<Rewrite><![CDATA[" . $row ->rewrite_name . "]]></Rewrite>";
			$xml .= "<Address><![CDATA[" . $row ->address . "]]></Address>";
			$xml .= "<AddressMap><![CDATA[" . $row ->address . "]]></AddressMap>";
			$xml .= "<Logo><![CDATA[" . $row ->logoPath . "]]></Logo>";
			$xml .= "<Descr><![CDATA[" . $row ->descr . "]]></Descr>";
			$xml .= "<Phone>" . formatPhone($row ->center_phone, $startPrefix = "8") . "</Phone>";
			$xml .= "<WorkWeek>" . $row ->weekdays_open . "</WorkWeek>";
			$xml .= "<WeekEnd>" . $row ->weekend_open . "</WeekEnd>";
			$xml .= "<Saturday>" . $row ->saturday_open . "</Saturday>";
			$xml .= "<Sunday>" . $row ->sunday_open . "</Sunday>";
			$xml .= "<Long>" . $row ->longitude . "</Long>";
			$xml .= "<Lat>" . $row ->latitude . "</Lat>";
			if (isset($params['subDiagnostica']) && !empty($params['subDiagnostica'])) {
				$xml .= getDiagnostic4Clinic($row ->id, 1, $params['subDiagnostica']);
			} else if (isset($params['diagnostica']) && !empty($params['diagnostica'])) {
				$countId = 0;
				$diagnosticList = "";
				$idList = getDiagnosticArrayById($params['diagnostica']);
				list($countId, $diagnosticList) = array2str($idList);
				$xml .= getDiagnostic4Clinic($row ->id, $countId, $diagnosticList);
			} else {
				$xml .= getDiagnostic4Clinic($row ->id);
			}
			$xml .= getDCMetroList($row ->id);
			$xml .= "</DCenter>";
		}
	}
	return $xml;

}


function getDCDeatail($id, $diagnosticList = "")
{
	$xml = "";
	$sqlAdd = " ";

	if (!empty($diagnosticList)) {
		$sqlAdd .= " AND t2.diagnostica_id IN '" . $diagnosticList . "' ";
	}


	$sql = "SELECT
					t2.price, t3.name 
				FROM clinic  t1, diagnostica4clinic t2
				LEFT JOIN diagnostica t3 ON (t2.diagnostica_id = t3.id)
				WHERE
					t1.id = $id
					AND
					t1.id = t2.clinic_id
				" . $sqlAdd . "
				ORDER BY t1.id";

	$result = query($sql);
	if (num_rows($result) > 0) {

		while ($row = fetch_object($result)) {
			$xml .= "<Diagnostica price=\"" . round($row ->price) . "\">" . $row ->name . "</Diagnostica>";
		}
	}
	return $xml;
}


function getDiagnostic4Clinic($clinicid, $countId = 0, $idList = "")
{
	$xml = "";

	$clinicid = intval($clinicid);

	if ($clinicid > 0) {
		$sqlAdd = "";

		if ($countId > 1) {
			$sqlAdd .= " AND t1.diagnostica_id IN ( " . $idList . ")";
		} else if ($countId == 1) {
			$sqlAdd .= " AND t1.diagnostica_id = '" . $idList . "'";
		}

		$sql = "	SELECT
						t1.diagnostica_id as id, t1.price, t1.special_price
					FROM diagnostica4clinic t1
                                        INNER JOIN diagnostica t2 ON t2.id=t1.diagnostica_id
					WHERE 
						t1.clinic_id = $clinicid
						AND t1.price > 0
						" . $sqlAdd . "
					GROUP BY id
					ORDER BY t2.sort, t2.name";
		//echo $sql;
		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml .= "<Diagnostics>";
			while ($row = fetch_object($result)) {
				$xml .= "<Element id=\"" . $row ->id . "\">";
				$xml .= "<Price>" . round($row ->price) . "</Price>";
				$xml .= "<SpecialPrice>" . round($row ->special_price) . "</SpecialPrice>";
				$xml .= "</Element>";
			}
			$xml .= "</Diagnostics>";
		}
	}

	return $xml;
}

function getDiagnosticArrayById($id)
{
	$idList = array();

	$id = intval($id);

	if ($id > 0) {
		$sql = "SELECT
						t1.id, t1.parent_id, t1.rewrite_name
					FROM diagnostica  t1
					WHERE parent_id = $id
					ORDER BY name, t1.id";
		$result = query($sql);
		if (num_rows($result) > 0) {
			while ($row = fetch_object($result)) {
				array_push($idList, $row ->id);
			}
		} else {
			array_push($idList, $id);
		}
	}

	return $idList;
}


function array2str($list = array())
{
	$str = "";

	if (count($list) == 1) {
		$str = $list[0];
	} else if (count($list) > 1) {
		foreach ($list as $key => $data) {
			$str .= $data . ", ";
		}
		$str = rtrim($str, ", ");
	}
	return array(count($list), $str);
}


function getDCMetroList($id)
{
	$xml = "";
	$sqlAdd = " ";

	$sql = "SELECT
					t2.id, t2.name, t2.underground_line_id as lineId
				FROM underground_station_4_clinic t1, underground_station t2
				WHERE
					t1.clinic_id = " . $id . "
					AND
					t1.undegraund_station_id = t2.id
				ORDER BY t2.name";
	$result = query($sql);
	if (num_rows($result) > 0) {
		while ($row = fetch_object($result)) {
			$xml .= "<Metro id=\"" . $row ->id . "\"  lineId=\"" . $row ->lineId . "\">" . $row ->name . "</Metro>";
		}
	}
	return $xml;
}


/*	Справочник диагностик */
function getDiagnosticDict($parent = 0)
{
	$xml = "";

	$sql = "	SELECT
					id, name, title, parent_id, reduction_name, rewrite_name
				FROM diagnostica 
				WHERE 
					parent_id = $parent 
				ORDER BY sort, name";
	//echo $sql;
	$result = query($sql);
	if (num_rows($result) > 0) {
		$xml .= "<DiagnosticList>";
		while ($row = fetch_object($result)) {
			$xml .= "<Element id=\"" . $row ->id . "\">";
			$xml .= "<Name>" . trim($row ->name) . "</Name>";
			$xml .= "<ReductionName>" . trim($row ->reduction_name) . "</ReductionName>";
			$xml .= "<RewriteName>" . $row ->rewrite_name . "</RewriteName>";
			$xml .= "<Title>" . $row ->title . "</Title>";
			if ($row ->parent_id == 0) {
				$xml .= getDiagnosticDict($row ->id);
			}
			$xml .= "</Element>";
		}
		$xml .= "</DiagnosticList>";
	}

	return $xml;
}

function getPriceListXML($clinicId)
{
	$xml = '';

	$sql = "SELECT
                    t3.id AS pid, t2.id,
                    t3.name AS name, t2.name AS subName,
                    ROUND(t1.price) AS price, ROUND(t1.special_price) AS special_price,
                    case when t3.sort is null then t2.sort  else t3.sort end as t3sort,
					t2.sort t2sort
                FROM diagnostica4clinic t1
                LEFT JOIN diagnostica t2 ON t2.id=t1.diagnostica_id
                LEFT JOIN diagnostica t3 ON t3.id=t2.parent_id
                WHERE t1.clinic_id=" . $clinicId . "
                    AND t2.name IS NOT NULL
                    AND t1.price>0
                ORDER BY t3sort, t2sort";

	$result = query($sql);
	$priceList = array();
	if (num_rows($result) > 0) {
		while ($row = fetch_object($result)) {
			if ($row->pid > 0) {
				$priceList[$row->pid]['Name'] = $row->name;
				$priceList[$row->pid]['Sub'][$row->id]['Name'] = $row->subName;
				$priceList[$row->pid]['Sub'][$row->id]['Price'] = $row->price;
				$priceList[$row->pid]['Sub'][$row->id]['SpecialPrice'] = $row->special_price;
			} else {
				$priceList[$row->id]['Name'] = $row->subName;
				$priceList[$row->id]['Sub'] = array();
				$priceList[$row->id]['Price'] = $row->price;
				$priceList[$row->id]['SpecialPrice'] = $row->special_price;
			}
		}
	}

	$xml .= '<PriceList>';
	foreach ($priceList as $group) {

		if (count($group['Sub']) > 0) {
			$xml .= '<Group hasSub="true">';
			$xml .= '<Name>' . $group['Name'] . '</Name>';
			$xml .= '<Sub>';
			foreach ($group['Sub'] as $item) {
				$xml .= '<Element>';
				$xml .= '<Name>' . $item['Name'] . '</Name>';
				$xml .= '<Price>' . $item['Price'] . '</Price>';
				$xml .= '<SpecialPrice>' . $item['SpecialPrice'] . '</SpecialPrice>';
				$xml .= '</Element>';
			}
			$xml .= '</Sub>';
		} else {
			$xml .= '<Group hasSub="false">';
			$xml .= '<Name>' . $group['Name'] . '</Name>';
			$xml .= '<Price>' . $group['Price'] . '</Price>';
			$xml .= '<SpecialPrice>' . $group['SpecialPrice'] . '</SpecialPrice>';
		}

		$xml .= '</Group>';
	}
	$xml .= '</PriceList>';

	return $xml;
}

?>
