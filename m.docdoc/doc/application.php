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
 * @author Aleksey Parshukov <aparshukov@docdoc.ru>
 * @package docdoc
 *
 * @property \CLogRouter                        $log
 * @property \dfs\components\City               $city
 * @property \dfs\components\MixpanelComponent  $mixpanel
 */
abstract class Application extends CApplication
{
}


