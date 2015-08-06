<?php


namespace likefifa\components\extensions;

use CApplicationComponent;
use CException;
use Racecore\GATracking\GATracking;
use Racecore\GATracking\Tracking\Event;
use Yii;

class GaTrackingComponent extends CApplicationComponent
{
	/**
	 * Идентификатор в GA
	 *
	 * @var string
	 */
	public $accountId;

	/**
	 * @var GATracking
	 */
	private $_t;

	public function init()
	{
		if ($this->accountId == null && Yii::app()->params->contains('gaAccount')) {
			$this->accountId = Yii::app()->params['gaAccount'];
		}

		$this->_t = new GATracking($this->accountId, false);

		parent::init();
	}

	/**
	 * Трекинг события
	 *
	 * Yii::app()->gaTracking->trackEvent('category', 'action', 'label', 'value');
	 *
	 * @param string $category
	 * @param string $action
	 * @param string $label
	 * @param string $value
	 *
	 * @return boolean
	 */
	public function trackEvent($category, $action, $label = null, $value = null)
	{
		$event = new Event();
		$event->setEventCategory($category);
		$event->setEventAction($action);

		if ($label != null) {
			$event->setEventLabel($label);
		}

		if ($value != null) {
			$event->setEventValue($value);
		}

		return $this->_t->sendTracking($event);
	}

	/**
	 * Возвращает идентификатор пользователя
	 * @return integer
	 */
	public function getUserId() {
		if(($ga = Yii::app()->request->cookies['_ga']) != null) {
			$ga = explode('.', $ga->value);

			if(isset($ga[2])) {
				return $ga[2];
			}
		}

		return null;
	}

	/**
	 * Call a GATracking functions
	 *
	 * @param string $method
	 * @param array  $params
	 *
	 * @return mixed
	 * @throws CException
	 */
	public function __call($method, $params)
	{
		if (is_object($this->_t) && get_class($this->_t) === 'GATracking') {
			return call_user_func_array(array($this->_t, $method), $params);
		} else {
			throw new CException(Yii::t('GATracking', 'Can not call a method of a non existent object'));
		}
	}

	/**
	 * @param string $name
	 * @param mixed  $value
	 *
	 * @return mixed|void
	 * @throws CException
	 */
	public function __set($name, $value)
	{
		if (is_object($this->_t) && get_class($this->_t) === 'GATracking') {
			$this->_t->$name = $value;
		} else {
			throw new CException(Yii::t('GATracking', 'Can not set a property of a non existent object'));
		}
	}

	/**
	 * @param string $name
	 *
	 * @return mixed
	 * @throws CException
	 */
	public function __get($name)
	{
		if (is_object($this->_t) && get_class($this->_t) === 'GATracking') {
			return $this->_t->$name;
		} else {
			throw new CException(Yii::t('GATracking', 'Can not access a property of a non existent object'));
		}
	}
}