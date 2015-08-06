<?php

use dfs\docdoc\models\StreetModel;
use dfs\docdoc\models\StationModel;
use dfs\docdoc\models\ClinicStationModel;

require_once dirname(__FILE__) . "/../../lib/php/user.class.php";
require_once dirname(__FILE__) . "/../../include/common.php";
require_once dirname(__FILE__) . "/../../lib/php/validate.php";

$report = "";

$user = new user();
$user->checkRight4page(array('ADM', 'CNM', 'SOP', 'ACM', 'SAL'), 'simple');
$userId = $user->idUser;

$id = (isset($_POST['id'])) ? checkField($_POST['id'], "i", 0) : '0';
$parentId = (isset($_POST['parentId'])) ? checkField($_POST['parentId'], "i", 0) : 0;
$title = (isset($_POST['title'])) ? checkField($_POST['title'], "t", "") : '';
$shortName = (isset($_POST['shortName'])) ? checkField($_POST['shortName'], "t", "") : '';
$alias = (isset($_POST['alias'])) ? checkField($_POST['alias'], "t", "") : '';
$url = (isset($_POST['url'])) ? checkField($_POST['url'], "t", "") : '';
$contactName = (isset($_POST['contactName'])) ? checkField($_POST['contactName'], "t", "") : '';
$age = (isset($_POST['age'])) ? checkField($_POST['age'], "t", "multy") : 'multy';
$city = (isset($_POST['city'])) ? checkField($_POST['city'], "t", "Москва") : 'Москва';
$districtId = (isset($_POST['districtId'])) ? checkField($_POST['districtId'], "i", 0) : 0;
$street = (isset($_POST['cityStreet'])) ? checkField($_POST['cityStreet'], "t", "") : '';
$address = (isset($_POST['addressEtc'])) ? checkField($_POST['addressEtc'], "t", "") : '';
$metroList = (isset ($_POST['metro'])) ? rtrim(trim($_POST['metro']), ',') : '';
$metroList = (!empty($metroList)) ? explode(",", $metroList) : array();
$longitude = (isset($_POST['longitude'])) ? checkField($_POST['longitude'], "f", "") : '';
$latitude = (isset($_POST['latitude'])) ? checkField($_POST['latitude'], "f", "") : '';
$logoPath = (isset($_POST['logoPath'])) ? checkField($_POST['logoPath'], "t", "") : '';


$description = (isset($_POST['description'])) ? checkField($_POST['description'], "h", "") : '';
$shortDescription = (isset($_POST['shortDescription'])) ? checkField($_POST['shortDescription'], "h", "") : '';
$comment = (isset($_POST['operatorComment'])) ? checkField($_POST['operatorComment'], "t", "") : '';
$wayOnFoot = (isset($_POST['wayOnFoot'])) ? checkField($_POST['wayOnFoot'], "h", "") : '';
$wayOnCar = (isset($_POST['wayOnCar'])) ? checkField($_POST['wayOnCar'], "h", "") : '';

$email = (isset($_POST['email'])) ? checkField($_POST['email'], "t", "") : '';
$status = (isset($_POST['statusClinic'])) ? checkField($_POST['statusClinic'], "i", 2) : 2;
$isDiagnostic = (isset($_POST['isDiagnostic'])) ? checkField($_POST['isDiagnostic'], "t", "no") : "no";
$isClinic = (isset($_POST['isClinic'])) ? checkField($_POST['isClinic'], "t", "no") : "no";
$isPrivatDoctor = (isset($_POST['isPrivatDoctor'])) ? checkField($_POST['isPrivatDoctor'], "t", "no") : "no";
$asteriskPhone = (isset($_POST['asteriskPhone'])) ? checkField($_POST['asteriskPhone'], "t", "") : "";
$clinicPhone = (isset($_POST['clinicPhone'])) ? checkField($_POST['clinicPhone'], "t", "") : "";

$weekdays = (isset($_POST['weekdays'])) ? checkField($_POST['weekdays'], "t", "") : '';
$weekend = (isset($_POST['weekend'])) ? checkField($_POST['weekend'], "t", "") : '';
$saturday = (isset($_POST['saturday'])) ? checkField($_POST['saturday'], "t", "") : '';
$sunday = (isset($_POST['sunday'])) ? checkField($_POST['sunday'], "t", "") : '';


$label = (isset($_POST['label'])) ? $_POST['label'] : array();
$phones = (isset($_POST['phones'])) ? $_POST['phones'] : array();
$idDC = (isset($_POST['isDC'])) ? checkField($_POST['isDC'], 't', 'no') : 'no';

$metro = explode(",", trim($_POST['metro']));
$metroListStr = "";
foreach ($metro as $key => $data) {
	if (!empty($data)) {
		$metroListStr .= "м. " . $data . ", ";
	}
}
$oldAddress = $metroListStr . $street . ", " . $address;
$cityId = getCityId();

/*	Валидация	*/
if (empty($title)) {
	echo htmlspecialchars(json_encode(array('error' => 'Необходимо ввести название клиники')), ENT_NOQUOTES);
	exit;
}
if (!empty($email) && !checkEmail($email)) {
	echo htmlspecialchars(json_encode(array('error' => 'Ошибки в поле Email')), ENT_NOQUOTES);
	exit;
}
if ($isDiagnostic == 'no' && $isClinic == 'no' && $isPrivatDoctor == 'no') {
	echo htmlspecialchars(json_encode(array('error' => 'Необходимо указать профиль организации: клиника / диагностический центр')), ENT_NOQUOTES);
	exit;
}
if (empty($districtId)) {
	echo htmlspecialchars(json_encode(array('error' => 'Необходимо выбрать район')), ENT_NOQUOTES);
	exit;
}

$street = trim($street);
$streetId = 'NULL';

if ($street !== '') {
	$streetModel = StreetModel::model()->
		inCity($cityId)->
		searchTitle($street)->
		find();

	if ($streetModel === null) {
		$streetModel = StreetModel::newStreet($cityId, $street);
		$streetModel->save();
	}
	$streetId = $streetModel->street_id;
}

$sqlAdd = "";
// Логотип
$sqlAdd .= emptyToNull($logoPath, "logoPath");

// Время работы
$sqlAdd .= emptyToNull($weekdays, "weekdays_open");
$sqlAdd .= emptyToNull($weekend, "weekend_open");
$sqlAdd .= emptyToNull($saturday, "saturday_open");
$sqlAdd .= emptyToNull($sunday, "sunday_open");

// Телефоны
$sqlAdd .= emptyToNull(formatPhone4DB($clinicPhone), "phone");

// Текст как добраться пешком / на машине
$sqlAdd .= emptyToNull($wayOnFoot, "way_on_foot");
$sqlAdd .= emptyToNull($wayOnCar, "way_on_car");

$alias = setAliasForClinic($alias, $title, $id);
$sqlAdd .= "rewrite_name='" . $alias . "',";

$result = query("START TRANSACTION");
if ($id > 0) {
	$sql = "UPDATE `clinic` SET
					name = '" . $title . "',
					short_name = '" . $shortName . "',
					age_selector = '" . $age . "',
					url = '" . $url . "',
					contact_name= '" . $contactName . "',
					email = '" . $email . "',
					city = '" . $city . "',
					street = '" . $street . "',
					street_id = " . $streetId . ",
					house = '" . $address . "',
					latitude = '" . $latitude . "',
					longitude = '" . $longitude . "',
					description = '" . $description . "',
					shortDescription = '" . $shortDescription . "',
					operator_comment = '" . $comment . "',
					isDiagnostic = '" . $isDiagnostic . "',
					isClinic = '" . $isClinic . "',
					isPrivatDoctor = '" . $isPrivatDoctor . "',
					city_id = " . $cityId . ",
					district_id = " . $districtId . ",
					" . $sqlAdd . "
					status = '" . $status . "'
				WHERE id=" . $id;
	queryJS($sql, 'Ошибка изменения данных ');

	$sql = "SELECT count(*) as cnt
					FROM `clinic_address`
					WHERE clinic_id = " . $id . "
						AND isNew = 'yes'";
	$result = query($sql);
	$row = fetch_object($result);
	if ($row->cnt == 1) {
		$sql = "UPDATE `clinic_address` SET
					address = '" . $oldAddress . "'
					WHERE clinic_id = " . $id . "
					AND isNew = 'yes'";
		queryJS($sql, 'Ошибка добавления адреса');
	} else if ($row->cnt > 1) {
		$sql = "DELETE FROM `clinic_address`
				WHERE clinic_id = " . $id;
		queryJS($sql, 'Ошибка удаления дублей адреса');

		$sql = "INSERT INTO `clinic_address` SET
					address = '" . $oldAddress . "',
					clinic_id = " . $id . ",
					isNew = 'yes'";
		queryJS($sql, 'Ошибка добавления адреса');
	} else if ($row->cnt == 0) {
		$sql = "INSERT INTO `clinic_address` SET
					address = '" . $oldAddress . "',
					clinic_id = " . $id . ",
					isNew = 'yes'";
		queryJS($sql, 'Ошибка добавления адреса');
	}

	$msg = "Модификация данных клиники id = $id";
	$log = new logger();
	$log->setLog($user->idUser, 'U_CLN', $msg);

} else {
	/*		Новая запись	*/
	$sql = "INSERT INTO `clinic` SET
					name = '" . $title . "',
					short_name = '" . $shortName . "',
					parent_clinic_id = '" . $parentId . "',
					age_selector = '" . $age . "',
					url = '" . $url . "',
					contact_name= '" . $contactName . "',
					email = '" . $email . "',
					city = '" . $city . "',
					street = '" . $street . "',
					street_id = " . $streetId . ",
					house = '" . $address . "',
					latitude = '" . $latitude . "',
					longitude = '" . $longitude . "',
					description = '" . $description . "',
					shortDescription = '" . $shortDescription . "',
					operator_comment = '" . $comment . "',
					isDiagnostic = '" . $isDiagnostic . "',
					isClinic = '" . $isClinic . "',
					isPrivatDoctor = '" . $isPrivatDoctor . "',
					city_id = " . $cityId . ",
					district_id = " . $districtId . ",
					" . $sqlAdd . "
					status = '" . $status . "'";
	queryJS($sql, 'Ошибка добавления данных');
	$id = legacy_insert_id();


	$sql = "INSERT INTO `clinic_address` SET
					address = '" . $oldAddress . "',
					clinic_id = " . $id . ",
					isNew = 'yes'";
	queryJS($sql, 'Ошибка добавления адреса');


	$msg = "Заведение клиники $title / id = $id";
	$log = new logger();
	$log->setLog($user->idUser, 'C_CLN', $msg);
}

$clinic = \dfs\docdoc\models\ClinicModel::model()->findByPk($id);
$clinic->asterisk_phone = $asteriskPhone;

if (!$clinic->save()) {
	query("rollback");
	$errors = $clinic->getErrors();
	echo htmlspecialchars(json_encode(array('error' => array_shift($errors))), ENT_NOQUOTES);
	exit;
}

setMetro($id, $metroList);
setPhones($id, $label, $phones);


$result = query("commit");

echo htmlspecialchars(json_encode(array('status' => 'success', 'id' => $id, 'parentId' => $parentId)), ENT_NOQUOTES);

/**
 * Метод, который устанавлвает алиас
 *
 * @param string $alias
 * @param string $title
 * @param int $id
 * @return string
 */
function setAliasForClinic($alias, $title, $id)
{
	$alias = htmlspecialchars(trim($alias));
	$alias = empty($alias) ? generateAlias($title, $id) : $alias;
	return $alias;
}

/**
 * Метод, который в отсутствии алиаса генерирует его на основе имени
 *
 * @param string $title
 * @param int $id
 * @return string
 */
function generateAlias($title, $id)
{
	$alias = RussianTextUtils::translit($title);
	$sql = "SELECT *
				FROM clinic
				WHERE rewrite_name
				LIKE '" . $alias . "%' AND id<>" . $id;
	$result = query($sql);
	if (num_rows($result)) {
		$busyAliases = array();
		while ($row = fetch_object($result)) {
			$busyAliases[] = $row->rewrite_name;
		}
		$newAlias = $alias;
		$i = 1;
		while (in_array($newAlias, $busyAliases)) {
			$newAlias = $alias . '_' . $i;
			$i++;
		}
		$alias = $newAlias;
	}
	return $alias;
}

/**
 * Устанавливает метро
 *
 * @param int    $clinicId        идентификатор клиники
 * @param array $stationNameList массив имен станций метро
 */
function setMetro($clinicId, $stationNameList = array())
{
	global $cityId;
	$clinicId = intval($clinicId);

	if (count($stationNameList) > 0 && $clinicId > 0) {
		ClinicStationModel::model()->deleteAllByAttributes(['clinic_id' => $clinicId]);
		foreach ($stationNameList as $station) {
			$stationModel = null;
			$result = preg_match("/\[[0-9]+\]/", $station, $matches);
			if ($result == 1) {
				$stationId = intval(trim(trim($matches[0], '['), ']'));
				$stationModel = StationModel::model()->findByPk($stationId);
			} elseif ($result == 0) {
				$stationModel = StationModel::model()
					->inCity($cityId)
					->searchByName($station)
					->find();
			}

			if (!is_null($stationModel)) {
				$clinicStation = new ClinicStationModel();
				$clinicStation->clinic_id = $clinicId;
				$clinicStation->undegraund_station_id = $stationModel->id;
				$clinicStation->save();
			} else {
				$result = query("rollback");
				echo htmlspecialchars(
					json_encode(array('error' => 'Ошибка в названии станции ' . $station)),
					ENT_NOQUOTES
				);
				exit;
			}
		}
	}
}

function setPhones($clinicId, $labelList = array(), $phonesList = array())
{
	$clinicId = intval($clinicId);

	if (count($labelList) > 0 && $clinicId > 0 && count($phonesList) > 0) {
		$sql = "DELETE FROM clinic_phone WHERE clinic_id = $clinicId ";
		queryJS($sql, 'Ошибка удаления телефонов');

		$oldPhone = "";
		foreach ($phonesList as $key => $phone) {

			$element = modifyPhone($phone);

			$lab = $labelList[$key];

			if (!empty($element)) {
				$sql = "INSERT INTO clinic_phone SET
								clinic_id = $clinicId,
								number_p = '" . $element . "',
								label = '" . $lab . "'";
				//echo $sql;
				//$log = new msgLog($sql, 35, $clinicId );
				queryJS($sql, 'Ошибка добавления телефона');
				$oldPhone .= formatPhone($element) . "; ";
			}
		}
		if (!empty ($oldPhone)) {
			$oldPhone = rtrim($oldPhone, "; ");
		}
		$sql = "UPDATE clinic SET
						phone_appointment = '" . $oldPhone . "'
					WHERE id =  " . $clinicId;
		//$log = new msgLog($sql);
		queryJS($sql, 'Ошибка изменения старого номера телефона');

	}

}
