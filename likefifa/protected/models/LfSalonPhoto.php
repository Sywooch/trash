<?php
use likefifa\extensions\image\Image;

/**
 * This is the model class for table "lf_salon_photo".
 *
 * The followings are the available columns in table 'lf_salon_photo':
 *
 * @property integer $id
 * @property integer $salon_id
 * @property string  $image
 *
 * The followings are the available model relations:
 * @property LfSalon $salon
 *
 * @method LfSalonPhoto findByPk
 */
class LfSalonPhoto extends CActiveRecord
{
	/**
	 * Размеры превьюшек
	 *
	 * @var array
	 */
	public $previewSizes = [
		'full'  => [800, 800],
		'small' => [116, 76],
		'big'   => [309, 200],
	];

	/**
	 * @param string $className active record class name.
	 *
	 * @return LfSalonPhoto the static model class
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
		return 'lf_salon_photo';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('salon_id', 'required'),
			array('salon_id', 'numerical', 'integerOnly' => true),
			array('image', 'length', 'max' => 256),
			array('image', 'file', 'types' => 'jpg, gif, png', 'on' => 'create'),
			array('image', 'file', 'types' => 'jpg, gif, png', 'allowEmpty' => true, 'on' => 'update'),
			array('id, salon_id, image', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'salon' => array(self::BELONGS_TO, 'LfSalon', 'salon_id'),
		);
	}

	public function behaviors()
	{
		return [
			[
				'class' => 'likefifa\extensions\image\ImageBehavior',
			],
			'CArModTimeBehavior' => [
				'class' => 'application.extensions.CArModTimeBehavior',
			],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'       => 'ID',
			'salon_id' => 'Salon',
			'image'    => 'Image',
		);
	}

	/**
	 * Возвращает ссылку на изображение
	 *
	 * @param string  $size
	 * @param bool    $crop
	 * @param int     $master
	 * @param boolean $force
	 * @param string  $watermark
	 *
	 * @return string
	 */
	public function preview($size, $crop = null, $master = Image::WIDTH, $force = false, $watermark = null)
	{
		if ($size == 'full') {
			$watermark = Yii::app()->basePath . '/../i/watermark-big.png';
		}

		return parent::preview(
			$this->previewSizes[$size][0],
			$this->previewSizes[$size][1],
			$crop,
			$master,
			$force,
			$watermark
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('salon_id', $this->salon_id);
		$criteria->compare('image', $this->image, true);

		return new CActiveDataProvider(
			$this, array(
				'criteria'   => $criteria,
				'pagination' => array(
					'pageSize' => 50,
				),
			)
		);
	}
}