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
define ("BASEDIR", realpath(__DIR__ . "/../../../back/public") . DIRECTORY_SEPARATOR);

/**
 * @deprecated
 */
define ("GeneralPhone", "+7 (495) 565-333-0");
/**
 * @deprecated
 */
define("TOP_SUPPORT_EMAIL", "mfisenko@docdoc.ru, aparshukov@docdoc.ru");
/**
 * @deprecated
 */
define("CALL_CENTER_SUPPORT_EMAIL", "ilokteeva@docdoc.ru, mfisenko@docdoc.ru , support@docdoc.ru");

/**
 * @deprecated
 */
define ("SendErrorsToScreen", "no");
/**
 * @deprecated
 */
define ("SendErrorsToMail", "yes");
/**
 * @deprecated
 */
define ("debugMode", "no");

/**
 * @deprecated
 */
define ("LOG_LEVEL", "OFF"); // ALL || CRITICAL || WARNING

/**
 * @deprecated
 */
define ("statisticKey", "no");
/**
 * @deprecated
 */
define ("social", "no");
/**
 * @deprecated
 */
define ("VK", "2761499");
/**
 * @deprecated
 */
define ("FB", "287434004654160");

/**
 * @deprecated
 */
define ("Path4Upload", $params['path']['upload'] . DIRECTORY_SEPARATOR);

/**
 * @deprecated
 */
define ("EMAIL_TRY_COUNT", 2);

/**
 * @deprecated
 */
define ("YandexAPIkey", "AP7xRE8BAAAAk9N2JQMAi94YePvl3FF9mz_YNCC3oJM0AHsAAAAAAAAAAABxM9DYuLvXEbVVBHiYQZ3xvFenVw==");

/**
 * @deprecated
 */
define ("DeltaAN", 9000);

/*      Заявки  */

/**
 * @deprecated
 */
define ("BEGIN_WORK_HOUR", 9);
/**
 * @deprecated
 */
define ("END_WORK_HOUR", 21);

/**
 * @deprecated
 */
define("croneSMS_MaxLockedTry", 5);

/**
 * @deprecated
 */
define("croneEmail_MaxLockedTry", 5);
/**
 * @deprecated
 */
define("croneDoctorRating_MaxLockedTry", 5);
/**
 * @deprecated
 */
define("croneRequestListener_MaxLockedTry", 5);
/**
 * @deprecated
 */
define("croneUpdateRequestForPartners_MaxLockedTry", 5);


/**
 * @deprecated
 */
define ("SMS_POOL", 25);
/**
 * @deprecated
 */
define ("SMS_LIMIT", 40);
/**
 * @deprecated
 */
define ("SMS_LIMIT_PER_PHONE", 5);

/**
 * @deprecated
 */
$ADMIN_SMS_PHONE = [
	'79167509307', // Паршуков Алексей
	'79168112564', // Николай Дунаев
	'79090598412', // Роман Бурнашев
];

/**
 * @deprecated
 */
define ("SMS_GateId", 1);
/**
 * @deprecated
 */
define ("SMS_BalanceLimit", 500);

/**
 * Качество сохраняемых изображений
 * @deprecated
 */
define("IMAGE_QUALITY", 90);


/**
 * Номера телефонов, которые не учитываются при слиянии заявок
 *
 * @deprecated
 */
$confPhonesForNoMergedRequests = array(
	74952138864,
	74952138895,
	74954110250
);
