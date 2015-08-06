<?php

// Что бы правильно работал CUrlManager для mobileDetect
$_SERVER['SCRIPT_NAME'] =  '/router.php';

require_once dirname(__FILE__) . "/../include/header.php";
require_once dirname(__FILE__) . "/../include/routes.php";
require_once LIB_PATH . "../lib/php/russianTextUtils.class.php";
require_once LIB_PATH . "../lib/php/pager.php";


$url = str_replace('/?', '?', $_SERVER['REQUEST_URI']);

$page = new Page($url, $routesMap);

if ($page->method) {
	$page->generate();
} else {
	$page->initSeo();

	Yii::app()->run();
}
