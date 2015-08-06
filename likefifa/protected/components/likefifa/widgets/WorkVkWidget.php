<?php


namespace likefifa\components\likefifa\widgets;

use CHtml;
use CWidget;
use Yii;

/**
 * Генерирует виджет лайка vk.com
 *
 * Class WorkVkWidget
 *
 * @package likefifa\components\likefifa\widgets
 */
class WorkVkWidget extends CWidget
{
	/**
	 * @var \LfWork
	 */
	public $work;

	/**
	 * @var \LfMaster
	 */
	public $master;

	public function run()
	{
		Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/social-likes.js');
		$socialImageLink = Yii::app()->getBaseUrl(true) . $this->work->preview('big');
		$socialLink = $this->master->getProfileUrl(true) . '#work-' . $this->work->id;
		$socialTitle = CHtml::encode($this->work->service->name) . ', ' . $this->master->getFullName();

		$this->render('WorkVkWidget', compact('socialImageLink', 'socialLink', 'socialTitle'));
	}
} 