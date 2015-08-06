<?php
use dfs\common\config\YiiAppRunner;

/**
 * Пусть до самого коря проекта
 *
 * @var string
 */
define('ROOT_PATH', realpath(__DIR__ . "/../.."));
require ROOT_PATH . '/common/include/common.php';

(new YiiAppRunner('common'))
	->create();

/**
 * @var CConsoleApplication $app
 */
$app = Yii::app();
$commandRunner = $app->commandRunner;
$commandRunner->addCommands(YII_PATH . '/cli/commands');
$commandRunner->addCommands(ROOT_PATH . '/common/commands');

$env = @getenv('YII_CONSOLE_COMMANDS');

if (!empty($env)) {
	$commandRunner->addCommands($env);
}

$app->run();
