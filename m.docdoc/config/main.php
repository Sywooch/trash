<?php
return [
    'name' => 'WebApplication',
    'components' => [
        'clientScript' => [
            'corePackages' => [
                'jquery' => array(
                    'baseUrl' => null,
                    'js' => null,
                ),
            ],
        ],
        'user' => [
            'allowAutoLogin' => true,
            'autoRenewCookie' => true,
        ],
        'errorHandler' => [
            'errorAction' => '/site/error',
        ],
        'session' => [
            'autoStart' => false,
            'cookieMode' => 'only',
            'sessionName' => 'x-rx',
        ],
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
                // uncomment the following to show log messages on web pages

                /**array(
                    'class' => 'CWebLogRoute',
                ),*/

            ),
        ),
    ],
];