<?php

namespace dfs\docdoc\models;

/**
 * This is the model class for table "sector".
 *
 * The followings are the available columns in table 'sector':
 *
 * @property integer       $id
 * @property string        $name
 * @property string        $name_genitive
 * @property string        $name_plural
 * @property string        $name_plural_genitive
 * @property integer       $doctor_count_all
 * @property integer       $doctor_count_active
 * @property string        $rewrite_name
 * @property string        $spec_name
 * @property string        $rewrite_spec_name
 * @property int           $hidden_in_menu
 * @property string        $clinic_seo_title
 * @property string        $sector_seo_title
 * @property int           $is_double
 *
 *
 * The followings are the available model relations:
 * @property DoctorModel[] $doctors
 * @property RelatedSpecialtyModel[] $relatedSpecialties
 * @property RelatedSpecialtyModel[] $complexSpecialties
 * @property SectorSeoTextModel[] $seoTexts
 *
 * @method SectorModel ordered
 * @method SectorModel findByPk
 * @method SectorModel find
 * @method SectorModel cache
 *
 * @method SectorModel[] findAll
 */
class SectorModel extends \CActiveRecord
{
	const ANY_SECTOR = 'по всем специальностям';

	public $relatedIds = array();

	/**
	 * Количество клиник по специальности
	 *
	 * @var string
	 */
	public $countClinics = null;

	/**
	 * Идентификаторы специальностей используемые только у взрослых
	 *
	 * @var int[]
	 */
	public static $adultIds = array(67, 70, 85, 99, 111, 114);

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return SectorModel the static model class
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
		return 'sector';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, name_genitive, name_plural, name_plural_genitive, rewrite_name', 'required'),
			array('name, rewrite_name, clinic_seo_title, sector_seo_title', 'length', 'max' => 512),
			array('name_genitive, name_plural, name_plural_genitive', 'length', 'max' => 64),
			array('spec_name, rewrite_spec_name, hidden_in_menu, clinic_seo_title, sector_seo_title, is_double', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name,rewrite_name', 'safe', 'on' => 'search'),
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
			'doctors'            => [self::MANY_MANY, DoctorModel::class, 'doctor_sector(sector_id, doctor_id)'],
			'relatedSpecialties' => [self::HAS_MANY, RelatedSpecialtyModel::class, 'specialty_id'],
			'complexSpecialties' => [self::HAS_MANY, RelatedSpecialtyModel::class, 'related_specialty_id'],
			'seoTexts'           => [self::MANY_MANY, SectorSeoTextModel::class, 'sector_seo_text_sector(sector_seo_text_id, sector_id)'],
		);
	}

	/**
	 * @return array
	 */
	public function scopes()
	{
		return array(
			'ordered'           => array(
				'order' => $this->getTableAlias() . '.name ASC',
			),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'                    => 'ID',
			'name'                  => 'Название направления',
			'name_genitive'         => 'Название в род. падеже',
			'name_plural'           => 'Название во множ. числе',
			'name_plural_genitive'  => 'Название в род. падеже во множ. числе',
			'rewrite_name'          => 'Алиас для ЧПУ',
			'is_double'             => 'Двойная специальность',
			'relatedSpecialtyIds'   => 'Связанные специальности'
		);
	}

	/**
	 * Действия после сохранения
	 */
	protected function afterSave()
	{
		RelatedSpecialtyModel::model()->deleteAll(
			[
				'condition' => 'specialty_id=:specialty_id',
				'params' => [':specialty_id' => $this->id]
			]
		);
		foreach ($this->relatedIds as $id) {
			$this->relatedSpecialties = new RelatedSpecialtyModel();
			$this->relatedSpecialties->specialty_id = $this->id;
			$this->relatedSpecialties->related_specialty_id = $id;
			$this->relatedSpecialties->save();
		}

		return parent::afterSave();
	}

	/**
	 * Получение массив идентификаторов связанных специальностей
	 *
	 * @return array
	 */
	public function getRelatedSpecialtyIds()
	{
		$data = [];
		foreach ($this->relatedSpecialties as $item) {
			$data[] = $item->related_specialty_id;
		}

		return $data;
	}

	/**
	 * Поиск по rewrite_name
	 *
	 * @param string $rewriteName
	 * @return $this
	 */
	public function byRewriteName($rewriteName)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => "rewrite_name = :sector_rewrite_name",
					'params' => [':sector_rewrite_name' => $rewriteName],
				]
			);

		return $this;
	}

	/**
	 * Поиск по rewrite_spec_name
	 *
	 * @param string $rewriteName
	 * @return $this
	 */
	public function byRewriteSpecName($rewriteName)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => "rewrite_spec_name = :rewrite_name",
					'params' => [':rewrite_name' => $rewriteName],
				]
			);

		return $this;
	}

	/**
	 * Поиск специальностей доктора
	 *
	 * @param $id
	 *
	 * @return $this
	 */
	public function byClinic($id)
	{
		$this->getDbCriteria()->mergeWith([
			'with' => [
				'doctors' => [
					'select' => false,
					'with' => [
						'doctorClinics' => [
							'select' => false,
							'condition' => 'doctorClinics.clinic_id = :clinic_id',
							'params' => [
								':clinic_id' => $id,
							],
						],
					],
					'condition' => 'doctors.status = :doctor_status',
					'params' => [
						':doctor_status' => DoctorModel::STATUS_ACTIVE,
					]
				],
			],
		]);

		return $this;
	}

	/**
	 * Поиск обычных специальностей
	 *
	 * @return $this
	 */
	public function simple()
	{
		$this->getDbCriteria()
			->mergeWith(
				['condition' => "is_double = 0"]
		);

		return $this;
	}

	/**
	 * Поиск составных специальностей
	 *
	 * @return $this
	 */
	public function complex()
	{
		$this->getDbCriteria()
			->mergeWith(
				['condition' => "is_double = 1"]
		);

		return $this;
	}

	/**
	 * Поиск составных специальностей
	 *
	 * @return $this
	 */
	public function visible()
	{
		$this->getDbCriteria()
			->mergeWith(
				['condition' => "hidden_in_menu = 0"]
			);

		return $this;
	}

	/**
	 * Текстовый список
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

	/**
	 * Получение массива специальностей по станции метро
	 *
	 * @param   int $station
	 *
	 * @return  array
	 */
	public static function getItemsByStation($station)
	{
		//кешируем запрос на 24 часа
		$command = \Yii::app()
			->db
			->cache(86400)
			->createCommand()
			->select("t1.id, t1.name, t1.rewrite_name AS alias")
			->from("sector t1")
			->join("doctor_sector t2", "t2.sector_id = t1.id")
			->join("doctor t3", "t3.id=t2.doctor_id")
			->join("doctor_4_clinic t4", "t4.doctor_id = t3.id and t4.type=" . DoctorClinicModel::TYPE_DOCTOR)
			->join("clinic t5", "t5.id = t4.clinic_id")
			->join("underground_station_4_clinic t6", "t6.clinic_id = t5.id")
			->where(
				"t6.undegraund_station_id = :station_id
					AND t3.status = :doctor_status
					AND t5.status = :clinic_status",
				array(
					":station_id"    => $station,
					":doctor_status" => DoctorModel::STATUS_ACTIVE,
					":clinic_status" => ClinicModel::STATUS_ACTIVE,
				)
			)
			->group("t1.id")
			->order("t1.name");

		return $command->queryAll();
	}

	/**
	 * Получение массива специальностей по городу
	 *
	 * @param   int $city
	 * @param   bool $simple
	 *
	 * @return  array
	 */
	public static function getItemsByCity($city, $simple = true)
	{
		$criteria = new \CDbCriteria();
		$criteria->select = 't.id, t.name, t.rewrite_name';
		$criteria->scopes = ['active', 'inCity' => $city];
		$criteria->order = 't.name';
		$criteria->group = 't.id';
		$dependency = new \CDbCacheDependency('SELECT COUNT(*) FROM sector');
		if ($simple) {
			$items = self::model()
				->cache(86400, $dependency)
				->simple()
				->visible()
				->findAll($criteria);
		} else {
			$items = self::model()
				->cache(86400, $dependency)
				->findAll($criteria);
		}

		$data = [];
		foreach ($items as $key => $item) {
			$data[$key]['Id']           = $item->id;
			$data[$key]['name']         = $item->name;
			$data[$key]['rewriteName']  = $item->rewrite_name;
			$data[$key]['alias']        = $item->rewrite_name;
		}

		return $data;
	}

	/**
	 * только сектора с активными врачами
	 *
	 * @return $this
	 */
	public function active() {
		$criteria = new \CDbCriteria();
		$criteria->with = array(
			'doctors'  => array(
				'select'   => [],
				'joinType' => 'INNER JOIN',
				'scopes'   => ['active'],
			)
		);
		$criteria->group = $this->getTableAlias() . ".id";

		$this
			->getDbCriteria()
			->mergeWith($criteria);

		return $this;
	}

	/**
	 * Сектора в городе
	 *
	 * @param integer $cityId
	 *
	 * @return $this
	 */
	public function inCity($cityId)
	{
		$criteria = new \CDbCriteria();
		$criteria->with = array(
			'doctors'  => array(
				'select'   => [],
				'joinType' => 'INNER JOIN',
				'scopes'   => ['inCity' => $cityId],
			)
		);
		$criteria->group = $this->getTableAlias() . ".id";

		$this
			->getDbCriteria()
			->mergeWith($criteria);

		return $this;
	}

	/**
	 * Получение массива с двойными специальностями
	 *
	 * @return array
	 */
	public function getRelatedSpecialities()
	{
		$data = [];

		foreach ($this->complexSpecialties as $item) {
			if ($item->sector->hidden_in_menu == 1) {
				$data[] = [
					'id'            => $item->sector->id,
					'rewrite_name'  => $item->sector->rewrite_name,
					'name'          => $item->sector->name,
				];
			}
		}

		return $data;
	}

	/**
	 * Получает список специальностей для клиники
	 *
	 * @param int $clinicId идентификатор клиники
	 *
	 * @return SectorModel[]
	 */
	public function findAllForClinic($clinicId)
	{
		return $this
			->cache(86400)
			->byClinic($clinicId)
			->findAll(['order' => 't.id']);
	}

	/*
	 * Добавить в выборку количество клиник по специальности
	 *
	 * @param bool $withClinics
	 *
	 * @return $this
	 */
	public function selectCountClinics($withClinics = true)
	{
		$join = $withClinics ? 'INNER JOIN' : 'LEFT JOIN';

		$this->getDbCriteria()->mergeWith([
			'select' => $this->getTableAlias() . '.*, COUNT(DISTINCT(c.id)) as countClinics',
			'join' =>
				$join . ' doctor_sector ds ON (ds.sector_id = t.id) ' .
				$join . ' doctor d ON (d.id = ds.doctor_id AND d.status = :doctorStatus) ' .
				$join . ' doctor_4_clinic dc ON (dc.doctor_id = d.id AND dc.type = :doctorType) ' .
				$join . ' clinic c ON (c.id = dc.clinic_id AND c.status = :clinicStatus AND c.city_id = :cityId AND c.isClinic = "yes") ',
			'group' => $this->getTableAlias() . '.id',
			'params' => [
				'doctorStatus' => DoctorModel::STATUS_ACTIVE,
				'doctorType' => DoctorClinicModel::TYPE_DOCTOR,
				'clinicStatus' => ClinicModel::STATUS_ACTIVE,
				'cityId' => \Yii::app()->city->getCityId(),
			],
		]);

		return $this;
	}
}
