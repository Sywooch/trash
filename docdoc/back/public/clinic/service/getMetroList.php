<?php
require_once dirname(__FILE__) . "/../../include/common.php";
require_once dirname(__FILE__) . "/../../lib/php/validate.php";


header('Content-Type: text/html; charset=utf-8');

$q = Yii::app()->request->getQuery('q', null);

$city = (isset($_GET['cityId'])) ? checkField($_GET['cityId'], "i", 1) : 1;
$withId = (isset($_GET['withId'])) ? checkField($_GET['withId'], "t", 'no') : 'no';

if ($q) {
	$q = strtr($q, array('%' => '\%', '_' => '\_'));

	$sql = "
			SELECT t1.name as title, t1.id as station_id
			FROM underground_station t1, underground_line t2
			WHERE
				t1.underground_line_id = t2.id
				AND t2.city_id = :city
				AND LOWER(t1.name) LIKE LOWER(concat(:q, '%'))
			ORDER BY t1.name
		";

	/** @var CDBConnection $connection */
	$connection = Yii::app()->getDb();
	$command = $connection->createCommand($sql);
	$command->bindParam(':city', $city, PDO::PARAM_INT);
	$command->bindParam(':q', $q, PDO::PARAM_STR);

	$reader = $command->query();

	foreach ($reader as $row) {
		if ($withId == 'yes') {
			print $row['title'] . "[" . $row['station_id'] . "]|" . $row['station_id'] . "\n";
		} else {
			print $row['title'] . "|" . $row['station_id'] . "\n";
		}
	}

}
