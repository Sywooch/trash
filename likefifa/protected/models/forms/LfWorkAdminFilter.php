<?php


namespace likefifa\models\forms;

use CActiveDataProvider;
use CMap;
use LfWork;
use likefifa\components\system\DbCriteria;

class LfWorkAdminFilter extends LfWork
{
	/**
	 * Начальная дата для фильтрации
	 *
	 * @var string
	 */
	public $createdFrom;

	/**
	 * Конечная дата для фильтрации
	 *
	 * @var string
	 */
	public $createdTo;

	/**
	 * Регион для фильтрации
	 *
	 * @var integer
	 */
	public $region;

	/**
	 * @return array
	 */
	public function rules()
	{
		return CMap::mergeArray(
			parent::rules(),
			[
				[
					'id, specialization_id, service_id, master_id, image, likes, sort, createdFrom, createdTo, region',
					'safe',
					'on' => 'search'
				],
			]
		);
	}

	/**
	 * @return array
	 */
	public function attributeLabels()
	{
		return CMap::mergeArray(
			parent::attributeLabels(),
			[
				'createdFrom' => 'Создание от',
				'createdTo'   => 'Создание до',
				'region'      => 'Регион',
			]
		);
	}

	/**
	 * Поиск работ в БО
	 *
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria = new DbCriteria();

		$criteria->compare('t.id', $this->id);
		$criteria->compare('t.master_id', $this->master_id);
		$criteria->compare('t.likes', $this->likes);
		$criteria->compare('t.alt', $this->alt, true);
		$criteria->compare('t.sort', $this->sort);
		$criteria->compare('t.specialization_id', $this->specialization_id);
		$criteria->compare('t.service_id', $this->service_id);

		if($this->region) {
			$criteria->with['master'] = [
				'with' => 'city',
			];
			$criteria->compare('city.region_id', $this->region);
		}

		if (!empty($this->createdFrom) || !empty($this->createdTo)) {
			$createdFrom = $createdTo = null;
			if (!empty($this->createdFrom)) {
				$createdFrom = date('Y-m-d 00:00:00', strtotime($this->createdFrom));
			}
			if (!empty($this->createdTo)) {
				$createdTo = date('Y-m-d 23:59:59', strtotime($this->createdTo));
			}

			$criteria->addBetweenCondition('t.created', $createdFrom, $createdTo);
		}

		return new CActiveDataProvider(
			$this, array(
				'criteria'   => $criteria,
				'pagination' => array(
					'pageSize' => 50,
				),
			)
		);
	}

	/**
	 * Получает отформатированную дату создания
	 *
	 * @return string
	 */
	public function getCreatedFormatted()
	{
		if ($this->created != "0000-00-00 00:00:00") {
			return date("d.m.Y", strtotime($this->created));
		}
		return "Не определено";
	}

	/**
	 * @param string $className
	 *
	 * @return LfWorkAdminFilter
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
} 