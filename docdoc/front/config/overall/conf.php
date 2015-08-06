<?php
$params = Yii::app()->params;
/**
 * @deprecated
 */
define ("SERVER_FRONT", $params['hosts']['front']);
/**
 * @deprecated
 */
define ("SERVER_BACK", $params['hosts']['back']);
/**
 * @deprecated
 */
define ("BASEDIR", realpath(__DIR__ . "/../../../front/public") . "/");
/**
 * @deprecated
 */
define ("GeneralPhone", "+7 (495) 565-333-0");
/**
 * @deprecated
 */
define ("GeneralPhoneShort", "(495) 565-333-0");

/**
 * @deprecated
 */
define ("SendErrorsToScreen", "yes");
/**
 * @deprecated
 */
define ("SendErrorsToMail", "yes");
/**
 * @deprecated
 */
define ("debugMode", "yes");

/**
 * @deprecated
 */
define ("statisticKey", "yes");
/**
 * @deprecated
 */
define ("socialKey", "yes");
/**
 * @deprecated
 */
define('socialVK', '3170529');
/**
 * @deprecated
 */
define('socialFB', '158112967546506');

/**
 * @deprecated
 */
define ("SendSMS", true);
/**
 * @deprecated
 */
define ("SMS_GateId", 1);

/**
 * @deprecated
 */
define ("Path4Reports", dirname(__FILE__) . "/../../../front/public/lk/_reports/");

/**
 * @deprecated
 */
$DEFCode = [
	'mobile' => [
		900,
		901,
		902,
		903,
		904,
		905,
		906,
		907,
		908,
		909,
		910,
		911,
		912,
		913,
		914,
		915,
		916,
		917,
		918,
		919,
		920,
		921,
		922,
		923,
		924,
		925,
		926,
		927,
		928,
		929,
		930,
		931,
		932,
		933,
		934,
		936,
		937,
		938,
		940,
		941,
		950,
		951,
		952,
		953,
		954,
		955,
		956,
		958,
		959,
		960,
		961,
		962,
		963,
		964,
		965,
		966,
		967,
		968,
		970,
		971,
		980,
		981,
		982,
		983,
		984,
		985,
		987,
		988,
		989,
		993,
		997,
		999
	],
	'msk'    => [
		499,
		800,
		803,
		901,
		903,
		905,
		906,
		909,
		910,
		915,
		916,
		917,
		919,
		925,
		926,
		929,
		936,
		941,
		958,
		962,
		963,
		964,
		965,
		967,
		968,
		970,
		971,
		985,
		997,
		999
	],
	'spb'    => [812, 813]
];
