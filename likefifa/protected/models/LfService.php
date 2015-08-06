<?php
use likefifa\models\CityModel;

/**
 * This is the model class for table "lf_service".
 *
 * The followings are the available columns in table 'lf_service':
 *
 * @property integer          $id
 * @property integer          $specialization_id
 * @property string           $name
 * @property string           $rewrite_name
 * @property int			  $price_from
 * @property string           $unit
 *
 * The followings are the available model relations:
 * @property LfMasterPrice[]  $lfMasterPrices
 * @property LfSpecialization $specialization
 *
 * @method LfService   findByPk
 * @method LfService[] findAll
 * @method LfService   filtered
 * @method string      getRewriteName()
 */
class LfService extends CActiveRecord
{
	protected $SearchEntity = 'masters';

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return LfService the static model class
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
		return 'lf_service';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('specialization_id, name', 'required'),
			array('specialization_id, weight, price_from', 'numerical', 'integerOnly' => true),
			array('name, rewrite_name, dative_name, genitive_name', 'length', 'max' => 256, 'allowEmpty' => true),
			array('unit', 'length', 'max' => 45),
			array(
				'rewrite_name',
				'match',
				'pattern' => '/^[a-z_-]+$/',
				'message' => 'Используйте только символы латинского алфавита в нижнем регистре, нижнее подчеркивание
					и тире',
			),
			array('id, specialization_id, name, rewrite_name', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'lfMasterPrices' => array(self::HAS_MANY, 'LfMasterPrice', 'service_id'),
			'specialization' => array(self::BELONGS_TO, 'LfSpecialization', 'specialization_id'),
			'seoText'        => array(self::HAS_ONE, 'LfSeoText', 'service_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'                => 'ID',
			'specialization_id' => 'Специализация',
			'name'              => 'Название',
			'rewrite_name'      => 'Абривиатура URL',
			'weight'            => 'Порядок сортировки',
			'price_from'        => 'Разрешить цену от',
			'unit'              => 'Наименование единицы',
		);
	}

	public function getListItems($withEmpty = false)
	{
		$items = array();

		if ($withEmpty) {
			$items[''] = 'услуга не выбрана';
		}

		$specializations = $this->findAll();
		foreach ($specializations as $specialization) {
			$items[$specialization->id] = su::ucfirst($specialization->name);
		}

		return $items;
	}

	public function scopes()
	{
		return array(
			'ordered'  => array(
				'order' => 'weight, name ASC',
			),
			'filtered' => array(
				'order'     => 't.weight, t.name ASC',
				'with'      => 'specialization',
				'condition' => 'specialization.binded_service_id IS NULL OR NOT (specialization.binded_service_id = t.id)',
			),
		);
	}

	public function behaviors()
	{
		return array(
			'CArRewriteBehavior' => array(
				'class' => 'application.extensions.CArRewriteBehavior',
			),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('t.id', $this->id);
		$criteria->compare('t.specialization_id', $this->specialization_id);
		$criteria->compare('t.name', $this->name, true);
		$criteria->compare('t.rewrite_name', $this->rewrite_name, true);
		$criteria->compare('t.weight', $this->weight);

		return new CActiveDataProvider(
			$this, array(
				'criteria'   => $criteria,
				'pagination' => array(
					'pageSize' => 50,
				),
			)
		);
	}

	public function setMastersSearch()
	{
		$this->SearchEntity = 'masters';
		return $this;
	}

	public function setSalonsSearch()
	{
		$this->SearchEntity = 'salons';
		return $this;
	}

	/**
	 * @param CityModel $city
	 *
	 * @return string
	 */
	public function getSearchUrl($city = null)
	{
		$params = ['specialization' => $this->specialization->getRewriteName(), 'service' => $this->getRewriteName()];
		if($city != null) {
			$params['city'] = $city->rewrite_name;
		}
		return Yii::app()->createUrl(
			$this->SearchEntity . '/custom',
			$params
		);
	}

	/**
	 * @param LfSpecialization $specialization
	 * @param                  $rewriteName
	 *
	 * @return LfService
	 */
	public function findBySpecAndRewrite(LfSpecialization $specialization, $rewriteName)
	{
		return $this->with('specialization')->find(
			't.specialization_id = :spec AND (t.id = :rewrite OR t.rewrite_name = :rewrite)',
			array('spec' => $specialization->id, 'rewrite' => $rewriteName)
		);
	}

	public function asArray()
	{
		return array(
			'id'   => (int)$this->id,
			'name' => $this->name,
		);
	}

	/**
	 * Выполняется перед валидацией модели
	 *
	 * @return bool
	 */
	protected function beforeValidate()
	{
		$this->rewrite_name = strtolower(str_replace(" ", "-", trim($this->rewrite_name)));

		return parent::beforeValidate();
	}
}