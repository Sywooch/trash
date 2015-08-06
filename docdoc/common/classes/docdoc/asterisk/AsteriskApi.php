<?php
namespace dfs\docdoc\asterisk;

use Exception;
use Yii;
use commonLog;

use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\CityModel;
use dfs\docdoc\models\PhoneModel;
use dfs\docdoc\models\SipChannelModel;
use dfs\docdoc\models\RequestHistoryModel;
use dfs\docdoc\models\RequestRecordModel;

/**
 * Класс обработки запросов от астериска
 *
 * Class AsteriskApi
 *
 * @package dfs\docdoc\asterisk
 */
class AsteriskApi
{
	/**
	 * Создание заявки по входящему звонку
	 *
	 * @param array $params
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function createRequest($params = array())
	{
		global $confPhonesForNoMergedRequests;

		$logFile = Yii::app()->params['asterisk']['api']['logFile'];

		$city = CityModel::model()->findCity($params['city']);

		$data = array();

		foreach ($params as $key => $value) {
			$data[] = "$key=$value";
		}

		$data = implode('; ', $data);

		new commonLog($logFile, "[Create Request Start] IN: {$data}");

		$phone = null;

		if (!empty($params['destinationPhone'])) {
			$phone = PhoneModel::model()->createPhone($params['destinationPhone']);

			if (!$phone) {
				trigger_error("Failed to add new phone number", E_USER_WARNING);
			}
		}

		$request = new RequestModel(RequestModel::SCENARIO_ASTERISK);
		$request->client_phone = $params['phone'];
		$request->id_city = $city->id_city;
		$request->last_call_id = $params['filename'];
		$request->queue = $params['queue'];

		if ($phone instanceof PhoneModel) {
			$request->destination_phone_id = $phone->id;
			$request->setPartnerFromDestinationPhone();
		}

		if (!empty($params['clinicId'])) {
			$request->clinic_id = $params['clinicId'];
		}

		$request = RequestModel::isSameRequest(
			$request,
			[ RequestModel::TYPE_WRITE_TO_DOCTOR, RequestModel::TYPE_PICK_DOCTOR, RequestModel::TYPE_CALL ],
			$confPhonesForNoMergedRequests
		);

		if (!$request->save()) {
			throw new Exception("Failed to create request");
		}

		new commonLog($logFile, "[Create Request End] Success");

		return $request->req_id;
	}

	/**
	 * Добавление аудиозаписи для заявки
	 *
	 * @param array $params
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function addRecord($params = array())
	{
		$requestId = $params['requestId'];
		$filename = $params['filename'];
		$recordType = $params['recordType'];

		$logFile = Yii::app()->params['asterisk']['api']['logFile'];

		new commonLog($logFile, "[Create Record Start] IN: requestId={$requestId}; filename={$filename}; recordType={$recordType}");

		if ($requestId > 0 && !empty($filename)) {
			$request = RequestModel::model()->findByPk($requestId);

			if (!$request) {
				throw new Exception('Request not found');
			}

			$record = new RequestRecordModel();

			$record->request_id = $requestId;
			$record->clinic_id = 0;
			$record->record = $filename . '.mp3';
			$record->crDate = date('Y-m-d H:i:s');
			$record->year = date('Y');
			$record->month = date('n');

			switch ($recordType) {
				case 'TRANSFER':
					$record->type = RequestRecordModel::TYPE_TRANSFER;
					$record->clinic_id = $request->transferred_clinic_id;
					break;

				case 'IN':
					$record->type = RequestRecordModel::TYPE_IN;
					break;

				case 'OUT':
					$record->type = RequestRecordModel::TYPE_OUT;
					break;

				default:
					$record->type = RequestRecordModel::TYPE_UNDEFINED;
					break;
			}

			if (!$record->save()) {
				throw new Exception("Failed to add record");
			}

			$this->saveLog($requestId, "Звонок состоялся. Заявка: {$requestId}. Запись: {$filename}", 0, RequestHistoryModel::LOG_TYPE_ACTION);

			$sipChannel = SipChannelModel::model()->findByAttributes([ 'request_id' => $requestId ]);
			if ($sipChannel && $sipChannel->active) {
				$sipChannel->active = 0;
				$sipChannel->save();
			}

			new commonLog($logFile, "[Create Record End] Success");

			return true;
		}

		throw new Exception("Incorrect requestId or filename");
	}

	/**
	 * Добавление SIP и канала для текущего разговора
	 *
	 * @param array $params
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function addChannel($params = array())
	{
		$sip = $params['sip'];
		$channel = $params['channel'];
		$requestId = $params['requestId'];

		$logFile = Yii::app()->params['asterisk']['api']['logFile'];

		new commonLog($logFile, '[Add Channel Start] IN: sip=' . $sip . '; channel=' . $channel . '; request_id=' . $requestId);

		if (!$sip || !$channel) {
			throw new Exception('Incorrect sip or channel');
		}

		$sipChannel = SipChannelModel::model()->findByPk($sip);

		if (!$sipChannel) {
			$sipChannel = new SipChannelModel();
			$sipChannel->sip = $sip;
		}

		$sipChannel->channel = $channel;
		$sipChannel->request_id = $requestId;

		if (!$sipChannel->save()) {
			throw new Exception('Failed to add new channel');
		}

		if ($requestId > 0) {
			$this->saveLog($requestId, 'Оператор поднял трубку по заявке', 0, RequestHistoryModel::LOG_TYPE_ACTION);
		}

		new commonLog($logFile, '[Add Channel End] Success');

		return true;
	}

	/**
	 * Сохранение логов
	 *
	 * @param int    $requestId
	 * @param string $message
	 * @param int    $owner
	 * @param int    $type
	 *
	 * @return bool
	 */
	private function saveLog($requestId, $message, $owner, $type = RequestHistoryModel::LOG_TYPE_CHANGE_STATUS)
	{
		$requestHistory = new RequestHistoryModel();

		$requestHistory->request_id = $requestId;
		$requestHistory->action = $type;
		$requestHistory->user_id = $owner;
		$requestHistory->text = $message;

		return $requestHistory->save();
	}
} 
