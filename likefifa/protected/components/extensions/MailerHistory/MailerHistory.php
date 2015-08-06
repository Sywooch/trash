<?php


namespace likefifa\components\extensions\MailerHistory;

use LfMaster;
use LfSalon;
use likefifa\components\system\ActiveRecord;

/**
 * This is the model class for table "mailer_history".
 *
 * The followings are the available columns in table 'mailer_history':
 *
 * @property integer  $id
 * @property integer  $master_id
 * @property integer  $salon_id
 * @property integer  $type
 * @property string   $created
 *
 * The followings are the available model relations:
 * @property LfSalon  $salon
 * @property LfMaster $master
 *
 * @method MailerHistory[]  findAll
 */
class MailerHistory extends ActiveRecord
{
	const TYPE_REGISTER = 0;
	const TYPE_PUBLISH = 1;
	const TYPE_EMPTY_FIELDS = 2;
	const TYPE_NEGATIVE_BALANCE = 3;
	const TYPE_EMPTY_PROFILE = 4;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'mailer_history';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{

		return [
			['type', 'required'],
			['master_id, salon_id, type', 'numerical', 'integerOnly' => true],
			['created', 'safe']
		];
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return [
			'salon'  => [self::BELONGS_TO, 'LfSalons', 'salon_id'],
			'master' => [self::BELONGS_TO, 'LfMaster', 'master_id'],
		];
	}

	/**
	 * Возвращает сущность
	 *
	 * @return LfMaster|LfSalon
	 */
	public function getEntity()
	{
		if ($this->master_id) {
			return $this->master;
		}

		return $this->salon;
	}

	/**
	 * @param string $className active record class name.
	 *
	 * @return MailerHistory the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}