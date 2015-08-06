<?php

namespace dfs\docdoc\models;
use dfs\common\config\Environment;
use Yii;

/**
 * This is the model class for table "request_history".
 *
 * The followings are the available columns in table 'request_history':
 *
 * @property integer $id
 * @property integer $request_id
 * @property string $created
 * @property integer $action
 * @property integer $user_id
 * @property string $text
 *
 *
 * The followings are the available model relations
 *
 * @method RequestHistoryModel findByPk
 */
class RequestHistoryModel extends \CActiveRecord
{
	/**
	 * Тип действия в логах
	 */
	const LOG_TYPE_ACTION = 1;
	const LOG_TYPE_COMMENT = 2;
	const LOG_TYPE_CHANGE_STATUS = 3;
	const LOG_TYPE_NOTIFY_BY_ASTERISK = 4;


	/**
	 * @var RequestModel
	 */
	public $request;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return ClinicModel the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'request_history';
	}

	/**
	 * обработка события onRequestSave при сохранении заявки
	 *
	 * @param $event
	 */
	public function saveLog($event)
	{
		$this->request = $event->sender;
		$this->addLog($this->getText());
	}

	/**
	 * сохранение сообщения в историю
	 * @param $text
	 */
	public function addLog($text)
	{
		$this->request_id =  $this->request->req_id;
		$this->text = $text;
		$this->user_id = $this->getUserId();

		if (!$this->action) {
			$this->action = $this->getAction();
		}

		//если не заданы правила логирования, значит, логировать не нужно
		if (empty($this->text)) {
			return;
		}

		$this->save();
	}

	/**
	 * обработка события onRequestStatusChange
	 *
	 * @param $event
	 */
	public function saveChangeStatusLog($event)
	{
		$this->request = $event->sender;
		$this->request_id =  $this->request->req_id;
		$this->action = self::LOG_TYPE_CHANGE_STATUS;
		$this->text = $this->getChangeStatusText();
		$this->user_id =  $this->getUserId();

		//если не заданы правила логирования, значит, логировать не нужно
		if ($this->text === '') {
			return;
		}

		$this->save();
	}


	/**
	 * Возвращает код действия с заявкой в зависимости от $this->scenario
	 *
	 * @return int
	 */
	public function getAction()
	{
		$action = 0;

		//TODO при создании нового сценария заполнить правило его логирования
		switch ($this->request->scenario) {

			case RequestModel::SCENARIO_SITE:
			case RequestModel::SCENARIO_CALL:
			case RequestModel::SCENARIO_ASTERISK:
			case RequestModel::SCENARIO_PARTNER:
			case RequestModel::SCENARIO_DIAGNOSTIC_ONLINE:
				$action = self::LOG_TYPE_ACTION;
				break;
			case RequestModel::SCENARIO_SAVE_CLINIC:
			case RequestModel::SCENARIO_RECORD_APPOINTMENT:
			case RequestModel::SCENARIO_OPERATOR:
				$action = self::LOG_TYPE_CHANGE_STATUS;
				break;

		}

		return $action;
	}


	/**
	 * Возвращает код действия с заявкой в зависимости от $this->scenario
	 *
	 * @return int
	 */
	public function getText()
	{
		$txt = '';
		//TODO при создании нового сценария заполнить правило его логирования
		switch ($this->request->scenario) {

			case RequestModel::SCENARIO_DIAGNOSTIC_ONLINE:
				$txt = 'Создание заявки с сайта диагностики (IP: ' . Environment::getIp() . ')';
				break;
			case RequestModel::SCENARIO_SITE:
			case RequestModel::SCENARIO_CALL:
				$txt = 'Создание заявки с сайта (IP: ' . Environment::getIp() . ')';
				break;
			case RequestModel::SCENARIO_ASTERISK:
				$txt = 'Входящий звонок с номера ' . $this->request->client_phone;
				break;
			case RequestModel::SCENARIO_SAVE_CLINIC:
				$txt = 'Изменена клиника на  ' . $this->request->clinic->name;
				break;
			case RequestModel::SCENARIO_OPERATOR:
				if (!$this->request->getIsNewRecord()) {
					foreach ($this->request->getAttributes() as $attr => $value) {
						if ($this->request->isChanged($attr)) {

							$original = $this->request->getOriginalValue($attr);
							$txt .= "значение {$attr} изменено с {$original} на {$value}, ";
						}
					}
				} else {
					$txt = "Создана заявка в БО";
				}
				break;

		}

		return $txt;
	}

	/**
	 * Возвращает код действия с заявкой в зависимости от $this->scenario
	 *
	 * @return int
	 */
	public function getUserId()
	{
		$userId = 0;

		//TODO при создании нового сценария заполнить правило его логирования
		switch ($this->request->scenario) {

			case RequestModel::SCENARIO_SITE:
			case RequestModel::SCENARIO_ASTERISK:
			case RequestModel::SCENARIO_CALL:
				$userId = 0;
				break;
			case RequestModel::SCENARIO_SAVE_CLINIC:
			case RequestModel::SCENARIO_RECORD_APPOINTMENT:
			case RequestModel::SCENARIO_OPERATOR:
			case RequestModel::SCENARIO_CHANGE_STATUS:
				$userId = $this->getBackOfficeUser();
				break;

		}
		return $userId;
	}

	/**
	 * Возвращает текст при изменении статуса заявки
	 *
	 * @return int
	 */
	public function getChangeStatusText()
	{
		$statuses = RequestModel::getStatusList();
		return "Изменен статус -> '" . $statuses[$this->request->req_status] . "'";
	}

	/**
	 * Определяет ID пользователя  БО
	 *
	 * @todo Пока что сделан костыль, который берет из сессии
	 *       необходимо сделать через Yii::app()->boUser->id;
	 *
	 */
	private function getBackOfficeUser()
	{
		$user = Yii::app()->session['user'];

		return is_object($user) ? $user->idUser : 0;
	}

	/**
	 * Возвращает правила проверки для атрибутов модели
	 *
	 * @return array
	 */
	public function rules()
	{
		return array(
			[
				'action, user_id',
				'numerical',
				'integerOnly' => true
			],
			[
				'text, created',
				'length'
			]
		);
	}
}
