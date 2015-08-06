<?php

use likefifa\models\CityModel;
use likefifa\components\Seo;

/**
 * This is the model class for table "lf_seo_text".
 *
 * The followings are the available columns in table 'lf_seo_text':
 *
 * @property integer          $id
 * @property integer          $disabled
 * @property string           $name
 * @property string           $text
 * @property integer          $sector_id
 * @property integer          $specialization_id
 * @property integer          $service_id
 * @property string           $meta_keywords
 * @property string           $meta_description
 * @property string           $page
 *
 * The followings are the available model relations:
 * @property Sector           $sector
 * @property LfService        $service
 * @property LfSpecialization $specialization
 */
class LfSeoText extends CActiveRecord
{
	const FOR_MASTERS = 1;
	const FOR_SALONS = 2;

	protected $types = array(
		self::FOR_MASTERS => 'для мастеров',
		self::FOR_SALONS  => 'для салонов',
	);

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return LfSeoText the static model class
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
		return 'lf_seo_text';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, text', 'required'),
			array(
				'disabled, sector_id, specialization_id, service_id, for_gallery, type',
				'numerical',
				'integerOnly' => true
			),
			array('name, page_title', 'length', 'max' => 512),
			array('meta_keywords, meta_description', 'safe'),
			array('page', 'numerical'),
			array('page', 'default', 'value' => null),
			array(
				'id, disabled, name, text, sector_id, specialization_id, service_id, meta_keywords, meta_description',
				'safe',
				'on' => 'search'
			),
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
			'sector'         => array(self::BELONGS_TO, 'Sector', 'sector_id'),
			'service'        => array(self::BELONGS_TO, 'LfService', 'service_id'),
			'specialization' => array(self::BELONGS_TO, 'LfSpecialization', 'specialization_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'                => 'ID',
			'disabled'          => 'Выключено',
			'name'              => 'Название',
			'text'              => 'Текст',
			'sector_id'         => 'Группа',
			'specialization_id' => 'Специализация',
			'service_id'        => 'Услуга',
			'meta_keywords'     => 'Ключевые слова',
			'meta_description'  => 'Meta описание',
			'page'              => 'Страница',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('disabled', $this->disabled);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('text', $this->text, true);
		$criteria->compare('sector_id', $this->sector_id);
		$criteria->compare('specialization_id', $this->specialization_id);
		$criteria->compare('service_id', $this->service_id);
		$criteria->compare('meta_keywords', $this->meta_keywords, true);
		$criteria->compare('meta_description', $this->meta_description, true);
		$criteria->compare('page', $this->page);

		return new CActiveDataProvider($this, array(
			'criteria'   => $criteria,
			'pagination' => array(
				'pageSize' => 50,
			),
		));
	}

	public function getTextSplitted()
	{
		$text = explode('<hr />', $this->text, 2);
		if (count($text) < 2) {
			$text = $text[0];
		} else {
			$pos = max(mb_strrpos($text[0], '</p>'), mb_strrpos($text[0], '</li>'));
			if ($pos !== false) {
				$text =
					mb_substr($text[0], 0, $pos) .
					'&nbsp;&nbsp;&nbsp;<a href="#" class="search-seo_open">Читать далее</a>' .
					mb_substr($text[0], $pos)
					.
					'<div class="search-seo_switch_txt">' .
					$text[1] .
					'<p><a href="#" class="search-seo_close">Свернуть</a></p></div>';

			}
		}

		return $text;
	}

	/**
	 * Получает модель
	 * В случае, если город не Москва, заменяет его на другие
	 *
	 * @param CityModel        $city           модель города
	 * @param LfSpecialization $specialization модель специальности
	 * @param LfService        $service        модель услуги
	 * @param bool             $gallery        галерея или нет
	 * @param string           $class          название класса
	 * @param integer $page текущая страница
	 *
	 * @return LfSeoText
	 */
	public function getModel($city, $specialization, $service, $gallery = false, $class = 'LfMaster', $page = 0)
	{
		$model = null;

		if (!$specialization && !$service) {
			return $model;
		}

		$criteria = new CDbCriteria;
		$criteria->params["type"] = $this->classToType($class);
		if ($service) {
			$criteria->condition = "type = :type AND service_id = :service";
			$criteria->params["service"] = $service->id;
		} else {
			if ($specialization) {
				$criteria->condition = "type = :type AND specialization_id = :spec AND service_id IS NULL";
				$criteria->params["spec"] = $specialization->id;
			}
		}
		if ($gallery) {
			$criteria->condition .= " AND for_gallery = 1";
		} else {
			$criteria->condition .= " AND for_gallery = 0 OR for_gallery IS NULL";
		}

		if ($page != 0) {
			$criteria->addCondition('t.page = :page');
			$criteria->params[':page'] = $page;
		} else {
			$criteria->addCondition('t.page = 0 OR t.page IS NULL');
		}
		
		$model = $this->find($criteria);

		if (!$model) {
			return $model;
		}

		$seo = new Seo($city);

		$nominativeFrom = "Москва";
		$nominativeTo = $seo::$location->name;
		$model->page_title = str_replace($nominativeFrom, $nominativeTo, $model->page_title);
		$model->meta_keywords = str_replace($nominativeFrom, $nominativeTo, $model->meta_keywords);
		$model->meta_description = str_replace($nominativeFrom, $nominativeTo, $model->meta_description);
		$model->text = str_replace($nominativeFrom, $nominativeTo, $model->text);

		$genitiveFrom = "Москвы";
		$genitiveTo = $seo::$location->name_genitive;
		$model->page_title = str_replace($genitiveFrom, $genitiveTo, $model->page_title);
		$model->meta_keywords = str_replace($genitiveFrom, $genitiveTo, $model->meta_keywords);
		$model->meta_description = str_replace($genitiveFrom, $genitiveTo, $model->meta_description);
		$model->text = str_replace($genitiveFrom, $genitiveTo, $model->text);

		$prepositionalFrom = "Москве";
		$prepositionalTo = $seo::$location->name_prepositional;
		$model->page_title = str_replace($prepositionalFrom, $prepositionalTo, $model->page_title);
		$model->meta_keywords = str_replace($prepositionalFrom, $prepositionalTo, $model->meta_keywords);
		$model->meta_description = str_replace($prepositionalFrom, $prepositionalTo, $model->meta_description);
		$model->text = str_replace($prepositionalFrom, $prepositionalTo, $model->text);

		return $model;
	}

	protected function classToType($class)
	{
		switch ($class) {
			case 'LfSalon':
				return self::FOR_SALONS;

			default:
				return self::FOR_MASTERS;
		}
	}

	public function getTypeListItems()
	{
		return $this->types;
	}
}