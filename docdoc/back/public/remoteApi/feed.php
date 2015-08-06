<?php

require_once	dirname(__FILE__)."/../include/common.php";
require_once	dirname(__FILE__)."/../lib/php/models/doctor.class.php";
require_once	dirname(__FILE__)."/../lib/php/models/clinic.class.php";
require_once	dirname(__FILE__)."/../lib/php/schedule.class.php";
require_once	dirname(__FILE__)."/../lib/php/feeder.class.php";

$xml = '';

$feed = new Feeder();
$xml .= $feed->getXML(array('specs'));

header('Content-type: text/xml; charset=UTF-8');

//$str = $doc->saveXML();	
print $xml;

?>
