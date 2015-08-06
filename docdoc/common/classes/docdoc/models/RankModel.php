<?php

namespace dfs\docdoc\models;

/**
 * Class RankModel
 * @package dfs\docdoc\models
 *
 * @property integer $rank_id
 * @property string $title
 *
 * @method RankModel findByPk
 * @method RankModel[] findAll
 * @method RankModel find
 */
class RankModel extends \CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return RankModel the static model class
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
		return 'rank_dict';
	}

	/**
	 * @return array
	 */
	public function relations() {
		return array(
		);
	}

} 