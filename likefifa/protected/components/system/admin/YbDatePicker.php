<?php


namespace likefifa\components\system\admin;

use TbDatePicker;
use Yii;

Yii::import('booster.widgets.TbDatePicker');

class YbDatePicker extends TbDatePicker
{
	public function init()
	{
		$this->options['language'] = 'ru';
		if (!isset($this->options['format'])) {
			$this->options['format'] = 'dd.mm.yyyy';
		}
		if (!isset($this->events['changeDate'])) {
			$this->events['changeDate'] = 'js:function() {$(this).data("datepicker").hide();}';
		}
		parent::init();
	}
} 