<?php

require_once __DIR__ . '/lib/php/Robots.php';

$secureConnection = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
	|| $_SERVER['SERVER_PORT'] == 443
	|| !empty($_SERVER['HTTP_SECURE']) && $_SERVER['HTTP_SECURE'] === 'on' // Кастыль для обхода балансировщика
;
header("Content-type: text/plain");

$robots = new Robots();
$robotsArr = array($robots);

$robots->setValue('User-agent', '*');
if ($secureConnection) {
	$robots->setValue('Disallow', '/');
} else {
	$robots->setValue('Disallow', array(
		'/admin/',
		'/kliniki*page/',
		'*/order/',
		'/select_doctor?*',
		'/opinion/',
		'/article/index',
		'/search',
		'/sector',
		'/library_new',
		'/article',
		'/js',
		'*/near/',
		'/*?station*',
		'/rewriteName/',
		'/docdoc/',
		'*/stations*',
		'/library/*/page',
		'/library/*/na-dom',
		'/library/podgotovka-k-uzi-malogo-taza',
		'/landing/',
		'/context/',
		'/clinic/*/page/',
		'*?PHPSESSID=*',
		'/bQ'
	));

	if (substr_count($_SERVER['HTTP_HOST'], 'spb.' ) > 0) {
		$robots->setValue('Host', 'spb.docdoc.ru');
		$robots->setValue('Sitemap', 'http://spb.docdoc.ru/sitemap.xml');
	} else {
		$robots->setValue('Host', 'docdoc.ru');
		$robots->setValue('Sitemap', 'http://docdoc.ru/sitemap.xml');

		$robots2 = new Robots();
		$robots2->setValue('User-agent', 'Googlebot');
		$robots2->setValue('Disallow', array(
			'/library/khirurgiia/',
			'/library/stomatologiya/',
			'/library/pediatriya/',
			'/library/urologiya/',
			'/library/andrologiia/',
			'/library/ginekologiya/',
			'/library/nevrologiya/',
			'/library/terapevtiya/',
			'/library/flebologiya/',
			'/library/kardiologiya/',
			'/library/mammologiya/',
		));

		$robotsArr[] = $robots2;
	}
}

/**
 * @var Robots $r
 */
foreach($robotsArr as $r) {
	echo $r->toString() . "\n";
}
