<?php

namespace dfs\docdoc\models;

/**
 * This is the model class for table "diagnostica".
 *
 * The followings are the available columns in table 'diagnostica':
 *
 * @property integer            $id
 * @property string             $name
 * @property string             $rewrite_name
 * @property string             $title
 * @property string             $meta_keywords
 * @property string             $meta_desc
 * @property string             $meta_description
 * @property integer            $parent_id
 * @property string             $reduction_name
 * @property integer            $sort
 * @property integer            $diagnostica_subtype_id
 * @property string             $accusative_name
 * @property string             $genitive_name
 *
 * @property DiagnosticaModel[] $childs
 * @property DiagnosticaModel[] $diagnosticClinics
 * @property DiagnosticaModel   $parent
 *
 * The followings are the available model relations:
 *
 *
 * @method DiagnosticaModel findByPk
 * @method DiagnosticaModel[] findAll
 * @method DiagnosticaModel find
 */
class DiagnosticaModel extends \CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return DiagnosticaModel the static model class
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
		return 'diagnostica';
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
			array('parent_id', 'numerical', 'integerOnly'=>true),
			array('name, rewrite_name, title, accusative_name, genitive_name', 'length', 'max'=>512),
			array('reduction_name, meta_keywords, meta_description, sort', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array(
				'id, name, rewrite_name, title, meta_keywords, meta_description, parent_id, accusative_name,
					genitive_name',
				'safe',
				'on'=>'search'
			),
		);
	}

	/**
	 * Зависимости
	 *
	 * @return array
	 */
	public function relations()
	{
		return array(
			'parent' => array(self::BELONGS_TO, '\dfs\docdoc\models\DiagnosticaModel', 'parent_id'),
			'childs' => array(self::HAS_MANY, '\dfs\docdoc\models\DiagnosticaModel', 'parent_id'),
			'diagnosticClinics' => [
				self::HAS_MANY, '\dfs\docdoc\models\DiagnosticClinicModel', 'diagnostica_id'
			],
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Название диагностики',
			'reducton_name' => 'Сокращение',
			'rewrite_name' => 'Алиас для ЧПУ',
			'title' => 'Тайтл',
			'meta_keywords' => 'Ключевые слова',
			'meta_description' => 'SEO-текст',
			'parent_id' => 'Привязка к диагностике:',
			'reduction_name' => 'Сокращение',
			'sort' => 'Сортировка',
			'accusative_name' => 'Название диагностики в винительном падеже',
			'genitive_name' => 'Название диагностики в родительном падеже',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return \CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new \CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('rewrite_name', $this->rewrite_name, true);
		$criteria->compare('title', $this->title, true);
		$criteria->compare('meta_keywords', $this->meta_keywords, true);
		$criteria->compare('meta_description', $this->meta_description, true);
		$criteria->compare('accusative_name', $this->meta_keywords, true);
		$criteria->compare('genitive_name', $this->meta_description, true);

		return new \CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination' => array(
				'pageSize' => 20,
			),
		));
	}

	/**
	 * Поиск по алиасу
	 *
	 * @param $alias
	 *
	 * @return $this
	 */
	public function searchByAlias($alias)
	{
		$this->getDbCriteria()
			->mergeWith(array(
					'condition' => "t.rewrite_name = :rewrite_name",
					'params' => array(':rewrite_name' => "/{$alias}/"),
				)
			);

		return $this;
	}

	/**
	 * Поиск по родителю
	 *
	 * @param $id
	 *
	 * @return $this
	 */
	public function searchByParent($id)
	{
		$this->getDbCriteria()
			->mergeWith(array(
					'condition' => "t.parent_id = :parent_id",
					'params' => array(':parent_id' => $id),
				)
			);

		return $this;
	}

	/**
	 * Поиск по клинике
	 *
	 * @param int $clinic
	 *
	 * @return $this
	 */
	public function byClinic($clinic)
	{
		$this->getDbCriteria()->mergeWith(
			array(
				'condition' => "diagnosticClinics.clinic_id = :clinic",
				'params'    => array(':clinic' => $clinic),
				'with'      => 'diagnosticClinics',
			)
		);

		return $this;
	}

	/**
	 * Поиск только главных диагностик
	 *
	 * @return $this
	 */
	public function onlyParents()
	{
		$this->getDbCriteria()
			->mergeWith(array(
					'condition' => " t.parent_id = 0 OR t.parent_id IS NULL",
				)
			);

		return $this;
	}

	/**
	 * Получение алиаса без слэшей
	 *
	 * @return mixed
	 */
	public function getRewriteName()
	{
		return str_replace('/', '', $this->rewrite_name);
	}

	/**
	 * Получение сокращенного названия
	 *
	 * @return string
	 */
	public function getShortName()
	{
		return $this->reduction_name ?: $this->name;
	}

	/**
	 * Получение полного названия исследования
	 *
	 * @return string
	 */
	public function getFullName()
	{
		return $this->parent ? "{$this->parent->getShortName()} {$this->name}" : $this->name;
	}

	/**
	 * Получение пути
	 *
	 * @return string
	 */
	public function getUrl()
	{
		return $this->parent
			? "/{$this->parent->getRewriteName()}/{$this->getRewriteName()}"
			: "/{$this->getRewriteName()}";
	}

	/**
	 * Получение короткого описания
	 *
	 * @param CityModel $city
	 * @param int $paragraphs
	 *
	 * @return string
	 */
	public function getShortDescription(CityModel $city, $paragraphs = 2)
	{
		$arr = explode('</p>', $this->getSeoText($city));

		$output = '';
		for ($i = 0; $i < $paragraphs; $i++) {
			$output .= $arr[$i];
		}

		return $output;
	}

	/**
	 * Получение сео-текста
	 *
	 * @param CityModel $city
	 *
	 * @return string
	 */
	public function getSeoText(CityModel $city)
	{
		$patterns = [
			'{city}',
			'{cityInGenitive}',
			'{cityInPrepositional}',
		];
		$replacements = [
			$city->title,
			$city->title_genitive,
			$city->title_prepositional,
		];

		return str_replace($patterns, $replacements, $this->meta_description);
	}

	/**
	 * Возвращает ид сервиса(пока не ясно что это и где, на будущее)
	 *
	 * @return int
	 */
	public function getServiceId()
	{
		$diagnosticId = $this->parent_id == 0 ? $this->id : $this->parent_id;

		$group = ContractGroupModel::model()
			->forService($diagnosticId, ContractGroupModel::KIND_DIAGNOSTICS)
			->find();

		if (!is_null($group) && $group->id == ContractGroupModel::MRT_KT) {
			$service_id = ServiceModel::TYPE_SUCCESSFUL_DIAGNOSTICS_MRT_OR_KT;
		} else {
			$service_id = ServiceModel::TYPE_SUCCESSFUL_DIAGNOSTICS_OTHER;
		}

		return $service_id;
	}

	/**
	 * Список диагностик
	 *
	 * @return array
	 */
	public static function getListDiagnostics()
	{
		$model = self::model();

		$criteria = $model->getDbCriteria();
		$criteria->with = [ 'parent' ];
		$criteria->order = 'parent.name, t.name';
		$criteria->together = true;

		$data = [];
		foreach ($model->findAll() as $item) {
			$data[$item->id] = $item->getFullName();
		}
		return $data;
	}

	/**
	 * Получение диагностики-родителя
	 *
	 * @param bool $nameInDative
	 *
	 * @return mixed|string
	 */
	public function getParentName($nameInDative = false)
	{
		if ($this->reduction_name) {
			if ($nameInDative)
				return $this->nameInDative($this->reduction_name);
			else
				return $this->reduction_name;
		} else {
			if ($nameInDative)
				return $this->nameInDative($this->name);
			else
				return $this->name;
		}
	}

	/**
	 * Выборка дочерних диагностик для массива родительских диагностик
	 *
	 * @param int[] $parents
	 * @return $this
	 */
	public function childsForParents($parents = [])
	{
		$criteria = new \CDbCriteria();
		$criteria->addInCondition('parent_id', $parents);

		$this->getDbCriteria()
			->mergeWith($criteria);

		return $this;
	}

	/**
	 * Выборка без родительских диагностик, имеющих поддиагностики
	 *
	 * @return $this
	 */
	public function withoutParents()
	{
		$alias = $this->getTableAlias();

		$this->getDbCriteria()->mergeWith([
			'join' => 'LEFT JOIN diagnostica as child ON (' . $alias . '.parent_id = 0 AND ' . $alias . '.id = child.parent_id)',
			'condition' => 'child.id IS NULL',
		]);

		return $this;
	}
}
