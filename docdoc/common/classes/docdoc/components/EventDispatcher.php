<?php
namespace dfs\docdoc\components;

use CLogger;

/**
 * Class EventListener
 *
 * Глобальный диспетчер событий
 *
 */
class EventDispatcher extends \CApplicationComponent
{
	/**
	 * Массив с событиями в системе.
	 *
	 * задаются в конфиге через компонент eventDispatcher
	 *
	 * 'components' =>array(
	 *		'eventDispatcher' => array(
				'class' => 'dfs\docdoc\components\EventDispatcher',
				'events' => array(
					'onRequestSave'=>array(
						'dfs\docdoc\models\RequestHistoryModel'=>'saveLog'
					),
				),
			),
	 *	)
	 *
	 * Структура массива:
	 * 		"имяСобытия1" => array(
	 * 			"классОбработчик1" => "методОбработчик1",
	 * 			"классОбработчик2" => "методОбработчик2",
	 * 		)
	 *
	 * @var array
	 */
	public $events = array();

	/**
	 * Обработка событий
	 *
	 * события вызываются при помощи Yii::app()->eventDispatcher->имяСобытия1(new \CEvent())
	 * Данный вызов попадает в метод __call
	 *
	 * @param string $name
	 * @param array  $parameters
	 *
	 * @return void
	 *
	 */
	public function  __call($name, $parameters)
	{
		if (!isset($this->events[$name])) {
			\Yii::log("Вызвано событие {$name}, параметры которого не описаны в конфигурации", CLogger::LEVEL_ERROR);
			return;
		}

		foreach ($this->events[$name] as $subscriber => $method) {
			(new $subscriber)->$method($parameters[0]);
		}
	}

	/**
	 * Замена методу __call
	 *
	 * @param string  $eventName
	 * @param \CEvent|\CComponent $event по сути сюда можно передать все что хочешь
	 */
	public function raiseEvent($eventName, $event)
	{
		if (isset($this->events[$eventName])) {

			foreach ($this->events[$eventName] as $subscriber => $method) {
				(new $subscriber)->$method($event);
			}

		} else {
			\Yii::log("Не найден обработчик события {$eventName}", CLogger::LEVEL_ERROR);
		}
	}
}
