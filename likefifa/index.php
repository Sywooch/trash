<?php
use likefifa\components\config\ConfigBuilder;
use likefifa\components\config\Environment;
use likefifa\components\config\WebApplication;
use likefifa\components\config\YiiRunner;

require __DIR__ . '/protected/vendors/autoload.php';
require __DIR__ . '/protected/extensions/ApnsPHP/Autoload.php';

require 'protected/components/config/common.php';

$startPoint = Environment::isProduction() || Environment::isStage() ? 'yiilite.php' : 'yii.php';
require dirname(__FILE__) . '/protected/vendors/yiisoft/yii/framework/' . $startPoint;

$config = (new ConfigBuilder(ConfigBuilder::getEnv()))->getConfig();
Yii::createApplication(WebApplication::class, $config)->run();