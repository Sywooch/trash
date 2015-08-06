<?php
use dfs\docdoc\models\CityModel;
use dfs\docdoc\models\DistrictModel;
use dfs\docdoc\models\ClinicModel;

	/**
	 * Генерация xml для клиники
	 *
	 * @param $alias
	 *
	 * @return string
	 */
function getClinicByAliasXML($alias)
{
	$xml = "";

	$sql = "SELECT
				t1.id,
				t1.name,
				t1.short_name,
				t1.rewrite_name,
				t1.status,
				t1.phone,
				t1.phone_appointment,
				t1.asterisk_phone,
				t1.url,
				t1.rating,
				t1.age_selector as age,
				t1.rating_total,
				t1.longitude, t1.latitude,
				t1.city,
				t1.street,
				t1.house,
				t1.shortDescription as short_description,
				t1.logoPath,
				t1.district_id
		FROM clinic  t1
		WHERE
			t1.status IN (" . ClinicModel::STATUS_ACTIVE . ", " . ClinicModel::STATUS_BLOCKED . ")
				AND
			t1.id='" . $alias . "'
				OR
			t1.rewrite_name='$alias'";

	$result = query($sql);

	if (num_rows($result) > 0) {
		$row = fetch_object($result);
		$xml .= "<Id>" . $row->id . "</Id>";
		$xml .= "<Title><![CDATA[" . $row->name . "]]></Title>";
		$xml .= "<ShortName><![CDATA[" . $row->short_name . "]]></ShortName>";
		$xml .= "<RewriteName><![CDATA[" . $row->rewrite_name . "]]></RewriteName>";
		$xml .= "<URL><![CDATA[" . $row->url . "]]></URL>";
		$xml .= "<Status>{$row->status}</Status>";

		if (!empty($row->logoPath)) {
			$xml .= "<Logo><![CDATA[" . $row->logoPath . "]]></Logo>";
		} else {
			$xml .= "<Logo>logo_default.jpg</Logo>";
		}

		$xml .= "<Rating>" . $row->rating . "</Rating>";
		$xml .= "<TotalRating>" . $row->rating_total . "</TotalRating>";
		$xml .= "<Phone digit=\"" . formatPhone4DB($row->phone) . "\">" . formatPhone($row->phone) . "</Phone>";

		$xml .= "<AsteriskPhone digit=\"" . formatPhone4DB($row->asterisk_phone) . "\">" .
					formatPhone($row->asterisk_phone) .
				"</AsteriskPhone>";

		$xml .= "<PhoneAppointment digit=\"" . formatPhone4DB($row->phone_appointment) . "\">" .
					formatPhone($row->phone_appointment) .
				"</PhoneAppointment>";

		$xml .= "<Age>" . $row->age . "</Age>";
		$xml .= "<Longitude>" . $row->longitude . "</Longitude>";
		$xml .= "<Latitude>" . $row->latitude . "</Latitude>";
		$xml .= "<City>" . $row->city . "</City>";
		$xml .= "<House>" . $row->house . "</House>";
		$xml .= "<Street>" . $row->street . "</Street>";
		$xml .= "<Description>" . $row->short_description . "</Description>";
		$xml .= getMetroXML($row->id);
		$xml .= getClinicSchedule($row->id);

		$district = DistrictModel::model()->findByPk($row->district_id);
		$district && $xml .= '<Area>' . $district->name . '</Area>';
	}

	return $xml;
}

	/**
	 * Генерация xml со списком метро для клиники
	 *
	 * @param int $clinic
	 *
	 * @return string
	 */
	function getMetroXML ($clinic) {
		$xml = "";
		$metro = [];

		$sql = "	SELECT
						t1.undegraund_station_id AS id,
						ust.name,
						ust.rewrite_name,
						ust.underground_line_id
					FROM underground_station_4_clinic  t1, underground_station ust
					WHERE
						t1.clinic_id = ".$clinic."
						AND
						t1.undegraund_station_id = ust.id
					ORDER BY ust.underground_line_id DESC, ust.name";

		$result = query($sql);
    	if (num_rows($result) > 0) {
        	$xml  .= "<MetroList>";
  			while ($row = fetch_object($result)) {
  				$xml  .= '<Element id="'. $row -> id .'">';
  				$xml  .= '<Id>'. $row -> id .'</Id>';
				$xml  .= '<Name>'. $row -> name .'</Name>';
				$xml  .= '<Alias>'. $row -> rewrite_name .'</Alias>';
				$xml  .= '<LineId>'. $row -> underground_line_id .'</LineId>';
				$xml  .= '</Element>';
				$metro[] = $row->name;
			}
			$xml .= "</MetroList>";
		}
		$xml .= '<Metro>' . implode(', ', $metro) . '</Metro>';
		return $xml;
	}




	function getClinicSchedule($clinic ) {
		$xml = "";

		$sql = "SELECT
					t1.id,  
					t1.week_day, 
					TIME_FORMAT(t1.start_time, '%H:%i') AS start_time,
					TIME_FORMAT(t1.end_time, '%H:%i') AS end_time  
				FROM clinic_schedule  t1
				WHERE t1.clinic_id = ".$clinic."
				GROUP BY t1.week_day";
		$result = query($sql);
		if (num_rows($result) > 0) {
			$xml  .= "<Schedule>";
			while ($row = fetch_object($result)) {
				$xml  .= "<Element day='".$row->week_day."' startTime='".$row->start_time."' endTime='".$row->end_time."' />";
			}
			$xml  .= "</Schedule>";
		}

		return $xml;
	}

	/*
	 * Генерирует xml со списком активных городов
	 *
	 * @return string
	 */
	function getActiveCitiesXml()
	{
		$cityId = Yii::app()->city->getCityId();

		$cityList = CityModel::model()
			->active(true)
			->findAll(['order' => 'title']);

		$xmlString = "<CityList>";

		foreach ($cityList as $c) {
			$xmlString .= sprintf(
				"<Element id='%s' prefix='%s' title_genitive='%s' selected='%s'>%s</Element>",
				$c->id_city,
				$c->prefix,
				$c->title_genitive,
				$c->id_city == $cityId,
				$c->title
			);
		}

		$xmlString .= "</CityList>";

		return $xmlString;
	}
