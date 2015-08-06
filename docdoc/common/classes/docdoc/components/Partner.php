<?php

namespace dfs\docdoc\components;

use dfs\docdoc\models\PartnerModel;
use Yii;

/**
 *  Класс Partner
 *  реализует компонент для партнера-реферала.
 *
 * Если посетитель пришел по ссылке с сайта партнера, этот компонент инициализирует партнера и сохраняет его в сессии
 * и реализует доступ к информации о партнере через Yii::app()->referral
 *
 *
 * Свойста, получаемые через магические методы при помощи геттеров
 *
 * @property PartnerModel $partner
 * @property integer      $id
 * @property string       $phone
 */
class Partner extends \CApplicationComponent
{
	/**
	 * Статус выполнения теста
	 */
	const AB_TEST_NOT_RUN = -1; // Тест не запущен
	const AB_TEST_A = 2;        // Старая площадка
	const AB_TEST_B = 1;        // Новоя площадка

	/**
	 * ID партнера для AB тестов
	 */
	const AB_TEST_PARTNER = 428;
	/**
	 * @var string ключ массива в сессии, в котором будет храниться $_partner
	 */
	public $sessParam = 'ReferralObj';
	/**
	 * @var string имя $_GET параметра, в котором передается id партнера
	 */
	public $getParam = 'pid';
	/**
	 * Время жизни куки в секундах
	 * По умолчанию 30 дней
	 *
	 * @var int
	 */
	public $cookieLifeTime = 2592000;
	/**
	 * Тесты проводятся под отдельным партнером с ID = 428
	 * В отдельной ветке этому партнеру ставится в конфиге runABTest=true
	 * и для этой ветки будут создаваться заявки под этим партнером и будет показываться телефон этого партнера
	 *
	 * @var bool
	 */
	public $runABTest = false;
	/**
	 * @var PartnerModel экземпляр модели партнера
	 */
	private $_partner;
	/**
	 * @var int идентификатор партнера
	 */
	private $_id;
	/**
	 * @var bool
	 */
	private $_isWidget = false;

	/**
	 * инициализация компонента
	 */
	public function init()
	{
		parent::init();
		$app = \Yii::app();
		if ($app->request->getQuery('widget', null) !== null) {
			$this->_isWidget = true;
		}

		$this->initPartner();

		if (empty($this->_id) && $this->runABTest && $app->city->isMoscow()) {
			$this->_id = self::AB_TEST_PARTNER;
			$app->session[$this->sessParam] = $this->_id;
			$this->getPartner();
		}
	}

	/**
	 * инициализация партера
	 */
	private function initPartner()
	{
		$app = \Yii::app();

		//проверяем передан ли id партнера
		if (!is_null($app->request->getQuery($this->getParam))) {
			$this->_id = $app->request->getQuery($this->getParam);

			if ($this->getPartner() !== null && !$this->_isWidget) {
				$app->session[$this->sessParam] = $this->_id;
				$this->setPartnerCookie();
			}

		} elseif ($app->request->cookies->contains($this->getParam)) {
			$this->_id = (string)$app->request->cookies[$this->getParam];

			if ($this->getPartner() !== null) {
				$app->session[$this->sessParam] = $this->_id;
			}

		} elseif (isset($app->session[$this->sessParam])) {
		 	//проверяем партнера, существует ли он в сессии
			$this->_id = $app->session[$this->sessParam];
			$this->setPartnerCookie();
		}

		if ($this->_partner !== null && $this->_partner->param_client_uid_name) {
			$this->checkPartnerClientUid($this->_partner->param_client_uid_name);
		}
	}

	/**
	 *  Геттер для модели партнера
	 *
	 * @return PartnerModel
	 */
	public function getPartner()
	{
		if ($this->_partner === null && $this->_id !== null) {
			$this->_partner = PartnerModel::model()
				->withoutSecureInfo()
				->with('phones')
				->findByPk($this->_id);

			if ($this->_partner === null) {
				$this->_id = null;
			}
		}

		return $this->_partner;
	}

	/**
	 * Ставит партнерскую куку
	 */
	public function setPartnerCookie()
	{
		if ($this->isABTest() > 0) {
			return;
		}

		if (php_sapi_name() !== 'cli' && !$this->_isWidget) {
			$cookie = new \CHttpCookie($this->getParam, $this->_id, ['expire' => time() + $this->cookieLifeTime]);
			\Yii::app()->request->cookies[$this->getParam] = $cookie;
		}
	}

	/**
	 * Признак проводится ли AB тест или нет
	 *
	 * 2 - площадка А
	 * 1 - площадка B
	 * -1  теста нет
	 *
	 * @return int
	 */
	public function isABTest()
	{
		//AB тест для партнеров не проводим
		if (!empty($this->_id) && $this->_id != self::AB_TEST_PARTNER) {
			return self::AB_TEST_NOT_RUN;
		}

		//AB тест только для москвы
		if (Yii::app()->city->isMoscow()) {
			return $this->_id == self::AB_TEST_PARTNER && $this->runABTest ? self::AB_TEST_B : self::AB_TEST_A;
		} else {
			return self::AB_TEST_NOT_RUN;
		}
	}

	/**
	 * Установка в куки идентификатора клиента переданого от партнера
	 *
	 * @param string $paramName
	 */
	private function checkPartnerClientUid($paramName)
	{
		$value = \Yii::app()->request->getQuery($paramName);
		if ($value) {
			\Yii::app()->request->cookies['partner_client_uid'] = new \CHttpCookie('partner_client_uid', $value, ['expire' => time() + $this->cookieLifeTime]);
		}
	}

	/**
	 * Возвращает id партнера
	 *
	 * @return int идентификатор патнера
	 */
	public function getId()
	{
		return $this->_id;
	}

	/**
	 * Установка партнера
	 *
	 * @param integer $id
	 */
	public function setId($id)
	{
		$this->_id = $id;
		$this->_partner = null;

		$this->getPartner();
	}

	/**
	 * Геттер для свойства phone
	 *
	 * @return string
	 */
	public function getPhone()
	{
		$partner = $this->getPartner();
		if (!$partner) {
			return null;
		}

		if (!$partner->phones) {
			return null;
		}

		foreach ($partner->phones as $partnerPhone) {
			if ($partnerPhone->city_id == Yii::app()->city->getCity()->id_city) {
				return $partnerPhone->phone->number;
			}
		}

		return null;
	}

	/**
	 * Логин партнера
	 *
	 * @return null|string
	 */
	public function getLogin()
	{
		$partner = $this->getPartner();

		return $partner ? $partner->login : null;
	}
}
