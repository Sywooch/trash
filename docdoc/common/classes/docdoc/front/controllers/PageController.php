<?php

namespace dfs\docdoc\front\controllers;

/**
 * Class PageController
 * @package dfs\docdoc\front\controllers
 */
class PageController extends FrontController
{
	public function actionOffer()
	{
		$this->initXslTemplates();

		$this->render('offer');
	}

	/**
	 * Рендер для старых страниц, работающих на xsl-шаблонах
	 *
	 * @param string $template
	 * @param string $mode
	 * @param string $withoutLayout
	 */
	public function actionOld($template, $mode = null, $withoutLayout = null)
	{
		getHeaderXML();

		if ($mode) {
			$this->mode = $mode;
		}

		if ($withoutLayout) {
			echo $this->renderXsl($template, null, true);
		} else {
			$this->renderText($this->renderXsl($template, null, true));
		}

		\Yii::app()->onEndRequest(new \CEvent(null));
	}
}