<?php
$isMobile = (int)Yii::app()->mobileDetect->isMobile();
$cacheName = "index_" . $isMobile . ".likefifa";

$page = Yii::app()->cache->get($_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"]);
if (!$page) {
	$page = $this->renderPartial(
		"_index",
		array(
			'sectors'   => Sector::model()->ordered()->findAll(),
			'bestWorks' => LfWork::model()->with(['master'])->index()->findAll(),
		),
		true
	);
	Yii::app()->cache->set($_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"], $page, Yii::app()->params["cacheTime"]);
}

echo $page;