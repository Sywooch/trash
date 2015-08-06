<?php
namespace likefifa\components\config;

use CDbConnection;
use CLogRouter;
use CWebApplication;
use DGSphinxSearch;
use EAuth;
use likefifa\components\extensions\GaTrackingComponent;
use likefifa\components\extensions\LfMandrill;
use likefifa\components\extensions\YiiElephantIOComponent;

/**
 * @property CDbConnection                   $sqlite
 * @property EAuth                           $eauth
 * @property CLogRouter                      $log
 * @property YiiElephantIOComponent          $elephant
 * @property DGSphinxSearch                  $search
 * @property GaTrackingComponent             $gaTracking
 * @property LfMandrill                      $mailer
 */
class WebApplication extends CWebApplication
{

}
