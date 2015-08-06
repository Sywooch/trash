<?php
/**
 * @author Aleksey Parshukov
 * @since 14.02.2014 4:47 PM
 *
 *        Общий инклуд по всем проектам
 */
mb_internal_encoding('UTF-8');

/**
 * Пусть до директории common
 *
 * @var string
 */
define('COMMON_PATH', realpath(ROOT_PATH . DIRECTORY_SEPARATOR . 'common'));

/**
 * Путь до вендоров
 *
 * @var string
 */
define('VENDOR_PATH', COMMON_PATH . DIRECTORY_SEPARATOR . 'vendor');

/**
 * Путь до yii
 *
 * @var string
 */
define('YII_PATH', join(DIRECTORY_SEPARATOR, array(
	VENDOR_PATH,
	'yiisoft',
	'yii',
	'framework',
)));

/**
 * Пусть до файлов из back
 *
 * @var string
 */
define ("LIB_PATH", ROOT_PATH . "/back/public/lib/");

/* Права на доступ для директорий*/
define('DIR_MODE', 0775);

/* Права на доступ для файлов*/
define("FILE_MODE", 0664);


require VENDOR_PATH . DIRECTORY_SEPARATOR . 'autoload.php';