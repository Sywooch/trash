<?php

/**
 * This is the model class for table "lf_price".
 *
 * The followings are the available columns in table 'lf_price':
 *
 * @property integer          $id
 * @property integer          $service_id
 * @property integer          $master_id
 * @property integer          $salon_id
 * @property integer          $price
 * @property integer          $price_from
 *
 * The followings are the available model relations:
 * @property LfMaster         $master
 * @property LfSalon          $salon
 * @property LfService        $service
 * @property LfSpecialization $specialization
 *
 * @method LfPrice findByPk
 * @method LfPrice find
 * @method LfPrice findByAttributes
 * @method LfPrice[] findAllByAttributes
 * @method LfPrice[] findAll
 */
class LfPrice extends CActiveRecord
{

	/**
	 * Количество записей в краткой анкете
	 *
	 * @var int
	 */
	const CARD_COUNT_PRICE = 5;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return LfPrice the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'lf_price';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('service_id', 'required'),
			array('service_id, master_id, salon_id, price, price_from', 'numerical', 'integerOnly' => true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, service_id, master_id, salon_id, price, price_from', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'salon'          => array(self::BELONGS_TO, 'LfSalon', 'salon_id'),
			'service'        => array(self::BELONGS_TO, 'LfService', 'service_id', 'together' => true),
			'specialization' => array(
				self::HAS_ONE,
				'LfSpecialization',
				array('specialization_id' => 'id'),
				'through' => 'service'
			),
			'works'          => array(
				self::HAS_MANY,
				'LfWork',
				array('master_id' => 'master_id', 'service_id' => 'service_id')
			)
		);
	}

	public function scopes()
	{
		return array(
			'ordered' => array(
				'with'  => array('specialization', 'service'),
				'order' => 'specialization.weight, specialization.name, service.weight, service.name ASC',
			),
		);
	}

	public function behaviors()
	{
		return array(
			'CArModTimeBehavior' => array(
				'class' => 'application.extensions.CArModTimeBehavior',
			)
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'         => 'ID',
			'service_id' => 'Service',
			'master_id'  => 'Master',
			'price'      => 'Price',
			'price_from' => 'Price From',
		);
	}

	/**
	 * Поиск в списке администраторов в БО
	 *
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('service_id', $this->service_id);
		$criteria->compare('master_id', $this->master_id);
		$criteria->compare('price', $this->price);
		$criteria->compare('price_from', $this->price_from);

		return new CActiveDataProvider($this, compact("criteria"));
	}

	public function getPriceFormatted($withFrom = true)
	{
		return $this->price ?
			($withFrom && $this->price_from ? '<span>от</span> ' : '') . number_format($this->price, 0, '.', ' ') : '';
	}

	public function toArray()
	{
		return array(
			'sum'  => (int)$this->price,
			'from' => (bool)$this->price_from,
		);
	}

	/**
	 * Вызывается после сохранения модели
	 *
	 * @return void
	 */
	public function afterSave()
	{
		if ($this->master_id) {
			$model = LfMaster::model()->findByPk($this->master_id);
			$model->updateRating();
		}

		parent::afterSave();
	}

	/**
	 * Вызывается после удаления модели
	 *
	 * @return void
	 */
	public function afterDelete()
	{
		if ($this->master_id) {
			$model = LfMaster::model()->findByPk($this->master_id);
			$model->updateRating();
		}

		parent::afterDelete();
	}

	/**
	 * Получает прайс
	 *
	 * @param LfMaster         $master         модель мастера
	 * @param LfSalon          $salon          модель салона
	 * @param LfSpecialization $specialization модель специальности
	 * @param LfService        $service        модель услуги
	 * @param boolean          $all            количество показываемых прайсов
	 *
	 * @return LfPrice[]
	 */
	public function getPrices(
		LfMaster $master = null,
		LfSalon $salon = null,
		LfSpecialization $specialization = null,
		LfService $service = null,
		$all = false
	)
	{
		$prices = array();

		$criteria = new CDbCriteria;
		$criteria->with = array("service");
		$criteria->condition = "t.price > :price";
		$criteria->params[":price"] = 0;
		if ($master) {
			if (!$master->salon_id) {
				$criteria->condition .= " AND t.master_id = :master_id";
				$criteria->params[":master_id"] = $master->id;
			} else {
				$criteria->condition .= " AND t.salon_id = :salon_id";
				$criteria->params[":salon_id"] = $master->salon_id;
			}
		}
		if ($salon) {
			$criteria->condition .= " AND t.salon_id = :salon_id";
			$criteria->params[":salon_id"] = $salon->id;
		}
		if ($service) {
			$criteria->condition .= " AND t.service_id = :service_id";
			$criteria->params[":service_id"] = $service->id;
		}
		$criteria->order = "service.specialization_id, service.weight";

		if(!$all) {
			$criteria->limit = self::CARD_COUNT_PRICE + 1;
		}

		if ($specialization) {
			$criteria->limit = self::CARD_COUNT_PRICE;
			$criteriaSpecialization = clone $criteria;

			if ($specialization->services) {
				$in = array();
				foreach ($specialization->services as $service) {
					$in[] = $service->id;
				}
				$criteriaSpecialization->addInCondition("t.service_id", $in);
			}

			$prices = $this->findAll($criteriaSpecialization);
		} else {
			$specializationId = Yii::app()->session['specializationId'];
			if ($specializationId) {
				$specialization = LfSpecialization::model()->findByPk($specializationId);
				if ($specialization) {
					if ($specialization->services) {
						$in = $specialization->getRelationIds("services");

						$criteriaSpecialization = clone $criteria;
						$criteriaSpecialization->addInCondition("t.service_id", $in);

						$criteriaGroup = clone $criteria;
						$criteriaGroup->addInCondition("t.service_id", $specialization->groupOne->getServicesIn());
						$criteriaGroup->addNotInCondition("t.service_id", $in);

						$criteria->addNotInCondition("t.service_id", $specialization->groupOne->getServicesIn());

						$prices = array_merge(
							$this->findAll($criteriaSpecialization),
							$this->findAll($criteriaGroup),
							$this->findAll($criteria)
						);
					}
				}

				Yii::app()->session['specializationId'] = null;
			} else {
				$prices = $this->findAll($criteria);
			}
		}

		return $prices;
	}
}