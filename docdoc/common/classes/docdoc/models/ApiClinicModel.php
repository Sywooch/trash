<?php
namespace dfs\docdoc\models;

use CActiveDataProvider;
use CDbCriteria;
use dfs\docdoc\api\clinic\ClinicApiClient;
use dfs\docdoc\objects\Phone;

/**
 * модель для таблицы phone
 *
 * @property integer $id
 * @property string $name
 * @property string $phone
 * @property string $city
 * @property boolean $enabled
 * @property integer $is_merged
 *
 * @property ApiDoctorModel[] $api_doctors
 * @property ClinicModel $clinic
 *
 * @method ApiClinicModel findByPk
 * @method ApiClinicModel find
 * @method ApiClinicModel[] findAll
 *
 */
class ApiClinicModel extends \CActiveRecord
{
	/**
	 * Используется для поиска в бо
	 *
	 * @var bool
	 */
	public $is_merged = '';

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return ApiClinicModel the static model class
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
		return 'api_clinic';
	}

	/**
	 * Правила валидации
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			['name', 'required'],
			['enabled', 'safe'],
			['name', 'length', 'max' => 45],
			['phone', 'length', 'max' => 11],
			['city', 'length', 'max' => 20],
			[
				'name, phone, city',
				'filter',
				'filter' => 'strip_tags'
			],
			[
				'id, name, phone, city, is_merged',
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
			'api_doctors' => [
				self::HAS_MANY, ApiDoctorModel::class, 'api_clinic_id'
			],
			'clinic'      => [
				self::HAS_ONE, ClinicModel::class, 'external_id'
			]
		];

	}

	/**
	 * загрузка клиник из API
	 *
	 * @return string[]
	 */
	public function loadClinicsFromApi()
	{
		return ClinicApiClient::createClient()->getClinics([]);
	}

	/**
	 * Сохранение клиник, полученных из метода getClinics API
	 *
	 * @link https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=12156945
	 *
	 * $clinics = [
	 *        [ "clinicId" => "1", "name" => "Клиника №1", "phone" => "74951234567",  "city" => "Москва" ],
	 *        [ "clinicId" => "2", "name" => "Клиника №2", "phone" => "74951234567",  "city" => "Москва" ],
	 * ];
	 *
	 * @param \StdClass[] $clinics
	 */
	public function saveClinicsFromApi(array $clinics)
	{
		foreach ($clinics as $clinic) {
			//clinicId уникален в рамках всех клиник за счет префикса, например onclinic_1, medsi_5
			//ищем клинику по external_id во всех филиалах
			$model = ApiClinicModel::model()->findByPk($clinic->clinicId);

			//если уже есть такая клиника - ничего с ней не делаем
			if ($model === null) {
				$model = new ApiClinicModel();
				$model->id = $clinic->clinicId;
			}

			$model->phone = isset($clinic->phone) ? new Phone($clinic->phone) : null;
			$model->city = isset($clinic->city) ? new Phone($clinic->city) : null;
			$model->name = $clinic->name;
			$model->enabled = true;
			$model->save();
		}
	}


	/**
	 * загрузка всех докторов в данной клинике
	 *
	 * @return null|array
	 */
	public function loadResourcesFromApi()
	{
		return ClinicApiClient::createClient()->getResources([$this->id]);
	}

	/**
	 * Поиск
	 *
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare($this->getTableAlias() . '.id', $this->id, true);
		$criteria->compare($this->getTableAlias() . '.name', $this->name, true);
		$criteria->compare($this->getTableAlias() . '.phone', $this->phone, true);
		$criteria->compare($this->getTableAlias() . '.city', $this->city, true);
		$criteria->compare($this->getTableAlias() . '.enabled', $this->enabled);

		$criteria->with = [
			'clinic' => [
				'joinType' => $this->is_merged ? 'inner join' : 'left join',
			]
		];


		if($this->is_merged !== '' && !$this->is_merged){
			$criteria->with['clinic']['condition'] = 'clinic.id is null';
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
			'id'        => 'ID',
			'name'      => 'Название',
			'phone'     => 'Телефон',
			'city'      => 'Город',
			'is_merged' => 'Сопоставление',
			'enabled'   => 'Активность'
		];
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
	 * Выключить список
	 *
	 * @param int[] $idList
	 * @param bool $invert
	 * @return int
	 */
	public function disableByPk(array $idList, $invert = false)
	{
		$cr = new \CDbCriteria();

		if ($invert) {
			$cr->addNotInCondition('id', $idList);
		} else {
			$cr->addInCondition('id', $idList);
		}

		return self::model()->updateAll(['enabled' => false], $cr);
	}

	/**
	 * Поиск только смерженных
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
			['joinType' => 'left join', 'condition' => 'clinic.id is null'];

		$criteria->with = [
			'clinic' => $params,
		];

		$this->getDbCriteria()
			->mergeWith($criteria);

		return $this;
	}
}
