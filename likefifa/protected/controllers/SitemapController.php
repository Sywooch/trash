<?php

use likefifa\models\RegionModel;

class SitemapController extends FrontendController {

	public function actionIndex($specialization = null, $service = null) {
		$this->render_map($specialization, $service, false);
	}

	public function actionSalons($specialization = null, $service = null) {
		$this->render_map($specialization, $service, true);
	}

	public function actionGenerate() {
		$sg = new SitemapGenerator;
		$sg->createSitemap();
	}

	/**
	 * Выводит карту сайта на экран
	 *
	 * @param string $specialization
	 * @param string $service
	 * @param bool $salons
	 *
	 * @return void
	 */
	protected function render_map($specialization = null, $service = null, $salons = false)
	{
		$this->setTitle('Карта сайта');
		if ($service || $specialization) {
			$specialization = LfSpecialization::model()->findByRewrite($specialization);
			if (!$specialization) {
				throw new CHttpException(404, 'Услуга не найдена');
			}

			$parentObj = $specialization;

			if ($service) {
				$service = LfService::model()->findBySpecAndRewrite($specialization, $service);
				if (!$service) {
					throw new CHttpException(404, 'Подуслуга не найдена');
				}

				$parentObj = $service;
			}
			$parent = $parentObj->name;
			if ($salons) {
				$this->render('detail_salons', compact('parent', 'service', 'specialization'));
			} else {
				$this->render('detail', compact('parent', 'service', 'specialization'));
			}
		} else {
			if (!empty($_GET["location"])) {
				$parent = "По расположению";
				if ($salons) {
					$this->render('detail_salons', compact('parent', 'service', 'specialization'));
				} else {
					$this->render('detail', compact('parent', 'service', 'specialization'));
				}
			} else {
				if ($salons) {
					$this->render('index_salons');
				} else {
					$this->render('index');
				}
			}
		}
	}

	/**
	 * Формирует динамический robots.txt
	 *
	 * @return void
	 */
	public function actionRobots()
	{
		header("Content-type: text/plain");
		$region = Yii::app()->activeRegion->getModel();

		$sitemap = $region->getIndexUrl() . "/sitemaps/mo/sitemap.xml";
		$host = $_SERVER['HTTP_HOST'];

		$this->renderPartial("robots", compact("host", "sitemap"));
	}
}