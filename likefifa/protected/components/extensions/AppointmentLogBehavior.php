<?php


namespace likefifa\components\extensions;

use CActiveRecordBehavior;
use CEvent;
use CModelEvent;
use LfAppointment;
use likefifa\models\LfAppointmentLog;
use Yii;

/**
 * Поведение для логирования изменений в заявке
 *
 * @property LfAppointment $owner
 *
 * @package likefifa\components\extensions
 */
class AppointmentLogBehavior extends CActiveRecordBehavior
{
	private $_oldAttributes = array();

	public $excludedAttributes = [
		'changed',
	];

	/**
	 * Действие выполняется после сохранения модели
	 *
	 * @param CModelEvent $event
	 */
	public function afterSave($event)
	{
		$data = [];
		if (!$this->owner->isNewRecord) {
			$newAttributes = $this->owner->getAttributes();
			$oldAttributes = $this->getOldAttributes();
			foreach ($newAttributes as $name => $value) {
				if (array_search($name, $this->excludedAttributes) !== false) {
					continue;
				}
				if (!empty($oldAttributes)) {
					$old = $oldAttributes[$name];
				} else {
					$old = '';
				}

				if ($value != $old) {
					$data[$name] = [
						'old'   => $old,
						'value' => $value,
					];
				}
			}
		}

		if ($this->owner->isNewRecord || !empty($data)) {
			$log = new LfAppointmentLog;
			$log->appointment_id = $this->owner->getPrimaryKey();
			$log->action = $this->owner->isNewRecord ? $log::ACTION_CREATE : $log::ACTION_UPDATE;
			$log->data = serialize($data);
			if (!Yii::app()->user->isGuest) {
				switch (Yii::app()->user->type) {
					case 'admin' :
						$log->admin_id = Yii::app()->user->id;
						break;
					case 'master' :
						$log->master_id = Yii::app()->user->id;
						break;
					case 'salon' :
						$log->salon_id = Yii::app()->user->id;
						break;
				}
			}
			$log->save(false);
		}
	}

	/**
	 * Выполняется после удаления модели
	 *
	 * @param CEvent $event
	 */
	public function afterDelete($event)
	{

	}

	/**
	 * После выборки модели сохраняются старые значения атрибутов
	 *
	 * @param CEvent $event
	 */
	public function afterFind($event)
	{
		$this->setOldAttributes($this->owner->getAttributes());
	}

	/**
	 * Возвращает старые значения атрибутов
	 *
	 * @return array
	 */
	public function getOldAttributes()
	{
		return $this->_oldAttributes;
	}

	/**
	 * Устанавливает старые значения атрибутов
	 *
	 * @param $value
	 */
	public function setOldAttributes($value)
	{
		$this->_oldAttributes = $value;
	}
}