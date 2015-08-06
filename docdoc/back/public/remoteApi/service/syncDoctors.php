<?php

require_once dirname(__FILE__)."/../../include/common.php";
require_once dirname(__FILE__)."/../lib/OnClinic.php";
require_once dirname(__FILE__)."/../lib/MedSoft.php";


// Synchronize OnClinic doctors
//$onClinic = new OnClinic();
//$onClinic->syncDoctors();


// Synchronize MedSoft doctors
$api = new MedSoft();
$api->syncDoctors();
