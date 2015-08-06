<?php

use dfs\common\components\console\Command;
use dfs\docdoc\models\StreetModel;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\extensions\TextUtils;

/**
 * Обновление данных улиц
 *
 * Примеры команд:
 *    ./yiic updateStreet clinicStreets
 *    ./yiic updateStreet geoCoordinates
 *    ./yiic updateStreet rewriteName
 */
class UpdateStreetCommand extends Command
{
	/**
	 * Связывание улиц с клиниками (добавление новых улиц)
	 */
	public function actionClinicStreets()
	{
		$this->log('---- Связывание улиц с клиниками -----');

		foreach (ClinicModel::model()->findAll() as $clinic) {
			if (trim($clinic->street) === '') {
				continue;
			}

			$isNew = false;
			$street = StreetModel::model()->
				inCity($clinic->city_id)->
				searchTitle($clinic->street)->
				find();

			if ($street === null) {
				$isNew = true;
				$street = StreetModel::newStreet($clinic->city_id, $clinic->street);
				$street->save();
			}

			$clinic->street_id = $street->street_id;
			$clinic->save(true, [ 'street_id' ]);

			$this->log($street->street_id . ' - ' . $street->title . ($isNew ? ' *** NEW ***' : ''));
		}
	}

	/**
	 * Обновление гео-координат для всех улиц
	 */
	public function actionGeoCoordinates()
	{
		$this->log('---- Обновление координат улиц -----');

		foreach (StreetModel::model()->findAll() as $street) {
			if ($street->updateBound()) {
				$street->save();
				$this->log($street->street_id . ' - ' . $street->title . ' (' .
					$street->bound_left . ' ' . $street->bound_bottom . ' | ' .
					$street->bound_right . ' ' . $street->bound_top . ')');
			}
			else {
				$this->log('Не установленны гео-координаты для улицы ' . $street->street_id . ' "' . $street->title . '"');
			}
		}
	}

	/**
	 * Обновление данных улиц
	 */
	public function actionRefreshInfo()
	{
		$this->log('---- Обновление улиц -----');

		foreach (StreetModel::model()->findAll() as $street) {
			$info = StreetModel::getSearchInfo($street->title);
			if ($info['type'] !== null) {
				$street->type = $info['type'];
			}
			$street->search_title = $info['search_title'];

			$street->title = StreetModel::normalizeTitle($street->title);

			if ($street->updateRewriteName()) {
				try {
					$street->save();
					$this->log($street->street_id . ' - ' . $street->title . ' - ' . $street->rewrite_name . ' [' . $street->type . ']');
				}
				catch (Exception $e) {
					$this->log('Ошибка обновления для улицы ' . $street->street_id . ' "' . $street->title . '"');
				}
			}
			else {
				$this->log('Ошибка обновления для улицы ' . $street->street_id . ' "' . $street->title . '"');
			}
		}
	}
}