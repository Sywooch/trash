<?php

require_once	dirname(__FILE__)."/../../include/common.php";
require_once	dirname(__FILE__)."/../lib/MedSoft.php";


// Synchronize MedSoft doctors
$api = new MedSoft();
$api->syncSpecialities();


?>
