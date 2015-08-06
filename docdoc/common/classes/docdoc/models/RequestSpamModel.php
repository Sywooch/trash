<?php

namespace dfs\docdoc\models;

/**
 * This is the model class for table "request_spam".
 *
 * The followings are the available columns in table 'request_spam':
 *
 * @property integer $req_id
 * @property string $id_city
 * @property string $client_name
 * @property string $client_phone
 * @property integer $req_created
 * @property integer $req_status
 * @property integer $req_departure
 * @property integer $req_sector_id
 * @property integer $diagnostics_id
 * @property integer $req_doctor_id
 * @property integer $req_type
 * @property integer $kind
 * @property integer $source_type
 * @property integer $clinic_id
 * @property integer $date_admission
 * @property integer $appointment_time
 * @property string $age_selector
 * @property string $client_comments
 * @property integer $partner_id
 * @property integer $is_hot
 * @property string $enter_point
 * @property string  $token
 *
 * The followings are the available model relations:
 *
 * @method RequestSpamModel find
 * @method RequestSpamModel findByPk
 * @method RequestSpamModel[] findAll
 * @method int count
 *
 */
class RequestSpamModel extends \CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return RequestSpamModel the static model class
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
		return 'request_spam';
	}

	/**
	 * @return string the associated primary key
	 */
	public function primaryKey()
	{
		return 'req_id';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			[
				'client_name, client_phone, req_sector_id, req_doctor_id, partner_id,
					clinic_id, req_departure, id_city, client_comments, age_selector,
					kind, req_type, enter_point, token, req_created, req_status, date_admission,
					appointment_time, source_type, diagnostics_id',
				'safe',
			],
		];
	}

	/**
	 * Выборка по токену
	 *
	 * @param $token
	 *
	 * @return $this
	 */
	public function byToken($token)
	{
		$this->getDbCriteria()->mergeWith(
			array(
				'condition' => "token = :token",
				'params' => [
					':token' => $token,
				],
			)
		);

		return $this;
	}

	/**
	 * Сохранение данных о заявке в спам
	 *
	 * @param RequestModel $request
	 *
	 * @return bool
	 */
	public function saveFromRequest(RequestModel $request)
	{
		$this->attributes = $request->attributes;
		return $this->save();
	}

	/**
	 * Действия после сохранения модели
	 */
	public function afterSave()
	{
		\Yii::app()->newRelic->customMetric('Custom/Request/Spam', 1);

		return parent::afterSave();
	}
}
