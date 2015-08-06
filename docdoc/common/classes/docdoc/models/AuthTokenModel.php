<?php

namespace dfs\docdoc\models;


/**
 * This is the model class for table "auth_token".
 *
 * The followings are the available columns in table 'auth_token':
 *
 * @property int    $id
 * @property string $token
 * @property string $type
 * @property string $expired
 * @property int    $using
 * @property int    $user_id
 *
 * @method AuthTokenModel[] findAll
 * @method AuthTokenModel find
 */
class AuthTokenModel extends \CActiveRecord
{
	const TYPE_LK = 'lk';
	const TYPE_PK = 'pk';
	const TYPE_BO = 'bo';

	const EXPIRED_TIME = 86400;


	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return AuthTokenModel the static model class
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
		return 'auth_token';
	}

	/**
	 * Выбрать по токену
	 *
	 * @param string $token
	 *
	 * @return AuthTokenModel
	 */
	public function findByToken($token)
	{
		return $this->findByAttributes([
			'token' => $token,
		]);
	}

	/**
	 * Выборка только активных токенов
	 *
	 * @return $this
	 */
	public function active()
	{
		$alias = $this->getTableAlias();

		$this->getDbCriteria()->mergeWith([
			'condition' => "$alias.expired > NOW() AND $alias.using = 0",
		]);

		return $this;
	}

	/**
	 * Генераци токена
	 *
	 * @param string $type
	 * @param int $userId
	 *
	 * @return $this
	 */
	public function generateToken($type, $userId)
	{
		$this->type = $type;
		$this->user_id = $userId;

		$this->token = bin2hex(openssl_random_pseudo_bytes(16)) . time();

		if (!$this->expired) {
			$this->expired = date('Y-m-d H:m:i', time() + self::EXPIRED_TIME);
		}

		return $this;
	}
}
