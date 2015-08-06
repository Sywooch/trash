<?php
namespace dfs\modules\payments\models;

use CActiveRecord;

/**
 * Class PaymentsProcessor
 *
 * @author  Aleksey Parshukov <parshukovag@gmail.com>
 * @date    24.09.2013
 *
 * @see     https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=1310733
 *
 * @property int                                          $id
 * @property string                                       $key
 * @property int                                          $account_id
 *
 * @property \dfs\modules\payments\models\PaymentsAccount $account
 *
 * @package dfs\modules\payments
 */
class PaymentsProcessor extends CActiveRecord
{
	/**
	 * @param string $className
	 *
	 * @return PaymentsProcessor
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string
	 */
	public function tableName()
	{
		return 'payments_processor';
	}

	/**
	 * @return array[] relational rules.
	 */
	public function relations()
	{
		return array(
			'account' => array(self::BELONGS_TO, '\dfs\modules\payments\models\PaymentsAccount', 'account_id'),
		);
	}

	/**
	 * @param string $key
	 *
	 * @return PaymentsProcessor|null
	 */
	public static function findByKey($key)
	{
		return static::model()->findByAttributes(
			array(
				'key' => $key,
			)
		);
	}

	/**
	 * Процесор по умолчанию
	 *
	 * @return PaymentsProcessor
	 */
	public static function findDefault()
	{
		return self::findByKey('robokassa');
	}

	/**
	 * Получить баланс аккаунта
	 *
	 * @return int Актуальный баланс в рублях
	 */
	public function getBalance()
	{
		return $this->account->getAmount();
	}

	/**
	 * @return \dfs\modules\payments\base\Processor
	 */
	public function getProcessor()
	{
		$class = '\\dfs\\modules\\payments\\processors\\' . ucfirst($this->key);
		return new $class();
	}
}