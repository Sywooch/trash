<?php

/*****  Карта маршрутов *****/

//  Параметры фильтров и навигации
$addPattern = '?(/na-dom)?(/deti)?(/order/(experience|price|rating)/direction/(asc|desc))?(/page/([0-9]+))?';
$addAliases = array('departure', 'kidsReception', 'trash', 'orderType', 'orderDir', 'trash', 'page');


$routesMap = array
(

	/*** Главная страница ***/
	array(
		'pattern' => '~^/$~',
		'method'  => 'Index',
		'aliases' => array('trash'),
	),
	/*** Полная и краткая карточки врача ***/

	array(
		'pattern' => '~^/doctor/' . $addPattern . '$~',
		'method'  => 'Doctor',
		'aliases' => array_merge(array('trash'), $addAliases)
	),
	// Запись к врачу
	array(
		'pattern' => '~^/doctor/request(/thanks(/id/([0-9a-zA-Z]+))?)?$~',
		'method'  => 'Request',
		'aliases' => array('trash', 'isThanks', 'trash', 'bookId'),
	),
	// Поиск врачей по городу Подмосковья
	array(
		'pattern' => '~^/doctor/([a-zA-Z_-]+)/city/([a-zA-Z_-]+)' . $addPattern . '$~',
		'method'  => 'Doctor',
		'aliases' => array_merge(array('trash', 'speciality', 'regCity'), $addAliases)
	),
	// все врачи в районе
	array(
		'pattern' => '~^/district/([0-9a-zA-Z_-]+)' . $addPattern . '$~',
		'method'  => 'Doctor',
		'aliases' => array_merge(array('trash', 'district'), $addAliases),
	),
	// Поиск врачей по району
	array(
		'pattern' => '~^/doctor/([a-zA-Z_-]+)/district/([0-9a-zA-Z_-]+)' . $addPattern . '$~',
		'method'  => 'Doctor',
		'aliases' => array_merge(array('trash', 'speciality', 'district'), $addAliases),
	),
	// Поиск врачей по округу и району Москвы
	array(
		'pattern' => '~^/doctor/([a-zA-Z_-]+)/area/([a-zA-Z_-]+)(/(?!deti)([a-zA-Z_-]+))?' . $addPattern . '$~',
		'method'  => 'Doctor',
		'aliases' => array_merge(array('trash', 'speciality', 'area', 'trash', 'district'), $addAliases),
	),
	// Поиск врачей по станциям метро
	array(
		'pattern' => '~^/doctor/([a-zA-Z_-]+)/stations/([0-9,]+)(/near)?' . $addPattern . '$~',
		'method'  => 'Doctor',
		'aliases' => array_merge(array('trash', 'speciality', 'stations', 'near'), $addAliases),
	),
	// Поиск всех врачей с выездом на дом
	array(
		'pattern' => '~^/doctor/([a-zA-Z_-]+)?' . $addPattern . '$~',
		'method'  => 'Doctor',
		'aliases' => array_merge(array('trash', 'speciality'), $addAliases),
	),
	// Поиск врачей по одной станции метро
	array(
		'pattern' => '~^/doctor/([a-zA-Z_-]+)/([0-9a-zA-Z_-]+)(/near)?' . $addPattern . '$~',
		'method'  => 'Doctor',
		'aliases' => array_merge(array('trash', 'speciality', 'stationAlias', 'near'), $addAliases),
	),
	// Поиск врачей по специальности
	array(
		'pattern' => '~^/doctor/([0-9a-zA-Z_-]+)?' . $addPattern . '$~',
		'method'  => 'Doctor',
		'aliases' => array_merge(array('trash', 'alias'), $addAliases),
	),
	// Поиск всех врачей по станциям
	array(
		'pattern' => '~^/search(/stations/([0-9,]+))?(/near)?' . $addPattern . '$~',
		'method'  => 'Doctor',
		'aliases' => array_merge(array('trash', 'trash', 'stations', 'near'), $addAliases),
	),
	// Поиск всех врачей по районам
	array(
		'pattern' => '~^/search(/district/([0-9a-zA-Z_-]+))?(/near)?' . $addPattern . '$~',
		'method'  => 'Doctor',
		'aliases' => array_merge(array('trash', 'trash', 'district', 'near'), $addAliases),
	),
	// Поиск врачей для контекстой рекламы
	array(
		'pattern' => '~^/context/([a-zA-Z_]+)' . $addPattern . '$~',
		'method'  => 'Context',
		'aliases' => array_merge(array('trash', 'speciality'), $addAliases),
	),
	// Поиск врачей для лэндинга
	array(
		'pattern' => '~^/landing/([a-zA-Z_]+)' . $addPattern . '$~',
		'method'  => 'Landing',
		'aliases' => array_merge(array('trash', 'speciality'), $addAliases),
	),
	// Поиск врачей по контексту
	array(
		'pattern' => '~^/contextSearch/keywords/([\S ]+)?(/page/([0-9]+))?$~U',
		'method'  => 'ContextSearch',
		'aliases' => array_merge(array('trash', 'searchWord', 'trash', 'page'), $addAliases),
	),

	/*** Клиники ***/

	// Поиск всех клиник
	array(
		'pattern' => '~^/clinic(/spec)?(/page/([0-9]+))?$~',
		'method'  => 'Clinic',
		'aliases' => array('trash', 'trash', 'trash', 'page'),
	),
	// Поиск клиник по специализации
	array(
		'pattern' => '~^/clinic/spec/([a-zA-Z_]+)?(/page/([0-9]+))?$~',
		'method'  => 'Clinic',
		'aliases' => array('trash', 'specialization', 'trash', 'page'),
	),
	// Поиск клиник по городу Подмосковья
	array(
		'pattern' => '~^/clinic/spec/([a-zA-Z_]+)/city/([a-zA-Z_-]+)(/page/([0-9]+))?$~',
		'method'  => 'Clinic',
		'aliases' => array('trash', 'specialization', 'regCity', 'trash', 'page')
	),
	// Поиск врачей по району города
	array(
		'pattern' => '~^/clinic/spec/([a-zA-Z_]+)/district/([0-9a-zA-Z_-]+)(/page/([0-9]+))?$~',
		'method'  => 'Clinic',
		'aliases' => array('trash', 'specialization', 'district'),
	),
	// Поиск врачей по округу и району Москвы
	array(
		'pattern' => '~^/clinic/spec/([a-zA-Z_]+)/area/([a-zA-Z_-]+)(/([a-zA-Z_-]+))?(/page/([0-9]+))?$~',
		'method'  => 'Clinic',
		'aliases' => array('trash', 'specialization', 'area', 'trash', 'district', 'trash', 'page'),
	),
	// Поиск врачей по одной станции метро
	array(
		'pattern' => '~^/clinic/spec/([a-zA-Z_]+)/([0-9a-zA-Z_-]+)(/page/([0-9]+))?$~',
		'method'  => 'Clinic',
		'aliases' => array('trash', 'specialization', 'stationAlias', 'trash', 'page'),
	),
	array(
		'pattern' => '~^/clinic/station/([0-9a-zA-Z_-]+)(/page/([0-9]+))?$~',
		'method'  => 'Clinic',
		'aliases' => array('trash', 'stationAlias', 'trash', 'page'),
	),
	// Поиск клиник по району города
	array(
		'pattern' => '~^/clinic/district/([0-9a-zA-Z_-]+)(/page/([0-9]+))?$~',
		'method'  => 'Clinic',
		'aliases' => array('trash', 'district', 'trash', 'page'),
	),
	// Поиск клиник по округу и району Москвы
	array(
		'pattern' => '~^/clinic/area/([a-zA-Z_-]+)(/([0-9a-zA-Z_-]+))?(/page/([0-9]+))?$~',
		'method'  => 'Clinic',
		'aliases' => array('trash', 'area', 'trash', 'district', 'trash', 'page'),
	),
	// Поиск клиник по улице
	array(
		'pattern' => '~^/clinic/street/([0-9a-zA-Z_-]+)(/page/([0-9]+))?$~',
		'method'  => 'Clinic',
		'aliases' => array('trash', 'street', 'trash', 'page'),
	),
	// Запись в клинику
	array(
		'pattern' => '~^/clinic/request(/thanks(/id/([0-9a-zA-Z]+))?)?$~',
		'method'  => 'Request',
		'aliases' => array('trash', 'isThanks', 'trash', 'bookId'),
	),
	// Просмотр клиники
	array(
		'pattern' => '~^/clinic(/([0-9a-zA-Z_-]+))?' . $addPattern . '$~',
		'method'  => 'Clinic',
		'aliases' => array_merge(array('trash', 'trash', 'alias'), $addAliases),
	),
	/*** Справочник пациента ***/

	// Разделы статей и статьи
	array(
		'pattern' => '~^/library(/([0-9a-zA-Z_-]+))?(/([0-9a-zA-Z_-]+))?$~',
		'method'  => 'Library',
		'aliases' => array('trash', 'trash', 'section', 'trash', 'article'),
	),
	/*** Справочник заболеваний ***/

	// Заболевания
	array(
		'pattern' => '~^/illness/alphabet(/([0-9a-zA-Z_-]+))?$~',
		'method'  => 'Illness',
		'aliases' => array('trash', 'trash', 'letter'),
	),
	array(
		'pattern' => '~^/illness(/([0-9a-zA-Z_-]+))?$~',
		'method'  => 'Illness',
		'aliases' => array('trash', 'trash', 'illness'),
	),
	array(
		'pattern' => '~^/illness/([a-zA-Z_-]+)/([0-9a-zA-Z_-]+)$~',
		'method'  => 'Illness',
		'aliases' => array('trash', 'specialization', 'illness'),
	),
	/*** Карта сайта ***/
	array(
		'pattern' => '~^/sitemap/(street)$~',
		'method'  => 'Sitemap',
		'aliases' => array('trash', 'street'),
	),
	array(
		'pattern' => '~^/sitemap(/([0-9]+))?$~',
		'method'  => 'Sitemap',
		'aliases' => array('trash', 'trash', 'speciality'),
	),
	array(
		'pattern' => '~^/sitemap/clinic/([0-9]+)$~',
		'method'  => 'Sitemap',
		'aliases' => array('trash', 'specialization'),
	),
	/*** О нас ***/

	array(
		'pattern' => '~^/about_docdoc?$~',
		'method'  => 'About',
		'aliases' => array('trash', 'trash', 'trash'),
	),
	array(
		'pattern' => '~^/news?$~',
		'method'  => 'News',
		'aliases' => array('trash', 'trash', 'trash'),
	),
	array(
		'pattern' => '~^/contact?$~',
		'method'  => 'Contact',
		'aliases' => array('trash', 'trash', 'trash'),
	),
	array(
		'pattern' => '~^/smi?$~',
		'method'  => 'SMI',
		'aliases' => array('trash', 'trash', 'trash'),
	),
	array(
		'pattern' => '~^/offer?$~',
		'method'  => 'Offer',
		'aliases' => array('trash', 'trash', 'trash'),
	),
	/*** Заявка ***/
	array(
		'pattern' => '~^/request(/thanks(/id/([0-9a-zA-Z]+))?)?$~',
		'method'  => 'Request',
		'aliases' => array('trash', 'isThanks', 'trash', 'bookId'),
	),
	array(
		'pattern' => '~^/requestForm/id/([0-9]+)$~',
		'method'  => 'RequestForm',
		'aliases' => array('trash', 'id'),
	),
	/*** Попары ***/

	array(
		'pattern' => '~^/popup/sector$~',
		'method'  => 'PopupSpeciality',
		'aliases' => array('trash', 'trash', 'map'),
	),
	array(
		'pattern' => '~^/popup/map$~',
		'method'  => 'PopupMap',
		'aliases' => array('trash', 'trash', 'map'),
	),
	array(
		'pattern' => '~^/popup/offer$~',
		'method'  => 'PopupOffer',
		'aliases' => array('trash'),
	),
	/*** Регистрация врачей и клиник ***/
	array(
		'pattern' => '~^/register(/([0-9a-zA-Z]+))?$~',
		'method'  => 'Register',
		'aliases' => array('trash', 'trash', 'step'),
	),
	/*** Служебные данные ***/
	array(
		'pattern' => '~^/opinion/more/([0-9]+)?$~',
		'method'  => 'OpinionMore',
		'aliases' => array('trash', 'id'),
	),
	/*** Страница помощи ***/
	array(
		'pattern' => '~^/page/help$~',
		'method'  => 'Help',
		'aliases' => array('trash'),
	),

);

?>
