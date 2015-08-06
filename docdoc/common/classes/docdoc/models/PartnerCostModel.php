<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 25.06.14
 * Time: 15:29
 */

namespace dfs\docdoc\models;

use CActiveDataProvider;
use CDbCriteria;


/**
 * Class PartnerCostModel
 *
 * @property int $id
 * @property int $partner_id
 * @property int $service_id
 * @property int $city_id
 * @property float $cost
 *
 * @property PartnerModel $partner модель партнера
 * @property CityModel    $city
 *
 * @method PartnerCostModel find
 * @method PartnerCostModel findByAttributes
 * @method PartnerCostModel[] findAll
 */
class PartnerCostModel extends \CActiveRecord
{
	/**
	 * Для кеша
	 *
	 * @var array
	 */
	private $_cache = [];

	/**
	 * @return string
	 */
	public function tableName()
	{
		return 'partner_cost';
	}

	/**
	 * @return mixed|string|void
	 */
	public function primaryKey()
	{
		return 'id';
	}

	/**
	 * @param string $className
	 *
	 * @return PartnerCostModel
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model(__CLASS__);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array(
				'cost',
				'numerical',
				'allowEmpty' => false,
			),
			array(
				'partner_id, city_id, service_id',
				'numerical',
				'integerOnly' => true,
				'allowEmpty' => true
			),
			array(
				'id, partner_id, service_id, city_id, cost',
				'safe',
				'on' => 'search'
			)
		);
	}

	/**
	 * Определение стоимости заявки для партнера
	 *
	 * @param RequestModel $request
	 * @return float
	 */
	public function getRequestCost(RequestModel $request)
	{
		$this->partner_id = $request->partner_id;
		$this->city_id = $request->id_city;

		if (empty($this->partner_id) || $request->partner_status != RequestModel::PARTNER_STATUS_ACCEPT) {
			return 0;
		}

		$this->service_id = null;

		if($request->kind == RequestModel::KIND_DOCTOR) {
			$this->service_id = ServiceModel::TYPE_SUCCESSFUL_DOCTOR_REQUEST;
		} elseif ($request->kind == RequestModel::KIND_DIAGNOSTICS){
			if($request->diagnostics_id){
				$diagnostics = DiagnosticaModel::model()->findByPk($request->diagnostics_id);

				if($diagnostics){
					$this->service_id = $diagnostics->getServiceId();
				}
			} else {
				$this->service_id = ServiceModel::TYPE_SUCCESSFUL_DIAGNOSTICS_OTHER;
			}
		}

		return $this->getCost();
	}

	/**
	 * Получает цену вознаграждения партнера
	 *
	 * @return float
	 */
	public function getCost()
	{
		if (!$this->service_id || !$this->partner_id) {
			return 0;
		}

		$key = $this->service_id . '_' . $this->partner_id . '_' . intval($this->city_id);

		if (!isset($this->_cache[$key])) {
			$partnerCost = self::model()->find([
				'condition' =>
					'(t.service_id = :service_id) AND ' .
					'(t.partner_id = :partner_id OR t.partner_id IS NULL) AND ' .
					'(t.city_id = :city_id OR t.city_id IS NULL)',
				'params' => [
					':service_id' => $this->service_id,
					':partner_id' => $this->partner_id,
					':city_id' => $this->city_id,
				],
				'order' => 't.partner_id DESC, t.city_id DESC',
			]);

			$this->_cache[$key] = $partnerCost ? $partnerCost->cost : 0;
		}

		return $this->_cache[$key];
	}

	/**
	 * Добавляет в выборку город
	 *
	 * @return PartnerCostModel
	 */
	public function withCity()
	{
		$criteria = new CDbCriteria;

		$criteria->with = [
			'city' => [
				'joinType' => 'LEFT JOIN',
			],
		];
		$criteria->together = true;

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Добавляет в выборку условие, что только для партнера
	 *
	 * По умолчанию берется партнер $this->partner_id
	 * Если указан параметр $partnerId, то берется он
	 *
	 * Внутри метода есть цикл, который убирает лишние значения, а именно
	 * если есть 2 строки с partner_id == null и partner_id != null,
	 * то убирается строка с partner_id == null
	 *
	 * @param integer $partnerId идентификатор партнера
	 *
	 * @return PartnerCostModel[]
	 */
	public function findAllForPartner($partnerId = null)
	{
		$criteria = new CDbCriteria;
		$criteria->condition = "t.partner_id = :partner_id OR t.partner_id IS NULL";
		$criteria->params["partner_id"] = !is_null($partnerId) ? $partnerId : $this->partner_id;
		$criteria->order = "t.city_id, t.service_id";

		$models = [];
		$all = $this->findAll($criteria);
		foreach ($all as $model) {
			if ($model->partner_id) {
				$models[] = $model;
			} else {
				$isExistNotNull = false;
				foreach ($all as $checkModel) {
					if (
						$checkModel->partner_id
						&& $checkModel->service_id == $model->service_id
						&& $checkModel->city_id == $model->city_id
					) {
						$isExistNotNull = true;
					}
				}
				if (!$isExistNotNull) {
					$models[] = $model;
				}
			}
		}

		return $models;
	}

	/**
	 * Отчистка кеша
	 *
	 * @return $this
	 */
	public function clearCache()
	{
		$this->_cache = [];

		return $this;
	}

	/**
	 * Поиск
	 *
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->with = [
			'partner' => [
				'joinType' => 'left join',
			],
			'city' => [
				'joinType' => 'left join',
			]
		];
		$criteria->together = true;

		$criteria->compare('t.id', $this->id);
		$criteria->compare('t.partner_id', $this->partner_id);
		$criteria->compare('t.service_id', $this->service_id);
		$criteria->compare('t.city_id', $this->city_id);
		$criteria->compare('t.cost', $this->cost);

		return new CActiveDataProvider(
			$this,
			array(
				'criteria'   => $criteria,
				'pagination' => array(
					'pageSize' => 50,
				),
			)
		);
	}

	/**
	 * Названия меток для атрибутов
	 *
	 * @return string[]
	 */
	public function attributeLabels()
	{
		return array(
			'id'         => 'ID',
			'partner_id' => 'Партнер',
			'service_id' => 'Услуга',
			'city_id'    => 'Город',
			'cost'       => 'Стоимость',
		);
	}

	/**
	 * Возвращает связи между объектами
	 *
	 * @return array
	 */
	public function relations()
	{
		return array(
			'partner' => [ self::BELONGS_TO, PartnerModel::class, 'partner_id' ],
			'city' => [ self::BELONGS_TO, CityModel::class, 'city_id' ],
		);
	}

	/**
	 * Получает название услуги
	 *
	 * @return string
	 */
	public function getServiceName()
	{
		if (array_key_exists($this->service_id, ServiceModel::$service_types)) {
			return ServiceModel::$service_types[$this->service_id];
		}

		return "Любая услуга";
	}

	/**
	 * Выполняется перед валидацией модели
	 *
	 * @return bool
	 */
	protected function beforeValidate()
	{
		// Присвоение NULL
		if (!$this->partner_id) {
			$this->partner_id = NULL;
		}
		if (!$this->service_id) {
			$this->service_id = NULL;
		}
		if (!$this->city_id) {
			$this->city_id = NULL;
		}

		// Проверка на повторную комбинацию
		$criteria = new CDbCriteria;
		if ($this->id) {
			$criteria->addCondition("t.id != :id");
			$criteria->params["id"] = $this->id;
		}
		if ($this->partner_id) {
			$criteria->addCondition("t.partner_id = :partner_id");
			$criteria->params["partner_id"] = $this->partner_id;
		} else {
			$criteria->addCondition("t.partner_id IS NULL");
		}
		if ($this->service_id) {
			$criteria->addCondition("t.service_id = :service_id");
			$criteria->params["service_id"] = $this->service_id;
		} else {
			$criteria->addCondition("t.service_id IS NULL");
		}
		if ($this->city_id) {
			$criteria->addCondition("t.city_id = :city_id");
			$criteria->params["city_id"] = $this->city_id;
		} else {
			$criteria->addCondition("t.city_id IS NULL");
		}

		if ($this->find($criteria)) {
			$this->addError("unique", "Такая комбинация уже существует");
		}

		return parent::beforeValidate();
	}

	/**
	 * Вызывается после создания модели
	 *
	 * @return void
	 */
	protected function afterFind()
	{
		parent::afterFind();

		$this->cost = number_format($this->cost, 2);
	}
} 
