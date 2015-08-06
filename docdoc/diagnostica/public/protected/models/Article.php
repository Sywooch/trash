<?php

/**
 * This is the model class for table "article".
 *
 * The followings are the available columns in table 'article':
 * @property integer $id
 * @property integer $disabled
 * @property string $name
 * @property string $description
 * @property string $text
 */
class Article extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Article the static model class
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
		return 'article';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('disabled, name, text', 'required'),
			array('name, title, meta_description, meta_keywords', 'filter', 'filter' => 'strip_tags'),
			array('disabled', 'numerical', 'integerOnly'=>true),
			array('description', 'safe'),
			array('name, rewrite_name, title, meta_description, meta_keywords', 'length', 'max'=>512),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, disabled, name, description, text', 'safe', 'on'=>'search'),
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
		);
	}
	
	public function scopes() {
		return array(
			'onlyActive' => array(
				'condition' => '(t.disabled IS NULL OR t.disabled = 0)',
			),
			'ordered' => array(
				'order' => 'name ASC',
			),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'disabled' => 'Скрыть статью',
			'name' => 'Название статьи',
			'description' => 'Аннотация статьи',
			'text' => 'Текст статьи',
			'rewrite_name' => 'Алиас для ЧПУ',
			'title' => 'Тайтл',
			'meta_keywords' => 'Meta keywords',
			'meta_description' => 'Meta description',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('disabled',$this->disabled);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('text',$this->text,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize' => 20,
			),
		));
	}
}