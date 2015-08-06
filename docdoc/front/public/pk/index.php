<?php

use dfs\common\config\YiiAppRunner;


define('ROOT_PATH', realpath(__DIR__ . '/../../..'));

require ROOT_PATH . '/common/include/common.php';

ini_set('default_charset', 'utf-8');

(new YiiAppRunner('front'))
	->addConfig('/front/config/overall/pk.php')
	->create();

require ROOT_PATH . '/front/config/overall/conf.php';

Yii::app()->run();
