<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 15.09.14
 * Time: 11:42
 */

namespace dfs\docdoc\components;


use dfs\docdoc\models\RatingStrategyModel;

class RatingComponent extends \CApplicationComponent
{
	/**
	 * Параметры для каждого типа стратегии
	 *    cookie - имя куки, в которой передается id стратегии
	 *
	 * @var array
	 */
	protected $_strategyParams = [
		RatingStrategyModel::FOR_DOCTOR => [
			'cookie' => 'rsid',
			'default' => 1,
		],
		RatingStrategyModel::FOR_CLINIC => [
			'cookie' => 'rscid',
			'default' => 1,
		],
	];

	/**
	 * @var RatingStrategyModel[]
	 */
	protected $_ratingStrategy = [];

	/**
	 * Время жизни куки в секундах
	 * По умолчанию 1 год
	 *
	 * @var int
	 */
	public $cookieLifeTime = 31536000;


	/**
	 * Гетер для имени куки
	 *
	 * @param int $type
	 *
	 * @return string
	 */
	public function getCookieParam($type)
	{
		return isset($this->_strategyParams[$type]['cookie']) ? $this->_strategyParams[$type]['cookie'] : null;
	}

	/**
	 * инициализация компонента
	 */
	public function init()
	{
		parent::init();

		$cookies = \Yii::app()->request->cookies;

		foreach ($this->_strategyParams as $type => $params) {
			if ($cookies->contains($params['cookie'])) {
				$this->setId($cookies[$params['cookie']], $type);
			}

			if (empty($this->_ratingStrategy[$type])) {
				$this->findAndSetStrategy($type);

				if (empty($this->_ratingStrategy[$type]) && !empty($params['default'])) {
					$this->setId($params['default'], $type);
				}
			}
		}
	}

	/**
	 * Ставит куку
	 *
	 * @param int $type
	 */
	protected function setCookie($type)
	{
		if (php_sapi_name() !== 'cli') {
			$cookieName = $this->getCookieParam($type);
			if ($cookieName) {
				$cookie = new \CHttpCookie($cookieName, $this->getId($type), ['expire' => time() + $this->cookieLifeTime]);
				\Yii::app()->request->cookies[$cookieName] = $cookie;
			}
		}
	}

	/**
	 * Поиск и установка стратегии
	 *
	 * @param int $type
	 */
	protected function findAndSetStrategy($type)
	{
		$strategy = RatingStrategyModel::model()
			->active()
			->byObjectType($type)
			->random()
			->find();

		if ($strategy) {
			$this->_ratingStrategy[$type] = $strategy;
			$this->setCookie($type);
		}
	}

	/**
	 * @param int $type
	 *
	 * @return RatingStrategyModel|null
	 */
	public function getRatingStrategy($type)
	{
		return isset($this->_ratingStrategy[$type]) ? $this->_ratingStrategy[$type] : null;
	}

	/**
	 * Гетер для id
	 *
	 * @param int $type
	 *
	 * @return int
	 */
	public function getId($type)
	{
		return isset($this->_ratingStrategy[$type]) ? $this->_ratingStrategy[$type]->id : null;
	}

	/**
	 * Установить стратегию
	 *
	 * @param int $id
	 * @param int $type
	 *
	 * @return $this
	 */
	public function setId($id, $type)
	{
		$this->_ratingStrategy[$type] = RatingStrategyModel::model()
			->active()
			->byObjectType($type)
			->cache(3600)
			->findByPk($id);

		return $this;
	}

	/**
	 * Устанавливает стратегию из конфига
	 *
	 * @return $this
	 */
	public function setFromConfig()
	{
		$config = \Yii::app()->params['api']['rest']['strategy'];

		if ($config) {
			foreach ($config as $type => $strategyId) {
				$this->setId($strategyId, $type);
			}
		}

		return $this;
	}
} 
