<?php

use dfs\common\components\console\Command;
use likefifa\models\RegionModel;

/**
 * SitemapCommand class file.
 *
 * Генерирует sitemap.xml
 *
 * @cron    * 3 * * *
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see     https://docdoc.megaplan.ru/task/1003365/card/
 * @package commands
 */
class SitemapCommand extends Command
{

	/**
	 * @param array $args
	 *
	 * @return void
	 */
	public function run($args)
	{
		// Фиксы для правильной генерации урлов
		$_SERVER['SERVER_NAME'] = str_replace('http://', '', Yii::app()->params['baseUrl']);
		$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'];
		Yii::app()->request->setScriptUrl('');

		$this->log("=======================================");

		$this->log("Началось создание sitemap.xml...", CLogger::LEVEL_INFO, "protected.commands.SitemapCommand");

		$sg = new SitemapGenerator;

		foreach (RegionModel::model()->active()->orderByName()->findAll() as $model) {
			$this->log(
				"Для {$model->name_genitive}...",
				CLogger::LEVEL_INFO,
				"protected.commands.SitemapCommand"
			);

			$sg->regionModel = $model;
			$sg->createSitemap();

			$this->log(
				"Для {$model->name_genitive} успешно созданы!",
				CLogger::LEVEL_INFO,
				"protected.commands.SitemapCommand"
			);
		}

		$this->log(
			"Sitemap.xml успешно создан!",
			CLogger::LEVEL_INFO,
			"protected.commands.SitemapCommand"
		);

		$this->log("=======================================");
	}
}