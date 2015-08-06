<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 12.11.14
 * Time: 15:31
 */

namespace dfs\docdoc\models;

/**
 * Class GoogleBigQueryModel
 * @package dfs\common\classes\docdoc\models
 *
 * @property string $token
 * @property string $mtime
 * @property int $id
 * @method GoogleBigQueryModel find
 */
class GoogleBigQueryModel extends \CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return GoogleBigQueryModel the static model class
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
		return 'google_big_query';
	}

	public function beforeSave()
	{
		parent::beforeSave();
		$this->mtime = date('Y-m-d H:i:s');
		return true;
	}
} 
