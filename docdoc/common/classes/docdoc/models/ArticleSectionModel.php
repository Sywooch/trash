<?php

namespace dfs\docdoc\models;

use CDbCriteria, CActiveRecord, CActiveDataProvider;

/**
 * This is the model class for table "article_section".
 *
 * The followings are the available columns in table 'article_section':
 *
 * @property integer $id
 * @property string  $name
 * @property string  $rewrite_name
 * @property string  $text
 * @property string  $title
 * @property string  $meta_keywords
 * @property string  $meta_description
 */
class ArticleSectionModel extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return ArticleSectionModel the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'article_section';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, rewrite_name', 'required'),
			array('name, rewrite_name, title, meta_keywords, meta_description', 'length', 'max' => 512),
			array('text, sector_id', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, rewrite_name, text, title, meta_keywords, meta_description', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'articles' => array(self::HAS_MANY, 'Article', 'article_section_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'               => 'ID',
			'name'             => 'Название',
			'rewrite_name'     => 'Алиас для ЧПУ',
			'text'             => 'SEO-текст',
			'title'            => 'Title',
			'meta_keywords'    => 'Ключевые слова',
			'meta_description' => 'Описание',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('t.id', $this->id);
		$criteria->compare('t.name', $this->name, true);
		$criteria->compare('t.rewrite_name', $this->rewrite_name, true);
		$criteria->compare('t.text', $this->text, true);
		$criteria->compare('t.title', $this->title, true);
		$criteria->compare('t.meta_keywords', $this->meta_keywords, true);
		$criteria->compare('t.meta_description', $this->meta_description, true);

		return new CActiveDataProvider(
			$this, array(
				'criteria' => $criteria,
			)
		);
	}

	/**
	 *
	 * @return string[]
	 */
	public function getListItems()
	{
		$items = array();

		$sections = $this->findAll();
		foreach ($sections as $section) {
			$items[$section->id] = $section->name;
		}

		return $items;
	}
}