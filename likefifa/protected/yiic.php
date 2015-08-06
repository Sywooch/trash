<?php
use likefifa\components\config\ConfigBuilder;
use likefifa\components\config\ConsoleApplication;
use likefifa\components\config\Environment;
use likefifa\components\config\YiiRunner;

require __DIR__ . '/vendors/autoload.php';

require 'components/config/common.php';


require dirname(__FILE__) . '/vendors/yiisoft/yii/framework/YiiBase.php';
require __DIR__ . '/components/config/Yii.php';

$config = (new ConfigBuilder(ConfigBuilder::getEnv()))->getConfig();
$app = Yii::createApplication(ConsoleApplication::class, $config);

$app->commandRunner->addCommands(YII_PATH . '/cli/commands');
$env = @getenv('YII_CONSOLE_COMMANDS');
if (!empty($env)) {
	$app->commandRunner->addCommands($env);
}

$app->run();


