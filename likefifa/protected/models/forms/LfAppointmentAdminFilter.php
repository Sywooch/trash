<?php

namespace likefifa\models\forms;

use CMap;
use LfAppointment;

class LfAppointmentAdminFilter extends LfAppointment
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
	 * Галка "В избранном@
	 *
	 * @var integer
	 */
	public $favoriteLabel;

	/**
	 * Иконки источника
	 *
	 * @var array
	 */
	public static $sourcesIcons = [
		self::SOURCE_FRONT => 'globe',
		self::SOURCE_BO    => 'phone',
	];

	public function rules()
	{
		return CMap::mergeArray(
			parent::rules(),
			[
				['createdFrom, createdTo, favoriteLabel', 'safe'],
			]
		);
	}

	/**
	 * @return \CActiveDataProvider
	 */
	public function search()
	{
		$this->resetScope();
		$dataProvider = parent::search();
		$criteria = $dataProvider->getCriteria();

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

		if (!empty($this->control)) {
			$controlFrom = date('Y-m-d 00:00:00', strtotime($this->control));
			$controlTo = date('Y-m-d 23:59:59', strtotime($this->control));
			$criteria->addBetweenCondition('FROM_UNIXTIME(t.control)', $controlFrom, $controlTo);
		}

		if ($this->favoriteLabel == 1) {
			$criteria->with['favorite'] = [
				'joinType' => 'INNER JOIN',
			];
		}

		if($this->status === null) {
			$criteria->addCondition('t.status != ' . LfAppointment::STATUS_REMOVED);
		}

		$criteria->order = 't.id desc';

		$dataProvider->setCriteria($criteria);
		return $dataProvider;
	}

	public function attributeLabels()
	{
		return CMap::mergeArray(
			parent::attributeLabels(),
			[
				'master_name'   => 'Имя мастера',
				'salon_tel'     => 'Телефон салона',
				'createdFrom'   => 'Создание от',
				'createdTo'     => 'Создание до',
				'favoriteLabel' => 'В избранном',
			]
		);
	}

	/**
	 * Возвращает строку из цены от и до
	 *
	 * @return string
	 */
	public function getMergedPrice()
	{
		$text = '';
		if (!empty($this->service_price)) {
			$text .= $this->service_price;
		}

		$different = false;
		if (!empty($this->service_price2) && $this->service_price2 != $this->service_price) {
			$text .= (!empty($text) ? ' / ' : '') . $this->service_price2;
			$different = true;
		}

		if (!empty($text)) {
			$text .= ' руб.';
		}

		if ($different) {
			$text = '<i class="fa fa-bomb" data-toggle="tooltip" title="Цены различаются"></i>' . $text;
		}

		return $text;
	}

	/**
	 * Возвращает список статусов с иконками
	 *
	 * @return array
	 */
	public function getFullStatusList()
	{
		$list = [];
		foreach ($this->statusList as $key => $status) {
			$list[$key] =
				'<i class="fa fa-' .
				self::$statusIcons[$key] .
				'" data-toggle="tooltip" title="' .
				$status .
				'"></i> ' .
				$status;
		}
		return $list;
	}
} 