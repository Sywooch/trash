<?php

namespace dfs\docdoc\back\controllers;

use dfs\docdoc\models\CityModel;
use Yii;

class PartnerFeedController extends \CController
{
	/**
	 * Генерация XML фида в формате yml
	 */
	public function actionYml()
	{
		$this->showYmlFeed();
	}

	/**
	 * Генерация XML фида в формате yml версии 2
	 */
	public function actionYml2()
	{
		$this->showYmlFeed(2);
	}

	/**
	 * Применить фильтры для каждого действия в контроллере
	 *
	 * @return array
	 */
	public function filters()
	{
		return [
			['dfs\docdoc\filters\PartnerAuthFilter']
		];
	}

	public function action2Gis()
	{
		$cityName = Yii::app()->request->getQuery('city', 'msk');
		$city = CityModel::model()->byRewriteName($cityName)->active()->find();

		if ($city === null) {
			throw new \CHttpException(400);
		}

		$file = ROOT_PATH . "/back/runtime/feed/gis2.xml";

		if (!is_file($file)) {
			throw new \CHttpException(500);
		}

		header('Content-type: text/xml');
		header('Content-length: ' . filesize($file));

		readfile($file);
		exit;
	}

	/**
	 * Показ yml фида
	 *
	 * @param string $version
	 *
	 * @throws \CHttpException
	 */
	public function showYmlFeed($version = '')
	{
		$cityName = Yii::app()->request->getQuery('city', 'msk');
		$city = CityModel::model()->byRewriteName($cityName)->active()->find();

		if ($city === null) {
			throw new \CHttpException(400);
		}

		$file = ROOT_PATH . "/back/runtime/feed/ymlfeed{$version}_city_{$city->rewrite_name}.xml";
		if (!is_file($file)) {
			throw new \CHttpException(500);
		}

		header('Content-type: text/xml');
		header('Content-length: ' . filesize($file));

		readfile($file);
		exit;
	}

}
