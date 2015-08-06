<?php

namespace dfs\docdoc\models;


/**
 * Class PartnerSectorMappingModel
 *
 * соответствие специальностей у нас специальностям на сайте партнера
 *
 * @property int $id
 * @property int $partner_id
 * @property int $sector_id
 * @property int $partner_sector
 *
 * @property PartnerModel $partner модель партнера
 * @property SectorModel $sector Специальность
 *
 * @method PartnerSectorMappingModel find
 * @method PartnerSectorMappingModel findByAttributes
 * @method PartnerSectorMappingModel[] findAll
 * @method PartnerSectorMappingModel cache
 * @method PartnerSectorMappingModel with
 */
class PartnerSectorMappingModel extends \CActiveRecord
{
	/**
	 * @return string
	 */
	public function tableName()
	{
		return 'partner_sector_mapping';
	}

	/**
	 * @return mixed|string|void
	 */
	public function primaryKey()
	{
		return 'id';
	}

	/**
	 * @param string $className
	 *
	 * @return PartnerSectorMappingModel
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model(__CLASS__);
	}

	/**
	 * Возвращает связи между объектами
	 *
	 * @return array
	 */
	public function relations()
	{
		return array(
			'partner' => [ self::BELONGS_TO, PartnerModel::class, 'partner_id' ],
			'sector' => [ self::BELONGS_TO, SectorModel::class, 'sector_id' ],
		);
	}

	/**
	 * Поиск по партнеру
	 *
	 * @param int $partnerId
	 *
	 * @return $this
	 */
	public function byPartner($partnerId)
	{
		$this->getDbCriteria()
			->mergeWith(
				[
					'condition' => 'partner_id = :partner_id',
					'params'    => [':partner_id' => $partnerId]
				]
			);
		return $this;
	}

	/**
	 * Поиск по партнерской специальноси
	 *
	 * @param string[] $sectors
	 *
	 * @return $this
	 */
	public function inSectors(array $sectors)
	{
		$criteria = new \CDbCriteria();
		$criteria->addInCondition('partner_sector', $sectors);
		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}


} 
