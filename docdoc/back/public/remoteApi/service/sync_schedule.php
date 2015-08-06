<?php

header('Content-type: text/html; charset=UTF-8');

require_once dirname(__FILE__)."/../../include/common.php";
require_once dirname(__FILE__)."/../../lib/php/schedule.class.php";
require_once dirname(__FILE__).'/../lib/OnClinic.php';
require_once dirname(__FILE__).'/../lib/MedSoft.php';


$data = array();
$dataTmp = array();


$api = new OnClinic;
$dataTmp = $api->getSchedule(strtotime('21.09.2013'));
$data = array_merge($data, $dataTmp);

$api = new MedSoft();
$dataTmp = $api->getSchedule();
$data = array_merge($data, $dataTmp);

var_dump($data);

foreach($data as $item) {
    $weekDay = date('w', strtotime($item['date']));
    $weekDay == 0 ? $weekDay = 7 : $weekDay += 1;
    $schedule = new Schedule($item['clinicId'], $item['doctorId']);
    $schedule->setDoctorSheduleByWeekDay($weekDay, $item['times']);
}

?>
