<?php

require(__DIR__ . "/components/App.php");
use components\App;

$app = new App(require(__DIR__ . "/config/common.php"));
$app->runController();