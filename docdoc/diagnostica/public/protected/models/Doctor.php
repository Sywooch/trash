<?php

/**
 * This is the model class for table "doctor".
 *
 * The followings are the available columns in table 'doctor':
 * @property integer $id
 * @property integer $clinic_id
 * @property string $created
 * @property integer $departure
 * @property integer $rating
 * @property integer $status
 * @property integer $view_count
 * @property string $name
 * @property CUploadedFile $image
 * @property string $phone
 * @property string $phone_appointment
 * @property string $text
 * @property string $text_degree
 * @property string $text_education
 * @property string $text_association
 * @property string $text_course
 *
 * The followings are the available model relations:
 * @property Clinic $clinic
 * @property UndergroundStation[] $undergroundStations
 * @property DoctorAppointment[] $doctorAppointments
 * @property DoctorSector[] $doctorSectorExperiences
 * @property DoctorOpinion[] $doctorOpinions
 * @property DoctorRequest[] $doctorRequests
 * @property Sector[] $sectors
 */
class Doctor extends StatusEntity
{
	protected $statusNames = array(
		self::STATUS_REGISTRATION 	=> 'Регистрация',
		self::STATUS_NEW			=> 'Новый',
		self::STATUS_ACTIVE 		=> 'Активный',
		self::STATUS_BLOCKED 		=> 'Заблокирован',
		self::STATUS_ARCHIVE 		=> 'Архив',
	);

	/**
	 * Returns the static model of the specified AR class.
	 * @return Doctor the static model class
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
		return 'doctor';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			//array('phone', 'PhoneValidator'),
			array('status, name, phone, price, experience_year', 'required'),
			array('clinic_id, status, view_count, departure, price, experience_year', 'numerical', 'integerOnly'=>true),
			array('rating', 'numerical', 'min' => 0, 'max' => 5),
			array('price', 'numerical', 'min' => 0),
			array('experience_year', 'numerical', 'min' => 1961),
			array('name, text, text_education, text_association, text_course', 'filter', 'filter' => 'strip_tags'),
			array('name, text, text_education, text_association, text_course', 'filter', 'filter' => 'htmlspecialchars'),
			array('name, rewrite_name, phone, text_degree, text_spec', 'length', 'max'=>512),
			array('image', 'file', 'types'=>'jpg, gif, png', 'allowEmpty' => true),
			array('attach', 'file', 'types'=>'doc, docx, pdf, txt, rtf', 'allowEmpty' => true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, clinic, price, experience_year, undergroundStations, created, rating, status, view_count, name, departure', 'safe', 'on'=>'search'),
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
			'clinic' => array(self::BELONGS_TO, 'Clinic', 'clinic_id'),
			'undergroundStations' => array(self::MANY_MANY, 'UndergroundStation', 'doctor_address(doctor_id,underground_station_id)'),
			'doctorAppointments' => array(self::HAS_MANY, 'DoctorAppointment', 'doctor_id'),
			'doctorOpinions' => array(self::HAS_MANY, 'DoctorOpinion', 'doctor_id'),
			'doctorRequests' => array(self::HAS_MANY, 'DoctorRequest', 'doctor_id'),

			'sectors' => array(self::MANY_MANY, 'Sector', 'doctor_sector(doctor_id,sector_id)'),
		);
	}

	public function scopes() {
		return array_merge(
			parent::scopes(),
			array(
				'ordered' => array(
					'order' => 'name ASC',
				),
			)
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'text_education' => 'Образование',
			'text_association' => 'Ассоциации врачей',
			'created' => 'Дата регистрации',
			'departure' => 'Возможен выезд на дом',
			'rating' => 'Рейтинг',
			'status' => 'Статус',
			'view_count' => 'Количество просмотров',
			'name' => 'Имя',
			'phone' => 'Телефон врача',
			'phone_appointment' => 'Телефон для записи',
			'text' => 'О докторе',
			'text_degree' => 'Научная степень',
			'text_spec' => 'Специализация',
			'text_course' => 'Курсы повышения квалификации',
			'academicDegree' => 'Учёная степень',
			'priceRange' => 'Диапазон цен',
			'education' => 'Образование',
			'image' => 'Фотография',
			'sectors' => 'Направления',
			'price' => 'Стоимость первого приёма',
			'undergroundStations' => 'Станции метро',
			'clinic' => 'Клиника',
			'clinic_id' => 'Клиника',
			'attach' => 'Приложение',
			'experience_year' => 'Год начала практики',
			'rewrite_name' => 'Алиас для ЧПУ',
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
			'created',
			'name',
			'status',
			'clinic' => array(
				'asc' => 'clinic.name',
				'desc' => 'clinic.name DESC',
			),
		);

		$criteria=new CDbCriteria;
		$criteria->with = array(
			'clinic',
			'undergroundStations',
		);
		$criteria->together = true;

		$criteria->compare('price',$this->price);
		$criteria->compare('clinic.id',$this->clinic);
		$criteria->compare('t.created',$this->created,true);
		if ($this->rating) $criteria->compare('t.rating',$this->rating);
		$criteria->compare('t.status',$this->status);
		$criteria->compare('t.name',$this->name,true);
		if ($this->undergroundStations) $criteria->addInCondition('undergroundStations.id', $this->undergroundStations);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize' => 20,
			),
			'sort' => $sort,
		));
	}

	public function behaviors()
	{
		return array(
			'CAdvancedArBehavior' => array(
				'class' => 'application.extensions.CAdvancedArBehavior',
			),

			'CArIdsBehavior' => array(
				'class' => 'application.extensions.CArIdsBehavior',
			),

			'CarFileUploadBehavior' => array(
				'class' => 'application.extensions.CArFileUploadBehavior',
			)
		);
	}

	public function getExperiencesBySector() {
		$expBySector = array();
		foreach ($this->doctorSectorExperiences as $sectorExperience) {
			$expBySector[$sectorExperience->sector_id] = $sectorExperience;
		}

		return $expBySector;
	}

	protected function updateDoctorCountForSector(Sector $sector) {
		$criteriaCountAll = new CDbCriteria;
		$criteriaCountAll->addCondition('sectors.id = '.$sector->id);

		$criteriaCountActive = clone $criteriaCountAll;
		$criteriaCountActive->addCondition('status = '.Doctor::STATUS_ACTIVE);

		$sector->doctor_count_all =
			intval(self::model()->with('sectors')->count($criteriaCountAll));

		$sector->doctor_count_active =
			intval(self::model()->with('sectors')->count($criteriaCountActive));

		$sector->save();

		return $this;
	}

	protected function updateDoctorCount() {
		foreach (Sector::model()->findAll() as $sector) {
			$this->updateDoctorCountForSector($sector);
		}
		return $this;
	}
	
	protected function createImageNames() {
		$src = $this->getPath('image');
		$ext = pathinfo($src, PATHINFO_EXTENSION);
		
		$smallName = 'avatar_'.$this->id.'_small.'.$ext;
		$medName ='avatar_'.$this->id.'_med.'.$ext;
		
		return array($smallName, $medName);
	}
	
	public function resizeImage() {
		if (!$this->isUploaded('image')) return;
		
		list($smallName, $medName) = $this->createImageNames();
		$src = $this->getPath('image');
		$path = $this->getUploadPath('image');
		
		$smallPath = $path.$smallName;
		$medPath = $path.$medName;
		
		copy($src, $smallPath);
		copy($src, $medPath);
		
		ImageResizeUtils::create($smallPath)->fit(array('w' => 110, 'h' => 150));
		ImageResizeUtils::create($medPath)->fit(array('w' => 160, 'h' => 215));
	}
	
	public function getAvatarUrl($small = true) {
		if (!$this->isUploaded('image')) return null;
		
		list($smallName, $medName) = $this->createImageNames();
		$url = $this->getUploadUrl('image');
		$path = $this->getUploadPath('image');
		
		$name = $small ? $smallName : $medName;
		
		if (!file_exists($path.$name)) {
			$this->resizeImage();
		}
		
		return $url.$name;
	}

	public function afterSave() {
		parent::afterSave();
		
		$this->updateDoctorCount();
		
		if ($this->isUploaded('image') && $this->isFileTouched('image')) {
			$this->resizeImage();
		}
		
		return true;
	}

	public function afterDelete() {
		$this->updateDoctorCount();

		parent::afterDelete();
		return true;
	}

	public function uploadPaths() {
		return array(
			'image' => Yii::app()->basePath.'/../upload/doctors/',
			'attach' => Yii::app()->basePath.'/../upload/attaches/',
		);
	}

	public function uploadUrls() {
		return array(
			'image' => Yii::app()->baseUrl.'/upload/doctors/',
			'attach' => Yii::app()->baseUrl.'/upload/attaches/',
		);
	}

	public function uploadFilename($attribute, CUploadedFile $file) {
		$filename = null;

		switch ($attribute) {
			case 'image':
				$filename = 'avatar_'.$this->id.'.'.$file->getExtensionName();
				break;
				
			case 'attach':
				$filename = 'doctor_'.$this->id.'.'.$file->getExtensionName();
				break;
		}

		return $filename;
	}
	
	public function expInYears() {
		return (intval(date('Y')) - $this->experience_year);
	}

	public function expInRussian() {
		$years = $this->expInYears();
		return
			$years
				? $years.' '.RussianTextUtils::caseForNumber($years, array('год', 'года', 'лет'))
				: 'нет';
	}

	public function hasExp() {
		return intval($this->experience_year) > 0;
	}

	public function getPriceInRussian() {
		return
			($this->price ? number_format($this->price, 0, '.', ' ').' руб' : 'не указана');
	}

	public function getListItems() {
		$items = array();

		$doctors = $this->ordered()->findAll();
		foreach ($doctors as $doctor) {
			$items[$doctor->id] = $doctor->name;
		}

		return $items;
	}
	
	public function getListItemsForOpinion() {
		$items = array();

		$doctors = self::model()->ordered()->findAll();
		foreach ($doctors as $doctor) {
			$items[$doctor->id] = $doctor->name;
		}

		return $items;
	}
	
	public function getOpinions() {
		return
			DoctorOpinion::model()->
				onlyAllowed()->
				ordered()->
				findAll('t.doctor_id = :doctorId', array('doctorId' => $this->id));
	}

}