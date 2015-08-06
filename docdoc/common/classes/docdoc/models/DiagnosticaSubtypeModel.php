<?php

namespace dfs\docdoc\models;

/**
 * Class DiagnosticaSubtypeModel
 * @package dfs\docdoc\models
 *
 * @property integer $id
 * @property integer $diagnostica_id
 * @property string $name
 * @property integer $priority
 *
 * @method DiagnosticaSubtypeModel findByPk
 * @method DiagnosticaSubtypeModel[] findAll
 * @method DiagnosticaSubtypeModel find
 */
class DiagnosticaSubtypeModel extends \CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return DiagnosticaSubtypeModel the static model class
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
		return 'diagnostica_subtype';
	}

	/**
	 * @return array
	 */
	public function relations()
	{
		return array(
			'diagnostics' => array(
				self::HAS_MANY, 'dfs\docdoc\models\DiagnosticaModel', 'diagnostica_subtype_id', 'order' => 'diagnostics.sort_in_subtype'
			),
		);
	}

	/**
	 * Поиск по виду диагностики
	 *
	 * @param int $diagnostic
	 *
	 * @return $this
	 */
	public function byDiagnostic($diagnostic)
	{
		$this->getDbCriteria()->mergeWith([
			'condition' => 'diagnostica_id = :diagnostic',
			'params'    => [':diagnostic' => $diagnostic],
		]);

		return $this;
	}

}