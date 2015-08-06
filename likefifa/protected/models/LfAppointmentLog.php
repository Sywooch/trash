<?php


namespace likefifa\models;

use CActiveRecord;
use CDbExpression;
use CHtml;
use LfAppointment;
use LfMaster;
use LfSalon;

/**
 * This is the model class for table "lf_appointment_log".
 *
 * The followings are the available columns in table 'lf_appointment_log':
 *
 * @property string        $id
 * @property integer       $appointment_id
 * @property string        $action
 * @property string        $data
 * @property string        $created
 * @property integer       $master_id
 * @property integer       $salon_id
 * @property integer       $admin_id
 *
 * The followings are the available model relations:
 * @property LfAppointment $appointment
 * @property LfMaster      $master
 * @property LfSalon       $salon
 * @property AdminModel    $admin
 */
class LfAppointmentLog extends CActiveRecord
{
	const ACTION_CREATE = 'create';
	const ACTION_UPDATE = 'update';
	const ACTION_REMOVE = 'remove';

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'lf_appointment_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return [
			['appointment_id, action, data', 'required'],
			['appointment_id, master_id, salon_id, admin_id', 'numerical', 'integerOnly' => true],
			['action', 'length', 'max' => 20],
			['created', 'safe'],
		];
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return [
			'master' => [self::BELONGS_TO, 'LfMaster', 'master_id'],
			'salon'  => [self::BELONGS_TO, 'LfSalon', 'salon_id'],
			'admin'  => [self::BELONGS_TO, 'likefifa\models\AdminModel', 'admin_id'],
		];
	}

	public function behaviors()
	{
		return [
			'CTimestampBehavior' => [
				'class'               => 'zii.behaviors.CTimestampBehavior',
				'createAttribute'     => 'created',
				'updateAttribute'     => null,
				'timestampExpression' => new CDbExpression('NOW()'),
			],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id'             => 'ID',
			'appointment_id' => 'Appointment',
			'action'         => 'Action',
			'data'           => 'Data',
			'created'        => 'Дата',
			'master_id'      => 'Master',
			'salon_id'       => 'Salon',
			'admin_id'       => 'Admin',
		];
	}

	/**
	 * Возвращает заявку.
	 * Сделано не через relation в связи с defaultScope в LfAppointment
	 *
	 * @return LfAppointment
	 */
	public function getAppointment()
	{
		return LfAppointment::model()->resetScope()->findByPk($this->appointment_id);
	}

	/**
	 * Возвращает пользователя, совершившего действие
	 *
	 * @return string
	 */
	public function getFormattedUser()
	{
		if ($this->admin_id) {
			return $this->admin->name;
		} elseif ($this->master_id) {
			return $this->master->getFullName();
		} elseif ($this->salon_id) {
			return $this->salon->getFullName();
		}
		return '--';
	}

	/**
	 * Возвращает описание действия
	 *
	 * @return string
	 */
	public function getFormattedText()
	{
		$model = new LfAppointment;
		if ($this->action == self::ACTION_CREATE) {
			$text = 'Создана новая заявка №' . $this->appointment_id;
			$text .=
				'<br/><strong>Источник:</strong> ' .
				LfAppointment::$sourcesList[$this->appointment->create_source];
			return $text;
		}
		if ($this->action == self::ACTION_REMOVE) {
			return 'Заявка №' . $this->appointment_id . ' удалена';
		}

		$text = '';
		$changed = [];

		foreach (unserialize($this->data) as $attr => $data) {
			if ($attr == 'is_viewed') {
				$text = 'Открытие заявки оператором';
				continue;
			}

			if ($attr == 'operator_comment') {
				$text = 'Новый комментарий: ' . CHtml::encode($data['value']);
				continue;
			}

			if ($attr == 'status') {
				$text = 'Новый статус: ' . $model->statusList[$data['value']];
				if ($this->master_id && $this->master_id == $this->appointment->master_id) {
					$text .= ' мастером';
				} elseif ($this->salon_id && $this->salon_id == $this->appointment->salon_id) {
					$text .= ' салоном';
				} elseif ($this->admin_id) {
					$text .= ' оператором';
				}

				continue;
			}

			if (($attr == 'date' || $attr == 'control') && !empty($data['value'])) {
				$data['value'] = date('d.m.Y H:i', $data['value']);
			}

			$changed[] = $model->getAttributeLabel($attr) . ': ' . $data['value'];
		}

		if (count($changed) > 0) {
			$text .= '<br/> <strong>Изменились:</strong> ' . implode('. ', $changed);
		}

		return $text;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 *
	 * @param string $className active record class name.
	 *
	 * @return LfAppointmentLog the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
}