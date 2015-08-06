<?php

/**
 * This is the model class for table "diagnostic_center".
 *
 * The followings are the available columns in table 'diagnostic_center':
 * @property integer $id
 * @property integer $date_created
 * @property integer $date_blocked
 * @property string $name
 * @property string $rewrite_name
 * @property integer $view_count
 * @property integer $status
 * @property string $contact_name
 * @property string $contact_phone
 * @property string $center_phone
 * @property string $address
 * @property string $weekdays_open
 * @property string $weekend_open
 * @property string $url
 * @property string $short_description
 * @property string $full_description
 * @property string $map_latitude
 * @property string $map_longitude
 */
class DiagnosticCenter extends StatusEntity {

    protected $statusNames = array(
        self::STATUS_REGISTRATION => 'Регистрация',
        self::STATUS_NEW => 'Новый',
        self::STATUS_ACTIVE => 'Активный',
        self::STATUS_BLOCKED => 'Заблокирован',
        self::STATUS_ARCHIVE => 'Архив',
    );

    /**
     * Returns the static model of the specified AR class.
     * @return DiagnosticCenter the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'diagnostic_center';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
     //       array('date_created, name', 'required'),
            array('date_created, date_blocked, view_count, status', 'numerical', 'integerOnly' => true),
            array('name, rewrite_name, contact_name, address, weekdays_open, weekend_open, map_latitude, map_longitude', 'length', 'max' => 512),
            array('name, contact_phone, center_phone, url, short_description, full_description', 'safe'),
            array('logo', 'file', 'types'=>'jpg, gif, png', 'allowEmpty' => true),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, date_created, date_blocked, name, rewrite_name, view_count, status, contact_name, 
			contact_phone, center_phone, address, weekdays_open, weekend_open, short_description, 
			full_description, map_latitude, map_longitude, url', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'undergroundStations' => array(self::MANY_MANY, 'UndergroundStation', 'diagnostic_center_address(diagnostic_center_id,underground_station_id)'),
            'diagnostics' => array(self::MANY_MANY, 'Diagnostica', 'diagnostic_center_diagnostica(diagnostic_center_id,diagnostica_id)'),
        	'diagnosticCenterDiagnostics' => array(self::HAS_MANY, 'DiagnosticCenterDiagnostica', 'diagnostic_center_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'date_created' => 'Дата создания',
            'date_blocked' => 'Дата блокировки',
            'name' => 'Название центра',
            'rewrite_name' => 'Алиас для ЧПУ',
            'view_count' => 'Кол-во просмотров',
            'status' => 'Статус',
            'contact_name' => 'ФИО контактного лица',
            'contact_phone' => 'Телефон для связи',
            'center_phone' => 'Телефон центра',
            'address' => 'Адрес центра',
            'weekdays_open' => 'Часы работы в будние дни',
            'weekend_open' => 'Часы работы в выходные',
            'url' => 'Сайт центра',
            'full_description' => 'Полное описание',
            'short_description' => 'Краткое описание',
            'map_latitude' => 'Широта',
            'map_longitude' => 'Долгота',
            'undergroundStations' => 'Станции метро',
            'diagnostics' => 'Диагностики',
        	'logo' => 'Лого',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;
        $criteria->with = array(
            'undergroundStations',
        );
        $criteria->together = true;

        $criteria->compare('id', $this->id);
        $criteria->compare('t.name', $this->name);
        $criteria->compare('date_created', $this->date_created);
        $criteria->compare('date_blocked', $this->date_blocked);
        $criteria->compare('rewrite_name', $this->rewrite_name);
        
        $criteria->compare('view_count', $this->view_count);
//		$criteria->compare('status',$this->status);
        if ($this->undergroundStations)
            $criteria->addInCondition('undergroundStations.id', $this->undergroundStations);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
        			'pagination' => array('pageSize'=>99999),
                ));
    }
    
    public function afterSave() {
    	parent::afterSave();
    	$photos = CUploadedFile::getInstancesByName('photo');
    	$path = self::uploadPaths();
   // 	$image = DiagnosticCenterImage::model()->deleteAll("diagnostic_center_id=:cid",array(":cid"=>$this->id));
    	foreach($photos as $key => $photo){
		//	$filename = 'photo_'.$this->rewrite_name.'_'.$key.'.'.$photo->getExtensionName();
			$filename = $this->rewrite_name.'_'.$photo->getName();
			move_uploaded_file($_FILES['photo']['tmp_name'][$key],$path['photo'].'/'.$filename);
			$image = new DiagnosticCenterImage();
			$image->diagnostic_center_id = $this->id;
			$image->image = $filename;
			$image->save();
		}
    	
        if (isset($_POST['diagnostica'])) {
            foreach ($_POST['diagnostica'] as $id=>$price) {
                if (!empty($price)){
                    $model=DiagnosticCenterDiagnostica::model()->find("diagnostic_center_id=:cid AND diagnostica_id=:id",array(
                        ":cid"=>$_POST['DiagnosticCenter']['id'],
                        ":id"=>$id,
                        )
                    );
                    
                    $model->price = $price; 
                    $model->save();
                }
            }
        }
    	if(isset($_POST['DiagnosticCenter']['undergroundStations']))
    		self::saveManyStations($_POST['DiagnosticCenter']['undergroundStations']);
        
        return true;
    }
    
    public function saveManyStations($stations) {
    	$connection=Yii::app()->db; 
        $sql = "DELETE FROM diagnostic_center_address WHERE diagnostic_center_id=".$this->id;
        $command=$connection->createCommand($sql);
        $rowCount=$command->execute();
        foreach($stations as $station) {
            $sql = "INSERT INTO diagnostic_center_address VALUES (".$this->id.",".$station.")";
            $command=$connection->createCommand($sql);
            $rowCount=$command->execute();
        }
    }
    
	public function uploadPaths() {
		return array(
			'photo' => Yii::app()->basePath.'/../upload/kliniki/photo/',
			'logo' => Yii::app()->basePath.'/../upload/kliniki/logo/',
		);
	}

	public function uploadUrls() {
		return array(
			'photo' => Yii::app()->baseUrl.'/upload/kliniki/photo/',
			'logo' => Yii::app()->baseUrl.'/upload/kliniki/logo/',
		);
	}

	public function uploadFilename($attribute, CUploadedFile $file) {
		$filename = null;

		switch ($attribute) {
			case 'photo':
				$filename = 'photo_'.$this->rewrite_name.'.'.$file->getExtensionName();
				break;
				
			case 'logo':
				$filename = 'logo_'.$this->rewrite_name.'.'.$file->getExtensionName();
				break;
		}

		return $filename;
	}
    
    public function formatPhone($startPrefix = "+7") {
        $number = $this->center_phone;
		$newPhone = "";
 
        $element = preg_replace('/[\D]/', '', $number);

		if ( substr($element, 0,1) == '8' && strlen($element) > 8) {
			$element= substr($element, 1, strlen($element));
		} else if (strlen($element) == 7 ) {
			$element= '495'.$element;
		}

		if ( !empty($element) )  {
			$newPhone = $startPrefix." (".substr($element, 0,3).") ".substr($element, 3,3)."-".substr($element, 6,2)."-".substr($element, 8,2);
		}
		
		return $newPhone;	
	}

    public function getLogo($file = 'logo_default.gif') {
        $host = Yii::app()->params['hosts']['front'];
        $path = "//{$host}/upload/kliniki/logo/";

        if (!empty($this->logo)) {
            $file = $this->logo;
        }

        return $path . $file;
    }

    public function behaviors() {
        return array(
            'CAdvancedArBehavior' => array(
                'class' => 'application.extensions.CAdvancedArBehavior',
            ),
            'CArIdsBehavior' => array(
                'class' => 'application.extensions.CArIdsBehavior',
            ),
            'CArFileUploadBehavior' => array(
                'class' => 'application.extensions.CArFileUploadBehavior',
            )
        );
    }

}