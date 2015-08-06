<?php
namespace dfs\docdoc\diagnostica\models;

use dfs\docdoc\models\CityModel;

/**
 * This is the model class for table "diagnostica".
 *
 * The followings are the available columns in table 'diagnostica':
 * @property integer $id
 * @property string $name
 * @property string $rewrite_name
 * @property string $title
 * @property string $meta_keywords
 * @property string $meta_description
 * @property integer $parent_id
 * @property string $accusative_name
 * @property string $genitive_name
 */
class Diagnostica extends \CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 * @return Diagnostica the static model class
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
			array('parent_id', 'numerical', 'integerOnly' => true),
			array('name, rewrite_name, title, accusative_name, genitive_name', 'length', 'max' => 512),
			array('reduction_name, meta_keywords, meta_description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array(
				'id, name, rewrite_name, title, meta_keywords, meta_description, parent_id, accusative_name,
					genitive_name',
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
			'diagnosticCenters' => array(self::MANY_MANY, 'DiagnosticCenter', 'diagnostic_center_diagnostica(diagnostica_id,diagnostic_center_id)'),
			'diagnosticCenterDiagnostics' => array(self::HAS_MANY, 'DiagnosticCenterDiagnostica', 'diagnostica_id'),
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
		//	$criteria->compare('parent_id',$this->parent_id);

		return new \CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination' => array(
				'pageSize' => 20,
			),
		));
	}

	/**
	 * @return array
	 */
	public function scopes()
	{
		return array_merge(
			parent::scopes(),
			array(
				'ordered' => array(
					'order' => 'sort, name ASC',
				),
			)
		);
	}

	/**
	 * @return string
	 */
	public function getFullName()
	{
		if ($this->parent_id == 0) {
			$name = $this->name;
		} else {
			$diagnostic = Diagnostica::model()->findByPk($this->parent_id);
			$name = $diagnostic->name . ' ' . $this->name;
		}

		return $name;
	}

	/**
	 * Список процедур
	 *
	 * @param bool $withEmpty
	 *
	 * @return string[]
	 */
	public function getListItems($withEmpty = false)
	{
		$items = array();

		if ($withEmpty) {
			$items[0] = 'верхний уровень';
		}

		$criteria = new CDbCriteria();
		$criteria->condition = "parent_id=0";
		$criteria->order = "name";
		$diagnostics = $this->findAll($criteria);
		foreach ($diagnostics as $diagnostic) {
			$items[$diagnostic->id] = $diagnostic->name;
		}

		return $items;
	}

	/**
	 * @param $id
	 * @return mixed|null|string|void
	 */
	public function getDiagnosticaName($id)
	{

		if ($id != 0) {
			$diagnostica = self::model()->find('id=:parentId', array(':parentId' => $id));
			$item = $diagnostica->name;
		} else
			$item = 'Верхний уровень';

		return $item;
	}

	/**
	 * @return bool
	 * @throws CHttpException
	 */
	protected function beforeDelete()
	{
		parent::beforeDelete();

		$countDiagnostics = self::model()->count('parent_id=:id', array(':id' => $this->id));
		if ($countDiagnostics > 0) {
//            	echo '<script>alert("Найдены связанные диагностики. Запрос на удаление не может быть выполнен!");</script>';                 

			throw new CHttpException(500, 'Найдены связанные диагностики. Запрос на удаление не может быть выполнен!');
		} else
			return true;

	}

	/**
	 * @return bool
	 */
	protected function beforeSave()
	{
		parent::beforeSave();

		$countDiagnostics = self::model()->count('(name=:name OR rewrite_name=:rname) AND parent_id=:pid', array(':name' => $this->name, ':rname' => $this->rewrite_name, ':pid' => $this->parent_id));
		if ($countDiagnostics > 1) {
			echo '<script>alert("Диагностика с таким названием уже существует!");</script>';
			return false;
			//               throw new CHttpException(500,'Диагностика с таким названием уже существует!');
		} else
			return true;

	}

	/**
	 * @param $word
	 * @param $endings
	 * @return mixed
	 */
	protected function replaceEnding($word, $endings)
	{
		foreach ($endings as $endingFrom => $endingTo) {
			if (preg_match('/' . $endingFrom . '$/u', $word)) {
				return preg_replace('/' . $endingFrom . '$/u', $endingTo, $word);
			}
		}
	}

	/**
	 * @param $word
	 * @param $many
	 * @return mixed
	 */
	protected function wordNominative($word, $many)
	{
		if (!$many) return $word;

		return $this->replaceEnding($word, array(
			'р' => 'ры',
			'г' => 'ги',
			'т' => 'ты',
			'д' => 'ды',
			'ий' => 'ие',
		));
	}

	/**
	 * @param $word
	 * @param $many
	 * @return mixed
	 */
	protected function wordGenitive($word, $many)
	{
		if (!$many) {
			return $this->replaceEnding($word, array(
				'ная' => 'ной',
				'ия' => 'ии',
				'ские' => 'ских',
				'ды' => 'дов',
				'ое' => 'ого',
				'ие' => 'ия',
				'а' => 'и',
				'р' => 'ра',
				'г' => 'га',
				'т' => 'та',
				'д' => 'да',
				'ий' => 'ого',
				'ен' => 'ена',
			));
		} else {
			return $this->replaceEnding($word, array(
				'р' => 'ров',
				'г' => 'гов',
				'т' => 'тов',
				'д' => 'дов',
				'ий' => 'их',
			));
		}
	}

	/**
	 * @param $word
	 * @param $many
	 * @return mixed
	 */
	protected function wordDative($word, $many)
	{
		if (!$many) {
			return $this->replaceEnding($word, array(
				'фия' => 'фию',
				'пия' => 'пию',
				'ая' => 'ую',
				'е' => 'е',
				'ды' => 'ды',
				'ния' => 'ния',
				'ен' => 'ен',
				'ой' => 'ой',
				'и' => 'и',
				'для' => 'для',
				'ых' => 'ых',
				'd' => 'd',
				'ка' => 'ку',
				'по' => 'по',
				'ру' => 'ру',
				'ба' => 'бу',
				'ия' => 'ия',
				'ил' => 'ил',
				'ст' => 'ст',
			));
		} else {
			return $this->replaceEnding($word, array(
				'р' => 'рам',
				'г' => 'гам',
				'т' => 'там',
				'д' => 'дам',
				'ий' => 'им',
			));
		}
	}

	/**
	 * Метод обработки скланений
	 *
	 * @param string $words Склоняемое слово
	 * @param bool $many Одно слово или несколько заменяем
	 * @param string $callback Название метода в этом классе, для обработки значения
	 *
	 * @return string Обработанное выражение
	 */
	protected function parseWords($words, $many, $callback)
	{
		return preg_replace_callback(
			'/([a-zа-яё]+)/u',
			function ($matches) use ($callback, $many) {
				return $this->{$callback}($matches[1], $many);
			},
			$words
		);
	}

	/**
	 * @param bool $many
	 * @return string
	 */
	public function nameInNominative($many = false)
	{
		return $this->parseWords($this->name, $many, 'wordNominative');
	}

	/**
	 * @param bool $many
	 * @return string
	 */
	public function nameInGenitive($many = false)
	{
		return $this->parseWords($this->name, $many, 'wordGenitive');
	}

	/**
	 * @param bool $many
	 * @return string
	 */
	public function reductionNameInGenitive($many = false)
	{
		if ($this->reduction_name)
			return $this->parseWords($this->reduction_name, $many, 'wordGenitive');
		else
			return mb_strtolower($this->parseWords($this->name, $many, 'wordGenitive'));
	}

	/**
	 * @param $word
	 * @return string
	 */
	public function nameInDative($word)
	{
		if (mb_strtolower($word) == 'магнитно-резонансная томография')
			return 'магнитно-резонансную томографию';
		elseif (mb_strtolower($word) == 'компьютерная томография')
			return 'компьютерную томографию';
		else
			return $this->parseWords($word, false, 'wordDative');
	}

	/**
	 * @return string
	 */
	public function fullName()
	{
		if ($this->rewrite_name == "/uzi/")
			return 'ультразвуковое исследование';
		elseif ($this->rewrite_name == "/komputernaya-tomografiya/")
			return 'компьютерная томография';
		elseif ($this->rewrite_name == "/mrt/")
			return 'магнитно-резонансная томография';
		elseif ($this->rewrite_name == "/ehokardiografiya/")
			return 'эхокардиография';
		elseif ($this->rewrite_name == "/rentgen/")
			return 'рентгенография';
		else
			return $this->name;
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
	 * @return array
	 */
	public function getTree()
	{
		$items = Diagnostica::model()->ordered()->findAll();

		$tree = array();
		foreach ($items as $item) {
			if ($item->parent_id == 0) {
				$tree[$item->id]['name'] = $item->name;
				$tree[$item->id]['childs'] = array();
				foreach ($items as $subItem) {
					if ($subItem->parent_id == $item->id)
						$tree[$item->id]['childs'][$subItem->id]['name'] = $subItem->name;
				}
			}
		}

		return $tree;
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
	 * Получение алиаса без слэшей
	 *
	 * @return mixed
	 */
	public function getRewriteName()
	{
		return str_replace('/', '', $this->rewrite_name);
	}

}