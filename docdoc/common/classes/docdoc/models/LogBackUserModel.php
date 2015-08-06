<?php
namespace dfs\docdoc\models;

/**
 * This is the model class for table "log_back_user".
 *
 * @property integer $log_id
 * @property string $crDate
 * @property string $message
 * @property string $log_code_id
 * @property integer $user_id
 *
 * @method LogBackUserModel findByPk
 * @method LogBackUserModel find
 * @method LogBackUserModel[] findAll
 *
 */
class LogBackUserModel extends \CActiveRecord
{

	const CODE_DELETE_DOCTOR = 'D_DOC';

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return LogBackUserModel the static model class
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
		return 'log_back_user';
	}

	/**
	 * Первичный ключ
	 * @return string
	 */
	public function primaryKey()
	{
		return 'log_id';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('log_id, user_id',
				'numerical',
				'integerOnly' => true,
			),
		);
	}

	/**
	 * Отношения
	 *
	 * @return array
	 */
	public function relations()
	{
		return array(
		);
	}

	/**
	 * Действия перед сохраенением
	 *
	 * @return bool
	 */
	public function beforeSave()
	{
		parent::beforeSave();

		if (php_sapi_name() != 'cli' && \Yii::app()->session['user']) {
			$this->user_id = \Yii::app()->session['user']->idUser;
		} else {
			$this->user_id = 0;
		}


		return true;
	}

	/**
	 * Сохранение лога удаления врача
	 *
	 * @param DoctorModel $doctor
	 *
	 * @return bool
	 */
	public function deleteDoctorLog(DoctorModel $doctor)
	{
		$this->message = "Удаление врача id = {$doctor->id}";
		$this->log_code_id = self::CODE_DELETE_DOCTOR;

		return $this->save();
	}

}
