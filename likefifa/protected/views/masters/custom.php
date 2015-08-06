<?php
$isMobile = (int)Yii::app()->mobileDetect->isMobile();
$cacheName = $_SERVER["REQUEST_URI"] . '_version_' . $isMobile;

$page = Yii::app()->cache->get($_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"]);
if (!$page) {
	$page = $this->renderPartial(
		"partials/_custom",
		array_merge(
			$this->getSearchParams(),
			compact(
				'seoText',
				'dataProvider',
				'top10',
				'articles',
				'showAll',
				'serviceName',
				'stationsName',
				'districtsName'
			)
		),
		true
	);
	Yii::app()->cache->set($_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"], $page, Yii::app()->params["cacheTime"]);
}

echo $page;