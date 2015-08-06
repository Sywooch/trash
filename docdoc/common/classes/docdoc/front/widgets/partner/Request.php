<?php
/**
 * Created by PhpStorm.
 * User: ndunaev
 * Date: 02.12.14
 * Time: 16:16
 */

namespace dfs\docdoc\front\widgets\partner;
use dfs\docdoc\exceptions\SpamException;
use dfs\docdoc\models\RequestModel;
use CHtml;

/**
 * Class Modal
 *
 * @package dfs\docdoc\front\widgets\partner
 *
 */
class Request extends PartnerWidget
{
	/**
	 * имя виджета
	 * @var string
	 */
	public $name = 'Request';

	/**
	 * #############################################
	 * параметры, которые берутся из адресной строки
	 * #############################################
	 */

	/**
	 * Номер телефона клиента
	 *
	 * @var null
	 */
	public $phone = null;

	/**
	 * Сектор
	 *
	 * @var null
	 */
	public $sector = null;

	/**
	 * Имя клиента
	 *
	 * @var null
	 */
	public $clientName = null;

	/**
	 * ID клиники
	 *
	 * @var null
	 */
	public $clinicId = null;

	/**
	 * ID врача
	 *
	 * @var null
	 */
	public $doctorId = null;

	/**
	 * Имя виджета, который инициировал создание заявки
	 *
	 * @var null
	 */
	public $srcWidget = null;

	/**
	 * Имя виджета, который инициировал создание заявки
	 *
	 * @var null
	 */
	public $srcTemplate = null;


	/**
	 * ############################
	 * внутренние свойств виджета
	 * ###########################
	 */

	/**
	 * Статус создания заявки
	 *
	 * @var bool
	 */
	protected $_createRequestStatus = false;

	/**
	 * инициализация виджета
	 */
	public function init()
	{
		parent::init();

		$this->sectorModel = $this->getSectorFromParam($this->sector, 'byRewriteName');
	}

	/**
	 * Действия при загрузке виджета
	 */
	public function loadWidget()
	{

	}

	/**
	 * создание заявки
	 *
	 */
	public function createRequest()
	{
		if (!empty($this->phone)) {
			$model = new RequestModel(RequestModel::SCENARIO_SITE);
			$model->client_name = $this->clientName;
			$model->client_phone = $this->phone;
			$model->clinic_id = $this->clinicId;
			$model->req_doctor_id = $this->doctorId;
			$model->partner_id = $this->partner->id;
			if ($this->cityModel) {
				$model->id_city = $this->cityModel->id_city;
			}
			try {
				$this->_createRequestStatus = $model->save();
			} catch (SpamException $e) {
				// В случае спама отправляем успешный ответ
				$this->_createRequestStatus = true;
			}
		}
	}

	/**
	 * Геттер для $this->_createRequestStatus
	 *
	 * @return bool
	 */
	public function getCreateRequestStatus()
	{
		return $this->_createRequestStatus;
	}

	/**
	 * Текстовое поле для имени клиента
	 *
	 * @return string
	 */
	public function getClientNameTextField()
	{
		return  CHtml::textField(
			"dd-name-request",
			$this->clientName,
			array(
				"placeholder" => "Ваше имя",
				"class"       => "dd-request-text-field dd-request-text-field-name"
			)
		);
	}

	/**
	 * Текстовое поле для номера телефона
	 *
	 * @return string
	 */
	public function getPhoneTextField()
	{
		return CHtml::textField(
			"dd-phone-request",
			$this->phone,
			array(
				"placeholder" => "Ваш телефон",
				"class"       => "dd-request-text-field dd-request-text-field-phone"
			)
		);
	}
}