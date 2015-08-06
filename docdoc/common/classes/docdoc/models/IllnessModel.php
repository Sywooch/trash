<?php

namespace dfs\docdoc\models;

use CActiveDataProvider, CDbCriteria, CException;

/**
 * This is the model class for table "illness".
 *
 * The followings are the available columns in table 'illness':
 *
 * @property int    $id
 * @property int    $sector_id
 * @property string $name
 * @property string $rewrite_name
 * @property string $full_name
 * @property string $text_desc
 * @property string $text_symptom
 * @property string $text_treatment
 * @property string $text_other
 * @property string $title
 * @property string $meta_keywords
 * @property string $meta_desc
 * @property int    $is_hidden
 *
 * @property SectorModel $sector
 *
 * @method IllnessModel[] findAll
 * @method IllnessModel find
 * @method IllnessModel findByPk
 */
class IllnessModel extends \CActiveRecord
{
	/**
	 * @var array
	 */
	static public $alphabet = array(
		'A' => 'А',
		'B' => 'Б',
		'V' => 'В',
		'G' => 'Г',
		'D' => 'Д',
		// 'EY' => 'Е',
		// 'YO' => 'Ё',
		'Zh' => 'Ж',
		'Z' => 'З',
		'I' => 'И',
		'Y' => 'Й',
		'K' => 'К',
		'L' => 'Л',
		'M' => 'М',
		'N' => 'Н',
		'O' => 'О',
		'P' => 'П',
		'R' => 'Р',
		'S' => 'С',
		'T' => 'Т',
		'U' => 'У',
		'F' => 'Ф',
		'H' => 'Х',
		'Ts' => 'Ц',
		'Ch' => 'Ч',
		'Sh' => 'Ш',
		// 'Sht' => 'Щ',
		'E' => 'Э',
		// 'Yu' => 'Ю',
		'Ya' => 'Я',
	);


	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return IllnessModel the static model class
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
		return 'illness';
	}

	/**
	 * @return string имя первичного ключа
	 */
	public function primaryKey()
	{
		return 'id';
	}

	/**
	 * Возвращает правила проверки для атрибутов модели
	 *
	 * @return string[]
	 */
	public function rules()
	{
		return [
			['sector_id, name, rewrite_name', 'required'],
			['sector_id, is_hidden', 'numerical', 'integerOnly' => true],
			['name, rewrite_name, full_name', 'length', 'max' => 512],
			[
				'name, full_name, text_desc, text_symptom, text_treatment, text_other, title, meta_keywords, meta_desc',
				'filter',
				'filter' => 'strip_tags'
			],
			['rewrite_name', 'dfs\docdoc\validators\StringValidator', 'type' => 'latinCharacters'],
		];
	}


	/**
	 * Возвращает связи между объектами
	 *
	 * @return string[]
	 */
	public function relations()
	{
		return [
			'sector' => [self::BELONGS_TO, SectorModel::class, 'sector_id'],
		];
	}

	/**
	 * Первая буква названия
	 *
	 * @return string
	 */
	public function getFirstLetter()
	{
		return mb_strtoupper(mb_substr($this->name, 0, 1, 'utf-8'), 'utf-8');
	}

	/**
	 * Поиск активных городов
	 *
	 * @return $this
	 */
	public function active()
	{
		$this->getDbCriteria()->mergeWith([
			'condition' => 'is_hidden = 0',
		]);

		return $this;
	}

	/**
	 * Исключить из выборки заболевания
	 *
	 * @param int[] $ids
	 *
	 * @return $this
	 */
	public function excludeIds($ids)
	{
		$this->getDbCriteria()->addNotInCondition($this->getTableAlias() . '.id', $ids);

		return $this;
	}

	/**
	 * Выборка по rewrite_name
	 *
	 * @param string $rewriteName
	 *
	 * @return $this
	 */
	public function byRewriteName($rewriteName)
	{
		$this->getDbCriteria()->mergeWith([
			'condition' => $this->getTableAlias() . '.rewrite_name = :rewriteName',
			'params'    => [':rewriteName' => $rewriteName],
		]);

		return $this;
	}

	/**
	 * Выборка по специальности
	 *
	 * @param int $sectorId
	 *
	 * @return $this
	 */
	public function bySector($sectorId)
	{
		$this->getDbCriteria()->mergeWith([
			'condition' => $this->getTableAlias() . '.sector_id = :sectorId',
			'params'    => [':sectorId' => $sectorId],
		]);

		return $this;
	}

	/**
	 * Выборка по первой букве названия
	 *
	 * @param string $letter
	 *
	 * @return $this
	 */
	public function byFirstLetter($letter)
	{
		$this->getDbCriteria()->mergeWith([
			'condition' => $this->getTableAlias() . '.name LIKE :nameTemplate',
			'params'    => [':nameTemplate' => $letter . '%'],
		]);

		return $this;
	}
}
