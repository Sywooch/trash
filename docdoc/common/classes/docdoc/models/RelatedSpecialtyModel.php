<?php
namespace dfs\docdoc\models;

use Yii;
/**
 * This is the model class for table "related_specialty".
 *
 * The followings are the available columns in table 'related_specialty':
 *
 * @property integer $specialty_id
 * @property integer $related_specialty_id
 * @property SectorModel $sector
 *
 */
class RelatedSpecialtyModel extends \CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return RelatedSpecialtyModel the static model class
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
		return 'related_specialty';
	}

	/**
	 * @return string имя первичного ключа
	 */
	public function primaryKey()
	{
		return array('specialty_id', 'related_specialty_id');
	}

	/**
	 * @return array
	 */
	public function relations()
	{
		return array(
			'sector' => array(self::BELONGS_TO, SectorModel::class, 'specialty_id'),
		);
	}

	/**
	 * Поиск по ид специальности
	 *
	 * @param $specialty
	 *
	 * @return $this
	 */
	public function bySpecialty($specialty)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => "specialty_id = :specialty",
					'params'    => [':specialty' => $specialty],
				]
			);

		return $this;
	}
}