<?php
require_once dirname(__FILE__) . '/protected/config/server.php';

$yii = dirname(__FILE__) . '/protected/vendors/yiisoft/yii/framework/yii.php';
$config = dirname(__FILE__) . '/protected/config/main.php';

require_once($yii);
Yii::createWebApplication($config)->run();