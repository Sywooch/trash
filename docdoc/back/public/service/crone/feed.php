<?php

set_time_limit(3000);

require_once	dirname(__FILE__)."/../../include/common.php";
require_once	dirname(__FILE__)."/../../lib/php/models/doctor.class.php";
require_once	dirname(__FILE__)."/../../lib/php/models/clinic.class.php";
require_once	dirname(__FILE__)."/../../lib/php/schedule.class.php";
require_once	dirname(__FILE__)."/../../lib/php/feeder.class.php";

$xml = '';


$params = array();
$params['filename'] = dirname(__FILE__)."/../../feed.xml";
if(isset($argc) && ($argc > 1))
    $params['filename'] = $argv[1];

$feed = new Feeder($params);
$feed->buildXML(array('specs'));

?>
