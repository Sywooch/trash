<?php
use likefifa\components\config\ConfigBuilder;
use likefifa\components\config\Environment;
use likefifa\components\config\WebApplication;
use likefifa\components\config\YiiRunner;
use likefifa\components\test\TestDbLoader;
require __DIR__ . '/../vendors/autoload.php';

require __DIR__ . '/../components/config/common.php';

require dirname(__FILE__) . '/../vendors/yiisoft/yii/framework/yii.php';
$config = (new ConfigBuilder(Environment::ENV_TEST))->getConfig();

YiiBase::$enableIncludePath = false;
Yii::createApplication(WebApplication::class, $config);

new TestDbLoader(ROOT_PATH . '/protected/data/schema.mysql.sql');


