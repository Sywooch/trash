<?php

namespace dfs\docdoc\models;

use CActiveRecord;
use CActiveDataProvider;
use CDbCriteria;

/**
 * This is the model class for table "sector_seo_text".
 *
 * The followings are the available columns in table 'sector_seo_text':
 *
 * @property integer       $id
 * @property integer       $disabled
 * @property integer       $position
 * @property int           $page_type
 * @property string        $name
 * @property string        $text
 *
 * The followings are the available model relations:
 * @property SectorModel[] $sectors
 *
 * @method SectorSeoTextModel ordered
 * @method SectorSeoTextModel onlyUpper
 * @method SectorSeoTextModel onlyLower
 * @method int[] getRelationIds
 *
 * @method SectorSeoTextModel[] findAll
 */
class SectorSeoTextModel extends CActiveRecord
{
	const POSITION_UP = 1;
	const POSITION_DOWN = 2;

	/**
	 * @var string[]
	 */
	protected $positionNames = array(
		self::POSITION_UP   => 'вверху',
		self::POSITION_DOWN => 'внизу',
	);

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return SectorSeoTextModel the static model class
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
		return 'sector_seo_text';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'filter', 'filter' => 'strip_tags'),
			array('name, text, position', 'required'),
			array('disabled, position', 'numerical', 'integerOnly' => true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, text', 'safe', 'on' => 'search'),
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
			'sectors' => array(
				self::MANY_MANY,
				'\dfs\docdoc\models\SectorModel',
				'sector_seo_text_sector(sector_seo_text_id, sector_id)'
			),
		);
	}

	/**
	 * @return array
	 */
	public function scopes()
	{
		return array(
			'ordered'   => array(
				'order' => 'name ASC',
			),
			'onlyUpper' => array(
				'condition' => 'position = ' . self::POSITION_UP,
			),
			'onlyLower' => array(
				'condition' => 'position = ' . self::POSITION_DOWN,
			),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'       => 'ID',
			'disabled' => 'Отключить SEO-блок',
			'name'     => 'Название SEO-блока',
			'text'     => 'Текст SEO-блока',
			'sectors'  => 'Направления',
			'position' => 'Положение SEO-блока'
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

		$criteria->compare('id', $this->id);
		$criteria->compare('disabled', $this->disabled);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('text', $this->text, true);

		return new CActiveDataProvider(
			$this, array(
				'criteria'   => $criteria,
				'pagination' => array(
					'pageSize' => 20,
				),
			)
		);
	}

	/**
	 * @return array
	 */
	public function behaviors()
	{
		return array(
			'CAdvancedArBehavior' => array(
				'class' => 'CAdvancedArBehavior',
			),
			'CArIdsBehavior'      => array(
				'class' => 'CArIdsBehavior',
			),
		);
	}

	/**
	 * @return string[]
	 */
	public function getPositionNames()
	{
		return $this->positionNames;
	}

	/**
	 * @return string
	 */
	public function getPositionName()
	{
		return $this->positionNames[$this->position];
	}

	/**
	 * Список
	 *
	 * @param bool $withEmpty
	 *
	 * @return string[]
	 */
	public function getListItems($withEmpty = false)
	{
		$items = array();

		if ($withEmpty) {
			$items[''] = 'нет направления';
		}

		$sectors = $this
			->ordered()
			->findAll();
		foreach ($sectors as $sector) {
			$items[$sector->id] = $sector->name;
		}

		return $items;
	}

}