<?php
use likefifa\components\system\ActiveRecord;

/**
 * This is the model class for table "article".
 *
 * The followings are the available columns in table 'article':
 * @property integer $id
 * @property integer $disabled
 * @property string $name
 * @property string $description
 * @property string $text
 *
 */
class Article extends ActiveRecord
{
	/**
	 * @param string $className
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
		return array(
			array('disabled, name, text', 'required'),
			array('name, title, meta_description, meta_keywords', 'filter', 'filter' => 'strip_tags'),
			array('disabled, is_memo, article_section_id', 'numerical', 'integerOnly'=>true),
			array('description', 'safe'),
			array('name, rewrite_name, title, meta_description, meta_keywords', 'length', 'max'=>512),
			array('id, article_section_id, disabled, name, description, text', 'safe', 'on'=>'search'),
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
			'section' => array(self::BELONGS_TO, 'LfSpecialization', 'article_section_id', 'together' => true),
			'services' => array(self::MANY_MANY, 'LfService', 'lf_article_service(article_id, service_id)', 'with' => 'specialization', 'together' => true),
		);
	}
	
	public function scopes() {
		return array(
			'onlyActive' => array(
				'condition' => '(t.disabled IS NULL OR t.disabled = 0)',
			),
			'last' => array(
				'order' => 't.id DESC',
				'limit' => 4,
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
			'is_memo' => 'В памятке пациента',
			'article_section_id' => 'Раздел',
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
	
	public function getCountArticles($section)
	{
		$count = Article::model()->count("article_section_id=:section AND disabled=0", array(":section"=>$section));
		
		return $count;
	}
	
	public function behaviors()
	{
		return array(
				'CAdvancedArBehavior' => array(
						'class' => 'application.extensions.CAdvancedArBehavior',
				),
		);
	}
	
	public function getDetailUrl() {
		$params = array(
			'articleRewriteName' => ($this->rewrite_name ?: $this->id),		
		);
		
		if ($this->section) {
			$params['sectionRewriteName'] = $this->section->getRewriteName();
		}
		
		return Yii::app()->createUrl('article/view', $params);
	}

	/**
	 * Получает ссылку на раздел
	 *
	 * @return string
	 */
	public function getSectionUrl() {
		$params = array();
		if ($this->section) {
			$params["sectionRewriteName"] = $this->section->getRewriteName();
		}

		return Yii::app()->createUrl('article/index', $params);
	}

	/**
	 * Находит статьи для специализаций и услуг
	 *
	 * @param LfSpecialization $specialization
	 * @param LfService        $service
	 *
	 * @return self[]
	 */
	public function findBySpecAndService($specialization, $service = null)
	{
		$model = $this->with(array('services', 'section'));
		$model->dbCriteria->order = 'rand()';

		if ($service) {
			$model = $model->findAll('services.id = :service', array(':service' => $service->id));
		} else {
			if ($specialization) {
				$model = $model->findAll('article_section_id = :spec', array(':spec' => $specialization->id));
			} else {
				return array();
			}
		}

		return $model;
	}

	/**
	 * Получает модели статей для специализации
	 *
	 * @param string $speciality абривиатура специализации
	 *
	 * @return self[]
	 */
	public function findBySpeciality($speciality)
	{
		$articles = array();

		if ($speciality) {
			$group = LfGroup::model()->getModelByRewriteName($speciality);
			if ($group && $group->specializations) {
				foreach ($group->specializations as $specialization) {
					$articles = array_merge($articles, $this->findBySpecAndService($specialization));
				}

				shuffle($articles);
			}
		}

		return $articles;
	}
}