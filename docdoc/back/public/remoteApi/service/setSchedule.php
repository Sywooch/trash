<?php

require_once	dirname(__FILE__)."/../../include/common.php";
require_once	dirname(__FILE__)."/../lib/MedSoft.php";
require_once	dirname(__FILE__)."/../../lib/php/schedule.class.php";


// Set schedule for doctors from MedSoft 
$api = new MedSoft();
$data = $api->getSchedule(time());

$schedule = new Schedule;
foreach($data as $item) {
    $weekDay = date('w', strtotime($item['date']));
    $weekDay == 0 ? $weekDay = 7 : $weekDay += 1;
    $schedule->setDoctor($item['doctorId']);
    $schedule->setClinic($item['clinicId']);
    $schedule->setDoctorSheduleByWeekDay($weekDay, $item['times']);
}

?>