<?php

header('Content-type: text/html; charset=UTF-8');

require_once dirname(__FILE__)."/../../include/common.php";
require_once dirname(__FILE__).'/../lib/OnClinic.php';
require_once dirname(__FILE__).'/../lib/MedSoft.php';

$api = new OnClinic;
//var_dump($api->getClinics());
//var_dump($api->getSpecialities());
//var_dump($api->getDoctors());
//var_dump($api->getDoctors(array('clinic'=>1, 'spec'=>9)));
//var_dump($api->getDoctorsXML());
//var_dump($api->getSchedule(array('date'=>'08.02.2013', 'doctor'=>1, 'clinic'=>9)));

$api->syncDoctors();
 
 
$api = new MedSoft();
$api->syncSpecialities();
$api->syncDoctors();

?>
