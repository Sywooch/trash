<?php

namespace dfs\docdoc\models;

/**
 * This is the model class for table "diagnostica4clinic".
 *
 * The followings are the available columns in table 'diagnostica4clinic':
 * @property integer $id
 * @property integer $diagnostica_id
 * @property integer $clinic_id
 * @property double $price
 * @property double $special_price
 * @property double $price_for_online
 *
 * @property DiagnosticaModel $diagnostic
 * @property ClinicModel $clinic
 * @property ModerationModel $moderation
 *
 * @method DiagnosticClinicModel find
 * @method DiagnosticClinicModel findByPk
 * @method DiagnosticClinicModel findByAttributes
 * @method DiagnosticClinicModel[] findAll
 * @method DiagnosticClinicModel cache
 * @method DiagnosticClinicModel with
 */
class DiagnosticClinicModel extends \CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return DiagnosticClinicModel the static model class
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
		return 'diagnostica4clinic';
	}

	/**
	 * Получает имя первичного ключа
	 *
	 * @return string
	 */
	public function primaryKey()
	{
		return 'id';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('diagnostica_id, clinic_id', 'numerical', 'integerOnly'=>true),
			array('price, special_price, price_for_online', 'numerical'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, diagnostica_id, clinic_id, price, special_price', 'safe', 'on'=>'search'),
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
			'clinic' => array(self::BELONGS_TO, 'dfs\docdoc\models\ClinicModel', 'clinic_id'),
			'diagnostic' => array(self::BELONGS_TO, 'dfs\docdoc\models\DiagnosticaModel', 'diagnostica_id'),
			'moderation' => [
				self::HAS_ONE,
				ModerationModel::class,
				'entity_id',
				'condition' => 'entity_class = "DiagnosticClinicModel"'
			],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'             => 'ID',
			'diagnostica_id' => 'Diagnostica',
			'clinic_id'      => 'Clinic',
			'price'          => 'Price',
			'special_price'  => 'Special Price',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return \CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new \CDbCriteria;

		$criteria->compare('diagnostica_id',$this->diagnostica_id);
		$criteria->compare('clinic_id',$this->clinic_id);
		$criteria->compare('price',$this->price);
		$criteria->compare('special_price',$this->special_price);

		return new \CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}


	/**
	 * Поиск по клинике
	 *
	 * @param int $clinicId
	 *
	 * @return $this
	 */
	public function byClinic($clinicId)
	{
		$this->getDbCriteria()->mergeWith([
			'condition' => $this->getTableAlias() . '.clinic_id = :clinicId',
			'params' => [
				'clinicId' => $clinicId,
			],
		]);

		return $this;
	}

	/**
	 * Поиск по филиалам
	 *
	 * @param int[] $clinicIds
	 *
	 * @return $this
	 */
	public function inClinics($clinicIds)
	{
		$this->getDbCriteria()->addInCondition('clinic_id', $clinicIds);

		return $this;
	}

	/**
	 * Получает список диагностик для клиники
	 *
	 * @param int $clinicId идентификатор клиники
	 *
	 * @return array
	 */
	public function findAllForClinic($clinicId)
	{
		$sql = "SELECT t2.id, t2.name as name, t3.name as parentName, t1.price, t1.special_price
				FROM diagnostica4clinic t1
					INNER JOIN diagnostica t2 ON (t2.id = t1.diagnostica_id)
					LEFT JOIN diagnostica t3 ON (t3.id = t2.parent_id)
				WHERE t1.clinic_id = :clinicId AND t1.price > 0
				ORDER BY t2.id";

		return $this->dbConnection
			->createCommand($sql)
			->bindValue('clinicId', $clinicId)
			->queryAll();
	}

	/**
	 * Получение скидки на услугу
	 *
	 * @return float
	 */
	public function getDiscountForOnline()
	{
		$price = $this->special_price > 0 ? $this->special_price : $this->price;

		return $price > 0 ? ceil((1 - ($this->price_for_online / $price)) * 100) : null;
	}

	/**
	 * @return bool
	 */
	public function beforeSave()
	{
		parent::beforeSave();

		$price = $this->special_price > 0 ? $this->special_price : $this->price;
		if ($this->price_for_online >= $price) {
			$this->price_for_online = 0;
		}

		return true;
	}

}
