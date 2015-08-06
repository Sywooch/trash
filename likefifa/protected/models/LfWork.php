<?php

use likefifa\extensions\image\Image;

/**
 * This is the model class for table "lf_work".
 *
 * The followings are the available columns in table 'lf_work':
 *
 * @property integer          $id
 * @property integer          $service_id
 * @property integer          $master_id
 * @property string           $image
 * @property integer          $likes
 * @property integer          $specialization_id
 * @property string           $alt
 * @property integer          $sort
 * @property string           $created
 * @property integer          $click_count
 * @property integer          $is_main
 * @property integer          $index_position
 * @property string           $crop_coordinates
 *
 * The followings are the available model relations:
 * @property LfMaster         $master
 * @property LfService        $service
 * @property LfSpecialization $specialization
 * @property LfPrice          $price
 *
 * @method LfWork[] findAll
 * @method LfWork   findByPk
 * @method string   getPath
 * @method LfWork   best
 * @method LfWork   main
 * @method LfWork   rand
 * @method LfWork   index
 * @method LfWork   mainOrder
 */
class LfWork extends CActiveRecord
{
	/**
	 * Размеры превьюшек
	 *
	 * @var array
	 */
	public $previewSizes = [
		'full'      => [800, 800],
		'small'     => [183, 118],
		'big'       => [309, 200],
		'thumbFull' => [230, 230],
	];

	/**
	 * Цифра в шапке сайта.
	 * Исходное число
	 *
	 * @var int
	 */
	const START_COUNT = 7854;

	/**
	 * Прибавление в день
	 * Для шапки
	 *
	 * @var int
	 */
	const PER_DAY = 3;

	/**
	 * Максимальное количество работ на главной
	 *
	 * @var int
	 */
	const MAIN_LIMIT = 9;

	/**
	 * Флаги вывода на главной
	 *
	 * @var string[]
	 */
	public static $notMainFlags = array(
		"0" => "вывод разрешен",
		"1" => "вывод запрещен",
	);

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return LfWork the static model class
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
		return 'lf_work';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return [
			['specialization_id, service_id, master_id', 'required'],
			[
				'specialization_id, service_id, master_id, likes, sort, click_count, is_main, index_position',
				'numerical',
				'integerOnly' => true
			],
			['index_position', 'default', 'value' => null],
			['image, alt', 'length', 'max' => 256],
			['image', 'file', 'types' => 'jpg, jpeg, gif, png', 'on' => 'create'],
			['image', 'file', 'types' => 'jpg, jpeg, gif, png', 'allowEmpty' => true, 'on' => 'update'],
			['created, crop_coordinates', 'safe'],
		];
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'specialization' => array(self::BELONGS_TO, 'LfSpecialization', 'specialization_id', 'together' => true),
			'service'        => array(self::BELONGS_TO, 'LfService', 'service_id', 'together' => true),
			'master'         => array(self::BELONGS_TO, 'LfMaster', 'master_id'),
			'price'          => array(
				self::BELONGS_TO,
				'LfPrice',
				array('master_id' => 'master_id', 'service_id' => 'service_id')
			),
		);
	}

	/**
	 * Возвращает декларацию о названных областей
	 *
	 * @return string[]
	 */
	public function scopes()
	{
		$alias = $this->getTableAlias();
		return [
			'index'     => [
				'with'      => ['price', 'master'],
				'condition' =>
					"price.price > 0 AND " .
					(Yii::app()->activeRegion->isMoscow() ? "{$alias}.index_position < 10"
						: "{$alias}.index_position >= 10"),
				'order'     => "{$alias}.index_position ASC",
				'limit'     => self::MAIN_LIMIT,
			],
			'rand'      => array(
				'order' => 'RAND()',
				'limit' => '10',
			),
			'main'      => [
				'condition' => $alias . '.is_main = 1',
			],
			'mainOrder' => [
				'order' => $alias . '.is_main DESC',
			],
		];
	}

	public function behaviors()
	{
		return [
			'CArModTimeBehavior' => [
				'class' => 'application.extensions.CArModTimeBehavior',
			],
			'CTimestampBehavior' => [
				'class'               => 'zii.behaviors.CTimestampBehavior',
				'createAttribute'     => 'created',
				'updateAttribute'     => null,
				'timestampExpression' => new CDbExpression('NOW()'),
			],
			[
				'class'         => 'likefifa\extensions\image\ImageBehavior',
				'cropAttribute' => 'crop_coordinates',
				'entity'        => 'LfWork',
			],

		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'             => 'ID',
			'service_id'     => 'Услуга',
			'master_id'      => 'Мастер',
			'image'          => 'Фото',
			'likes'          => 'Лайки',
			'alt'            => 'Описание',
			'sort'           => 'Сортировка',
			'click_count'    => 'Клики',
			'is_main'        => 'ТОП 10',
			'index_position' => 'Позиция на главной странице'
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
	 * @return $string
	 */
	public function preview($size, $crop = null, $master = Image::WIDTH, $force = false, $watermark = null)
	{
		if ($crop == null) {
			$crop = $size == 'thumbFull' ? false : true;
		}
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

	public function generatePreview()
	{
		foreach ($this->previewSizes as $key => $sizes) {
			$this->preview($key, null, Image::WIDTH, true);
		}
	}

	public function toArray()
	{
		return array(
			'id'             => (int)$this->id,
			'smallThumb'     => $this->preview('small'),
			'bigThumb'       => $this->preview('big'),
			'full'           => $this->preview('full'),
			'likes'          => (int)$this->likes,
			'specialization' => (int)$this->specialization_id ?: null,
			'service'        => (int)$this->service_id ?: null,
		);
	}

	/**
	 * Вызывается после сохранения модели
	 *
	 * @return void
	 */
	protected function afterSave()
	{
		if ($this->master) {
			$this->master->updateRating();
		}

		parent::afterSave();
	}

	/**
	 * Вызывается после удаления модели
	 *
	 * @return void
	 */
	protected function afterDelete()
	{
		if ($this->master) {
			$this->master->updateRating();
		}

		parent::afterDelete();
	}

	/**
	 * Переворачивает изображение и получает ссылку на него
	 *
	 * @param string $direction направление вращения
	 * @param string $temp      имя временной фото
	 *
	 * @return string|boolean
	 */
	public function rotateImage($direction, $temp = null)
	{
		$this->crop_coordinates = null;
		$this->rotate($direction, $temp);
		$this->generatePreview();

		if ($temp == null) {
			return $this->preview('small');
		} else {
			return $this->getTempUrl() . '/' . $temp;
		}
	}

	/**
	 * Возвращает количество работ, добавленных в топ10
	 *
	 * @param $master_id
	 *
	 * @return integer
	 */
	public static function getTop10Count($master_id)
	{
		$criteria = new CDbCriteria();
		$criteria->scopes = ['main'];
		$criteria->compare('t.master_id', $master_id);

		return (int)self::model()->count($criteria);
	}
}