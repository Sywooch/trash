<?php

require_once	dirname(__FILE__)."/../../include/common.php";
require_once	dirname(__FILE__)."/../lib/MedSoft.php";
require_once dirname(__FILE__).'/../lib/OnClinic.php';


$api = new OnClinic;
$api->book(52782);


// Synchronize MedSoft doctors
//$api = new MedSoft();


//var_dump($api->syncSpecialities());
//$api->syncSpecialities();
//var_dump($api->getSchedule(strtotime('20-05-2013')));
//var_dump($api->getSheduleKeys(time(),2242));
//var_dump($api->getFreeTimes(time(),2242));
//var_dump($api->record(22478));


?>
