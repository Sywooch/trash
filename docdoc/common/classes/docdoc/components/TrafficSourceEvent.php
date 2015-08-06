<?php
namespace dfs\docdoc\components;

use Yii;
use CEvent;
use dfs\docdoc\models\TrafficParamsModel;

/**
 * Class TrafficSourceEvent
 *
 * Класс-регистратор событий TrafficSourceEvent
 *
 * @package dfs\docdoc\components
 */
class TrafficSourceEvent
{
	/**
	 * Сохранение параметров при создании заявки
	 *
	 * @param CEvent $event
	 */
	public function requestCreated(CEvent $event)
	{
		if(php_sapi_name() !== 'cli') {
			$params = Yii::app()->trafficSource->getParams(TrafficSourceComponent::CURRENT_VISIT);
			$request = $event->sender;

			TrafficParamsModel::model()->saveByParams($params, $request->req_id, TrafficParamsModel::OBJECT_REQUEST);
		}
	}

	/**
	 * Сохранение параметров при создании клиента
	 *
	 * @param CEvent $event
	 */
	public function clientCreated(CEvent $event)
	{
		if(php_sapi_name() !== 'cli') {
			$params = Yii::app()->trafficSource->getParams(TrafficSourceComponent::FIRST_VISIT);
			$client = $event->sender;

			TrafficParamsModel::model()->saveByParams($params, $client->clientId, TrafficParamsModel::OBJECT_CLIENT);
		}
	}
}
