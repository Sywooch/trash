<?php
namespace dfs\docdoc\models;

use CActiveDataProvider;
use CDbCriteria;
use dfs\docdoc\api\clinic\ClinicApiClient;

/**
 * модель для таблицы phone
 *
 * @property integer $id
 * @property string $name
 * @property string $api_clinic_id
 * @property string $api_doctor_id
 * @property string $api_resource_type
 * @property boolean $enabled
 *
 * @property ApiClinicModel $api_clinic модель клиники
 * @property DoctorClinicModel $doctorClinic докто в клинике
 * @property DoctorModel $doctor
 *
 * @method ApiDoctorModel findByPk
 * @method ApiDoctorModel find
 * @method ApiDoctorModel[] findAll
 * @method int count
 * @method ApiDoctorModel with
 */
class ApiDoctorModel extends \CActiveRecord
{
	/**
	 * Типы которые могут прийти из апи
	 */
	const TYPE_DOCTOR = 'doctor';
	const TYPE_CABINET = 'diagnostic';

	/**
	 * Используется для поиска в бо
	 *
	 * @var string
	 */
	public $is_merged = '';

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return ApiDoctorModel the static model class
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
		return 'api_doctor';
	}

	/**
	 * Правила валидации
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			['api_doctor_id, name, api_clinic_id', 'required'],
			['enabled', 'safe'],
			['name', 'length', 'max' => 255],
			['api_doctor_id, api_clinic_id', 'length', 'max' => 50],
			[
				'api_doctor_id, api_clinic_id, name',
				'filter',
				'filter' => 'strip_tags'
			],
			[
				'id, api_doctor_id, api_clinic_id, name, is_merged',
				'safe',
				'on' => 'search'
			],
		];
	}

	/**
	 * Отношения
	 *
	 * @return array
	 */
	public function relations()
	{
		return [
			'api_clinic'   => [
				self::BELONGS_TO, ApiClinicModel::class, 'api_clinic_id'
			],
			'doctorClinic' => [
				self::HAS_ONE, DoctorClinicModel::class, 'doc_external_id'
			],
			'doctor'       => [
				self::HAS_ONE, DoctorModel::class, ['doctor_id' => 'id'], 'through' => 'doctorClinic'
			],
			'clinic'       => [
				self::HAS_ONE, ClinicModel::class, ['clinic_id' => 'id'], 'through' => 'doctorClinic'
			],
		];
	}

	/**
	 * сохранение докторов в клинике, полученных из метода getDoctors API
	 *
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=12156945
	 *
	 * $doctors = [
	 *        [ "doctorId" => "abc87", "clinicId" => "1", "name" => "Иванов Иван Иванович" ],
	 *        [ "doctorId" => "2", "clinicId" => "2", "name" => "Петров Иван Иванович" ],
	 * ];
	 *
	 * @param \StdClass[] $doctors
	 *
	 * @return array
	 */
	public function saveResourcesFromApi(array $doctors)
	{
		foreach ($doctors as $doc) {
			$model = ApiDoctorModel::model()->findByAttributes([
					'api_clinic_id' => $doc->clinicId,
					'api_doctor_id' => $doc->resourceId,
					'api_resource_type' => $doc->resourceType
				]
			);

			if (is_null($model)) {
				$model = new ApiDoctorModel();
				$model->api_clinic_id = $doc->clinicId;
				$model->api_doctor_id = $doc->resourceId;
				$model->api_resource_type = $doc->resourceType;
			}

			$model->name = $doc->name;
			$model->enabled = true;
			$model->save();

			//пытаемся автоматически смерджить с нащими докторами
			if (!$model->doctorClinic && $model->api_resource_type === ApiDoctorModel::TYPE_DOCTOR) {
				DoctorClinicModel::model()->mergeWithApiDoctor($model);
			}
		}
	}

	/**
	 * Выключить список
	 *
	 * @param int $clinicId
	 * @param string $type
	 * @param int[] $idList
	 * @param bool $invert
	 * @return int
	 */
	public function disableByClinicAndDoctors($clinicId, $type, array $idList, $invert = false)
	{
		$cr = new \CDbCriteria();
		if ($invert) {
			$cr->addNotInCondition('api_doctor_id', $idList);
		} else {
			$cr->addInCondition('api_doctor_id', $idList);
		}

		$cr->addCondition('api_clinic_id = :clinicId and api_resource_type = :type');
		$cr->params['clinicId'] = $clinicId;
		$cr->params['type'] = $type;

		return self::model()->updateAll(['enabled' => false], $cr);
	}

	/**
	 * Возвращает клинику по api_clinic_id
	 *
	 * @return ClinicModel|null
	 */
	public function getClinic()
	{
		return ClinicModel::model()->byExternalId($this->api_clinic_id)->find();

	}

	/**
	 * Поиск
	 *
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->with = array("doctor");

		$criteria->compare($this->getTableAlias() . '.id', $this->id);
		$criteria->compare($this->getTableAlias() . '.name', $this->name, true);
		$criteria->compare('doctor.name', $this->api_doctor_id, true);
		$criteria->compare($this->getTableAlias() . '.api_clinic_id', $this->api_clinic_id);
		$criteria->compare($this->getTableAlias() . '.enabled', $this->enabled);

		$criteria->with = [
			'doctorClinic' => [
				'joinType' => $this->is_merged ? 'inner join' : 'left join',
				'with'     => [
					'doctor' => [
						'select'   => 'doctor.id, doctor.name',
						'joinType' => 'left join',
					],
					'clinic' => [
						'select'   => 'clinic.id, clinic.name',
						'joinType' => 'left join',
					]
				]
			],
			'api_clinic'   => [
				'joinType' => 'inner join',
			]
		];

		if($this->is_merged !== '' && !$this->is_merged){
			$criteria->with['doctorClinic']['condition'] = 'doctorClinic.id is null';
		}

		return new CActiveDataProvider(
			$this, [
				'criteria'   => $criteria,
				'pagination' => [
					'pageSize' => 50,
				]
			]
		);
	}

	/**
	 * Названия меток
	 *
	 * @return array
	 */
	public function attributeLabels()
	{
		return [
			'id'            => 'ID',
			'name'          => 'Доктор API',
			'is_merged'     => 'Сопоставление',
			'api_clinic_id' => 'Клиника API',
			'api_doctor_id' => 'Доктор',
			'clinic'        => 'Клиника',
			'enabled'       => 'Активность'
		];
	}

	/**
	 * Возвращает название клиники
	 *
	 * @return string
	 */
	public function getApiClinicName()
	{
		$model = $this->api_clinic;

		if ($model) {
			return $model->name;
		}

		return null;
	}

	/**
	 * Только активные
	 *
	 * @return $this
	 */
	public function enabled()
	{
		$this->getDbCriteria()
			->mergeWith(
				['condition' => 'enabled = 1']
			);

		return $this;
	}

	/**
	 * Выключить всех врачей по клиникам
	 *
	 * @param array|string $clinicIdList
	 * @param bool $invert
	 */
	public function disableByClinic($clinicIdList, $invert = false)
	{
		$clinicIdList = (array)$clinicIdList;

		$cr = new CDbCriteria();

		if ($invert) {
			$cr->addNotInCondition('api_clinic_id', $clinicIdList);
		} else {
			$cr->addInCondition('api_clinic_id', $clinicIdList);
		}

		self::model()->updateAll(['enabled' => false], $cr);
	}

	/**
	 * Поиск по клинике
	 *
	 * @param string $api_clinic_id
	 * @return $this
	 */
	public function byClinic($api_clinic_id)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'api_clinic_id = :api_clinic_id',
					'params'    => [':api_clinic_id' => $api_clinic_id]
				]
			);

		return $this;
	}

	/**
	 * Загрузка расписания
	 *
	 * @param null $from
	 * @param null $to
	 *
	 * @return \StdClass[]
	 */
	public function loadSLots($from = null, $to = null)
	{
		$ids = [$this->api_clinic_id . "#" . $this->api_doctor_id];
		$attributes = null;

		if ($from !== null && $to !== null) {
			$attributes = ["from" => $from, 'to' => $to];
		}

		$api = ClinicApiClient::createClient();

		if ($attributes === null) {
			$slots = $api->getSlots($ids);
		} else {
			$slots = $api->getSlots($ids, $attributes);
		}

		return $slots;
	}

	/**
	 * Поиск смерженных
	 *
	 * @param bool $isMerged
	 *
	 * @return $this
	 */
	public function merged($isMerged = true)
	{
		$criteria = new CDbCriteria();

		$params = $isMerged ?
			['joinType' => 'inner join'] :
			['joinType' => 'left join', 'condition' => 'doctorClinic.id is null'];

		$criteria->with = [
			'doctorClinic' => $params,
		];

		$this->getDbCriteria()
			->mergeWith($criteria);

		return $this;
	}
}
