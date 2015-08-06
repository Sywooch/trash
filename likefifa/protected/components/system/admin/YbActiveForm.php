<?php


namespace likefifa\components\system\admin;

use CHtml;
use TbActiveForm;
use Yii;

Yii::import('booster.widgets.TbActiveForm');

class YbActiveForm extends TbActiveForm
{
	public function select2Group($model, $attribute, $options = array())
	{
		return $this->widgetGroupInternal('likefifa\components\system\admin\YbSelect2', $model, $attribute, $options);
	}

	public function datePickerGroup($model, $attribute, $options = array())
	{

		return $this->widgetGroupInternal('likefifa\components\system\admin\YbDatePicker', $model, $attribute, $options);
	}

	/**
	 * Выполняется после отрисовки формы
	 */
	public function run()
	{
		if ($this->controller->returnUrl != null &&
			($this->controller->action->id == 'create' || $this->controller->action->id == 'update')
		) {
			echo CHtml::hiddenField('returnUrl', $this->controller->returnUrl);
		}
		parent::run();
	}
} 