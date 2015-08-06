<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 12.11.14
 * Time: 14:10
 */

use dfs\common\components\console\Command;
use dfs\docdoc\objects\google\BigQuery;

/**
 * Class GoogleBDCommand
 */
class GoogleBQCommand extends Command
{
	/**
	 * Обновление токена для гугли биг query
	 */
	public function actionUpdateToken()
	{
		$bigData = new BigQuery();

		if($bigData->updateToken()){
			$this->log('Google big query token успешно обновлен');
		} else {
			$this->log('Ошибка обновленя google big query token');
		}
	}

	/**
	 * Сброс данных в таблицу гугли биг query
	 */
	public function actionFlush()
	{
		try {
			BigQuery::flush();
		} catch (Exception $e) {
			$this->log("Ошибка при выполнении запроса в Google Big Query: " . $e->getMessage());
		}
	}
} 
