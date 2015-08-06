<?php

namespace dfs\docdoc\doc;

use CApplication;
use Exception;

throw new Exception("Not for usage");

/**
 * Только для подсветки PHPDoc
 *
 * В этот файл нужно добавлять любые новые компоненты чтобы проходить статическую проверку синтаксиса
 *
 *
 * @author  Aleksey Parshukov <aparshukov@docdoc.ru>
 * @package docdoc
 *
 * @property \CLogRouter                               $log
 * @property \dfs\docdoc\components\EventDispatcher    $eventDispatcher
 * @property \dfs\docdoc\components\City               $city
 * @property \dfs\docdoc\components\MixpanelComponent  $mixpanel
 * @property \dfs\docdoc\components\YandexGeoApi       $yandexGeoApi
 * @property \dfs\docdoc\components\Partner            $referral
 * @property \dfs\docdoc\components\GaClient           $gaClient
 * @property \SwiftMailerComponent                     $mailer
 * @property \dfs\docdoc\components\MobileDetect       $mobileDetect
 * @property \dfs\docdoc\components\RatingComponent    $rating
 * @property \dfs\smsc\SmsC                            $sms
 * @property \YiiNewRelic                              $newRelic
 * @property \CWebUser                                 $user
 * @property \CHttpSession                             $session
 * @property \CHttpRequest                             $request
 * @property \dfs\docdoc\components\WhiteLabel         $whiteLabel
 * @property \dfs\docdoc\components\TrafficSourceComponent      $trafficSource
 * @property \dfs\docdoc\components\seo\SEO            $seo
 * @property \CClientScript                            $clientScript
 */
abstract class Application extends CApplication
{
}
