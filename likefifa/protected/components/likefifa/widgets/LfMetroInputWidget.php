<?php


namespace likefifa\components\likefifa\widgets;

use CClientScript;
use CWidget;
use Yii;

class LfMetroInputWidget extends CWidget
{
	/**
	 * @var string
	 */
	public $stationList = null;

	/**
	 * @var string
	 */
	public $stationIdList = null;

	/**
	 * Запуск виджета
	 */
	public function run()
	{
		if (Yii::app()->mobileDetect->isMobile()) {
			Yii::app()->clientScript
				->registerScriptFile(
					Yii::app()->baseUrl . '/js/jquery.jsonSuggest.js',
					CClientScript::POS_END
				)
				->registerScript(
					'init-metro-input',
					'var suggest = new SearchSuggest(); suggest.formId = "filter-form"; suggest.initMetro("metro-suggest");',
					CClientScript::POS_END
				);
		}
		$this->render('LfMetroInputWidget');
	}
} 