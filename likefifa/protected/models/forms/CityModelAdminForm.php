<?php


namespace likefifa\models\forms;

use CMap;
use likefifa\models\CityModel;
use Yii;

class CityModelAdminForm extends CityModel
{
	/**
	 * Ближайшие станции для формы в БО
	 *
	 * @var string
	 */
	public $nearStationsElement;

	/**
	 * @return array|\string[]
	 */
	public function rules()
	{
		return CMap::mergeArray(
			parent::rules(),
			[
				['nearStationsElement', 'safe']
			]
		);
	}

	public function attributeLabels()
	{
		return CMap::mergeArray(
			parent::attributeLabels(),
			[
				'nearStationsElement' => 'Ближайшие станции',
			]
		);
	}

	/**
	 * Получает модель класса
	 *
	 * @param string $className название класса
	 *
	 * @return CityModelAdminForm
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function afterFind()
	{
		$this->nearStationsElement = implode(',', $this->getRelationIds('nearStations'));
	}

	public function afterSave()
	{
		// Сохраняем ближайшие станции
		Yii::app()->db->createCommand()->delete('city_near_stations', 'city_id = :id', [':id' => $this->id]);
		if (mb_strlen($this->nearStationsElement) > 0) {
			$nearStations = explode(',', $this->nearStationsElement);
			foreach ($nearStations as $i => $ns) {
				Yii::app()->db->createCommand()->insert(
					'city_near_stations',
					[
						'city_id'    => $this->id,
						'station_id' => $ns,
						'priority'   => $i + 1,
					]
				);
			}
		}

		parent::afterSave();
	}
} 