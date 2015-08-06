<?php

namespace dfs\docdoc\models;


/**
 * This is the model class for table "sip_channels".
 *
 * The followings are the available columns in table 'sip_channels':
 *
 * @property int     $sip
 * @property string  $channel
 * @property string  $ts_update
 * @property int     $request_id
 * @property int     $active
 *
 * @property RequestModel    $request
 * @property QueueModel      $queue
 *
 * @method SipChannelModel[] findAll
 * @method SipChannelModel find
 */
class SipChannelModel extends \CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return SipChannelModel
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
		return 'sip_channels';
	}

	/**
	 * @return string имя первичного ключа
	 */
	public function primaryKey()
	{
		return 'sip';
	}

	/**
	 * relations
	 *
	 * @return array
	 */
	public function relations()
	{
		return [
			'request' => [self::BELONGS_TO, RequestModel::class, 'request_id'],
			'queue'   => [self::HAS_ONE, QueueModel::class, 'SIP'],
		];
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return [
			[ 'sip, request_id', 'numerical', 'integerOnly' => true ],
		];
	}

	/**
	 * Поиск по подключённому пользователю
	 *
	 * @param int $userId
	 *
	 * @return $this
	 */
	public function byQueueUser($userId)
	{
		$criteria = new \CDbCriteria();

		$criteria->with = [ 'queue' => [ 'joinType' => 'INNER JOIN' ] ];
		$criteria->condition = 'queue.user_id = :user_id';
		$criteria->params = [ 'user_id' => $userId ];
		$criteria->together = true;

		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Делаем редирект на заявку, если оператор принял входящий звонок по ней
	 *
	 * @param int $userId
	 * @param array | null $params
	 *
	 * @return int | null
	 */
	static public function checkActiveRequest($userId, $params = null)
	{
		$sipChannel = self::model()->byQueueUser($userId)->find();

		if ($sipChannel && $sipChannel->active && $sipChannel->request_id) {
			$sipChannel->active = 0;
			$sipChannel->save();

			\Yii::app()->request->redirect('/request/request.htm?id=' . $sipChannel->request_id . ($params ? '&' . http_build_query($params): ''));
		}

		return $sipChannel ? $sipChannel->request_id : null;
	}
}
