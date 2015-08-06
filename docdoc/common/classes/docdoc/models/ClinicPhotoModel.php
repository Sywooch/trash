<?php

namespace dfs\docdoc\models;


/**
 * Фотографии для клиник
 *
 * @property int    $img_id
 * @property int    $clinic_id
 * @property string $imgPath
 * @property string $description
 *
 * The followings are the available model relations:
 *
 * @property ClinicModel $clinic
 *
 *
 * @method ClinicPhotoModel[] findAll
 * @method ClinicPhotoModel find
 * @method ClinicPhotoModel findByPk
 */
class ClinicPhotoModel extends \CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return ClinicPhotoModel the static model class
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
		return 'img_clinic';
	}

	/**
	 * @return string имя первичного ключа
	 */
	public function primaryKey()
	{
		return 'img_id';
	}

	/**
	 * Зависимости
	 *
	 * @return array
	 */
	public function relations()
	{
		return [
			'clinic' => [self::BELONGS_TO, ClinicModel::class, 'clinic_id'],
		];
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return [
			['clinic_id, imgPath', 'required'],
			['img_id, clinic_id', 'numerical', 'integerOnly' => true],
			['imgPath', 'file', 'types' => 'jpg, jpeg, gif, png', 'allowEmpty' => true],
		];
	}


	/**
	 * Поиск по клинике
	 *
	 * @param $clinicId
	 *
	 * @return $this
	 */
	public function byClinic($clinicId)
	{
		$this->getDbCriteria()->mergeWith([
			'condition' => $this->getTableAlias() . '.clinic_id = :clinicId',
			'params'    => [':clinicId' => $clinicId],
		]);

		return $this;
	}


	/**
	 * Путь к файлу с фотографией
	 *
	 * @return string
	 */
	public function getFilePath()
	{
		return \Yii::app()->params['path']['upload'] . '/clinic/photos/' . $this->imgPath;
	}

	/**
	 * Ссылка на фотографию
	 *
	 * @return string
	 */
	public function getUrl()
	{
		return 'https://' . \Yii::app()->params['hosts']['static'] . '/img/clinic/photos/' . $this->imgPath;
	}
}
