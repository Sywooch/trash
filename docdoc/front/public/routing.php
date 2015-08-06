<?php
/**
 * Точка входа для yii файлов.
 *
 * сейчас вызызов осуществляется так:
 * /routing.php?r=controller/action&param1=1&param2=2
 *
 * @todo переделать на вызов из корня сайта
 *
 */
require __DIR__ . "/include/common.php";

Yii::app()->urlManager->setUrlFormat(CUrlManager::GET_FORMAT);

Yii::app()->run();
