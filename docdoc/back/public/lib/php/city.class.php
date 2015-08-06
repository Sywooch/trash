<?php
use dfs\docdoc\models\CityModel;
use \Yii;

/**
 * Метод, который возвращает xml-строку с названием города
 *
 * @throws CException
 * @return string
 */
function getCityXML()
{
	$city = CityModel::model()->findByPk(getCityId());
	if (!$city) throw new CException('City not find');
	return "<City id='". $city->id_city ."' prefix='" . $city->prefix . "'>". $city->title ."</City>";
}

function getCityId()
{
	$city = (isset(Yii::app()->session['city'])) ? Yii::app()->session['city'] : 1;
	return $city;
}

function  getCityListXML()
{
	$xml = "";

	$sql = "SELECT id_city, title FROM city";
	$result = query($sql);
	$xml .= "<CityList>";
	while ($row = fetch_object($result)) {
		$xml .= "<Element>";
		$xml .= "<Id>" . $row->id_city . "</Id>";
		$xml .= "<Name>" . $row->title . "</Name>";
		$xml .= "</Element>";
	}
	$xml .= "</CityList>";

	return $xml;
}
