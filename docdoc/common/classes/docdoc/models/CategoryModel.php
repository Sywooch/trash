<?php

namespace dfs\docdoc\models;

/**
 * Class CategoryModel
 * @package dfs\docdoc\models
 *
 * @property integer $category_id
 * @property string $title
 *
 * @method CategoryModel findByPk
 * @method CategoryModel[] findAll
 * @method CategoryModel find
 */
class CategoryModel extends \CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return CategoryModel the static model class
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
		return 'category_dict';
	}

	/**
	 * @return array
	 */
	public function relations() {
		return array(
		);
	}

} 