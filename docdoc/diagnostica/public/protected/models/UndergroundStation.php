<?php

/**
 * This is the model class for table "underground_station".
 *
 * The followings are the available columns in table 'underground_station':
 * @property integer $id
 * @property string $name
 * @property integer $underground_line_id
 * @property integer $index
 *
 * The followings are the available model relations:
 * @property DoctorAddress[] $doctorAddresses
 * @property UndergroundLine $undergroundLine
 */
class UndergroundStation extends CActiveRecord
{
	const ANY_STATION = 'любом районе Москвы';
	const MOSCOW_INDEX = 11;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return UndergroundStation the static model class
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
		return 'underground_station';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, underground_line_id', 'required'),
			array('underground_line_id, index', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>512),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('name, undergroundLine', 'safe', 'on'=>'search'),
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
			'doctorAddresses' => array(self::HAS_MANY, 'DoctorAddress', 'underground_station_id'),
			//'doctors' => array(self::HAS_MANY, 'Doctor', 'doctor_id', 'through' => 'doctorAddresses'),
			'undergroundLine' => array(self::BELONGS_TO, 'UndergroundLine', 'underground_line_id'),
		
			'doctors' => array(self::MANY_MANY, 'Doctor', 'doctor_address(underground_station_id,doctor_id)'),
		
			'diagnosticCentres' => array(self::MANY_MANY, 'DiagnosticCenter', 'diagnostic_center_address(underground_station_id,diagnostic_center_id)'),
		);
	}
	
	public function scopes()
	{
		return array(
			'orderedByLine' => array(
				'order' => 't.underground_line_id ASC',
			),
			'ordered' => array(
				'order' => 't.name ASC',
			),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Название станции',
			'underground_line_id' => 'Ветка метро',
			'undergroundLine' => 'Ветка метро',
			'index' => 'Index',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		
		$sort = new CSort;
		$sort->attributes = array(
			'id',
			'name',
			'undergroundLine' => array(
				'asc' => 'undergroundLine.name',
				'desc' => 'undergroundLine.name DESC',
			),
		);

		$criteria = new CDbCriteria;
		$criteria->with = 'undergroundLine';
		
		$criteria->compare('t.name',$this->name,true);
		$criteria->compare('undergroundLine.id',$this->undergroundLine);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize' => 20,
			),
			'sort' => $sort,
		));
	}
	
	public function getListItems() {
		$items = array();
		
		$stations = $this->ordered()->findAll();
		foreach ($stations as $station) {
			$items[$station->id] = $station->name;
		}

		return $items;
	}

	public static function getListMoscowItems()
	{
		$items = array();
		$lines = UndergroundLine::model()->moscow()->findAll();
		foreach ($lines as $line) {
			foreach ($line->undergroundStations as $station) {
				$items[$station->id] = $station->name;
			}
		}
		
		return $items;
	}

    
    static function getIcon($lineId) {
        switch ($lineId) {
            case 1:
                $line = '<i style="background-position: 0 -81px"></i>';
                break;
            case 2:
                $line = '<i style="background-position: 0 -90px"></i>';
                break;
            case 3:
                $line = '<i style="background-position: 0 0px"></i>';
                break;
            case 4:
                $line = '<i style="background-position: 0 -36px"></i>';
                break;
            case 5:
                $line = '<i style="background-position: 0 -54px"></i>';
                break;
            case 6:
                $line = '<i style="background-position: 0 -27px"></i>';
                break;
            case 7:
                $line = '<i style="background-position: 0 -18px"></i>';
                break;
            case 8:
                $line = '<i style="background-position: 0 -72px"></i>';
                break;
            case 9:
                $line = '<i style="background-position: 0 -45px"></i>';
                break;
            case 10:
                $line = '<i style="background-position: 0 -9px"></i>';
                break;
            case 11:
                $line = '<i style="background-position: 0 -63px"></i>';
                break;
            case 12:
                $line = '<i style="background-position: 0 -99px"></i>';
                break;
            default:
                $line = '';
                break;
        }
        
        return $line;
    }
}