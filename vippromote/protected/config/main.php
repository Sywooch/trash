<?php

return array(
	"sourceLanguage" => "ru",

	"basePath" => dirname(__FILE__) . DIRECTORY_SEPARATOR . "..",
	"name" => "VIP-promote",
	"charset" => "UTF-8",

	"import" => array(
		"application.models.*",
		"application.components.*",
		"application.controllers.*",
		"application.helpers.*",
	),

	"preload" => array("log"),

	"components" => array(
		"db" => array(
			"connectionString" => "mysql:host=" . DB_HOST . "; dbname=" . DB_NAME,
			"emulatePrepare"   => true,
			"username"         => DB_USERNAME,
			"password"         => DB_PASSWORD,
			"charset"          => "utf8",
		),

		"urlManager"   => array(
			"urlFormat"      => "path",
			'showScriptName' => false,
			"rules"          => array(
				"/" => "/site/index",
				'logout' => 'site/logout',
				'admin/' => 'admin/index/index',
				'program' => 'page/program',
				'advertisement' => 'page/advertisement',
				'news' => 'page/news',
				'rules' => 'page/rules',
				'news/<id:\d+>' => 'page/news',
				'recovery' => 'site/recovery',
				'docs' => 'page/docs',
				'registration' => 'page/registration',
				'shop' => 'page/shop',
				'faq' => 'page/faq',
				'offers' => 'page/offers',
				'contacts' => 'page/contacts',
				'admin/<controller:\w+>/' => 'admin/<controller>/index',
				'admin/<controller:\w+>/<action:\w+>/<id:\d+>/*' => 'admin/<controller>/<action>',
				'admin/<controller:\w+>/<action:\w+>/*' => 'admin/<controller>/<action>',
				'admin/<controller:\w+>/*' => 'admin/<controller>/index',
			),
		),

		"clientScript" => array(
			"packages" => array(
				"jquery" => false,
				"jqueryui" => false, 
			),
		),

		"log"=>array(
            "class"=>"CLogRouter",
            "routes"=>array(
            	array(
                    "class"=>"CFileLogRoute",
                    "levels"=>"*",
                    "except"=>"system.*",
                    "logFile"=>"log.log",
                ),
                array(
                    "class"=>"CFileLogRoute",
                    "levels"=>"error, warning",
                    "except"=>"protected.*",
                    "logFile"=>"system.log",
                ),
            ),
        ),

		"user" => array(
			"allowAutoLogin" => true,
		),

		'image'=>array(
          'class'=>'application.extensions.image.CImageComponent',
            'driver'=>'GD',
            //'params'=>array('directory'=>'/opt/local/bin'),
        ),
	),

	"modules"    => array(
		"gii" => array(
			"class"          => "system.gii.GiiModule",
			"password"       => "q123456",
			"ipFilters"      => false,
			"generatorPaths" => array("application.gii"),
		),
	),

);