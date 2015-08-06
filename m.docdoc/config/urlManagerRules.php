<?php
/**
 * Правила работы роутера
 */
$suffixesToGenerate = [
	'na-dom' => '/<nadom:na-dom(?<!station)>',
	'deti' => '/<deti:deti>',
	'order' => '/order/<order:(price|experience|rating|rating_internal)>/direction/<direction:(asc|desc)>',
	'pagination' => '/page/<page:\d+>'
];

/**
 * Правила без генерации
 */
$urls = [
	'/' => 'site/index',
	'doctor/<alias:([A-Z].+)>' => 'doctor/detail',
	'context/<alias:([A-Z].+)>' => 'doctor/detail',
	'landing/<alias:([A-Z].+)>' => 'doctor/detail',

	'request' => 'doctor/request',
	'request/send' => 'doctor/requestSend',

	'<controller:\w+>/<id:\d+>' => '<controller>/view',
	'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
	'<controller:\w+>/<action:\w+>' => '<controller>/<action>',
];

//одинаковые урлы для контекста, лендинга и доктора
$_urls = [
	// Специальность + станция метро
	'<speciality:[a-z0-9_-]+(?<!page)>/<station:[\w-]+(?<!na-dom)(?<!deti)(?<!area)>',

	'<speciality:[a-z0-9_-]+(?<!page)>/stations/<stationId:\d+(,\d+)*>',

	// Районы (только москва)
	'<speciality:[a-z0-9_-]+>/area/<area:[\w-]+>/<district:[\w-]+(?<!na-dom)(?<!deti)>',

	// Округа (только москва)
	'<speciality:[a-z0-9_-]+>/area/<area:[\w-]+(?<!page)>',

	// Специальности + Район
	'<speciality:[a-z0-9_-]+>/district/<district:[\w-]+>',

	// Специальность
	'<speciality:[a-z0-9_-]+(?<!page)>',

	// Пустой округ (только москва)
	'<speciality:[a-z0-9_-]+>/area',
];

/**
 * Префикся для генератора
 */
$toGenerate = array_merge(
	generate($_urls),
	[
		// Станции метро
		'search/stations/<stationId:\d+[,\d+]*>' => 'search/search',

		// Район идут после всех
		'district/<district:[\w-]+>' => 'search/search'
	],
	generate([
		// Все врачи
		'',
	])
);

$generated = [];
foreach ($toGenerate as $baseUrl => $action) {
	$localGenerated = [];

	$suffixes = $suffixesToGenerate;

	while (count($suffixes)) {
		$top = array_shift($suffixes);

		$localGenerated[$baseUrl . $top] = $action;

		$temp = $suffixes;

		if (count($suffixes)) {
			while (count($temp)) {
				foreach ($temp as $s) {
					$localGenerated[$baseUrl . $top . $s] = $action;
				}

				$top .= array_shift($temp);
			}
		}
	}

	$localGenerated[$baseUrl] = $action;

	uksort(
		$localGenerated,
		function ($a, $b) {
			return strlen($a) < strlen($b);
		}
	);

	$generated = array_merge($generated, $localGenerated);
}

$fullUrls = array_merge($generated, $urls);

return $fullUrls;

/**
 * @param string[] $urls
 *
 * @return string[]
 */
function generate(array $urls) {
	$toGenerate = [];

	//заполнялю одноообразные урлы для контекста и лендинга
	foreach(['doctor', 'context', 'landing'] as $head){
		foreach($urls as $urlBody){
			$toGenerate[$head . ($urlBody ? "/{$urlBody}" : '')] = 'search/search';
		}
	}

	return $toGenerate;
}
