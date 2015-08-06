<?php

namespace likefifa\models\forms;

use CDbExpression;
use CHtml;
use LfAppointment;
use LfMaster;
use Cmap;
use CActiveDataProvider;
use Racecore\GATracking\Exception;
use Yii;

/**
 * Class LfMasterAdminFilter
 * Вспомогательная модель для фильтрации мастеров в админке
 *
 * @package likefifa\models\forms
 */
class LfMasterAdminFilter extends LfMaster
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
	 * Специализация для фильтрации
	 *
	 * @var integer
	 */
	public $specialization_id;

	/**
	 * Услуга для фильтрации
	 *
	 * @var integer
	 */
	public $service_id;

	/**
	 * Принадлежность к салону, для фильтрации
	 *
	 * @var integer
	 */
	public $is_salon = null;

	/**
	 * Текущий статус
	 *
	 * @var integer
	 */
	public $status = null;

	public static $statusList = [
		1 => 'активен',
		0 => 'не активен',
		2 => 'блок'
	];

	/**
	 * Название специализации
	 *
	 * @var string
	 */
	public $group_name;

	public function rules()
	{
		return CMap::mergeArray(
			parent::rules(),
			[
				['createdFrom, createdTo, group_name, status, specialization_id, service_id, is_salon', 'safe'],
			]
		);
	}

	public function attributeLabels()
	{
		return CMap::mergeArray(
			parent::attributeLabels(),
			[
				'createdFrom' => 'Создание от',
				'createdTo'   => 'Создание до',
			]
		);
	}

	/**
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$dataProvider = parent::search();

		if ($this->status !== null && $this->status !== '') {
			if ($this->status == 0 || $this->status == 1) {
				$dataProvider->getCriteria()->compare('t.is_published', $this->status);
			} else {
				if ($this->status == 2) {
					$dataProvider->getCriteria()->compare('t.is_blocked', 1);
				}
			}
		}

		if ($this->phone_cell) {
			$this->phone_cell = preg_replace('/[^0-9]/', '', $this->phone_cell);
			$dataProvider->getCriteria()->addCondition(
				't.phone_numeric like :phone_cell OR t.phone_cell like :phone_cell'
			);
			$dataProvider->getCriteria()->params[':phone_cell'] = '%' . addcslashes($this->phone_cell, '%_') . '%';
		}

		if (!empty($this->group_name)) {
			$dataProvider->getCriteria()->compare('group.name', $this->group_name, true);
		}

		if (!empty($this->createdFrom) || !empty($this->createdTo)) {
			$createdFrom = $createdTo = null;
			if (!empty($this->createdFrom)) {
				$createdFrom = date('Y-m-d 00:00:00', strtotime($this->createdFrom));
			}
			if (!empty($this->createdTo)) {
				$createdTo = date('Y-m-d 23:59:59', strtotime($this->createdTo));
			}

			$dataProvider->getCriteria()->addBetweenCondition('t.created', $createdFrom, $createdTo);
		}

		if ($this->specialization_id != null) {
			$dataProvider->criteria->with['prices'] = [
				'joinType' => 'INNER JOIN',
				'together' => true,
				'with'     => [
					'specialization' => [
						'joinType' => 'INNER JOIN',
						'together' => true,
					],
				]
			];
			$dataProvider->criteria->compare('specialization.id', $this->specialization_id);
		}

		if ($this->service_id != null) {
			if (!isset($dataProvider->criteria->with['prices'])) {
				$dataProvider->criteria->with['prices'] = [
					'joinType' => 'INNER JOIN',
					'together' => true,
				];
			}
			$dataProvider->criteria->compare('prices.service_id', $this->service_id);
		}

		if ($this->is_salon) {
			$dataProvider->criteria->addCondition('t.salon_id IS NOT NULL');
		} elseif($this->is_salon === '0') {
			$dataProvider->criteria->addCondition('t.salon_id IS NULL');
		}

		return $dataProvider;
	}

	/**
	 * Возвращает комментарий, отформатированный для html отображения
	 *
	 * @return mixed
	 */
	public function getFormattedComment()
	{
		return str_replace("\r\n", '<br/>', $this->comment);
	}

	/**
	 * Получает статус публикации мастера
	 *
	 * @return string
	 */
	public function getStatus()
	{
		if ($this->is_blocked) {
			return '<span class="label label-danger">блок</span>';
		} else {
			if (!$this->is_published) {
				return '<span class="label label-default">не активен</div>';
			} else {
				return '<span class="label label-success">активен</span>';
			}
		}
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
	 * Возвращает список статусов заявок мастера с их количеством
	 *
	 * @return string
	 */
	public function getAppointmentCountByTypes()
	{
		$model = new LfAppointment();
		$data = Yii::app()->db->createCommand()
			->select(['status', new CDbExpression('COUNT(id) cnt')])
			->from($model->tableName())
			->where('master_id = :master_id', [':master_id' => $this->id])
			->group('status')
			->order('status')
			->queryAll();
		$html = '<ul class="list-unstyled">';
		foreach ($data as $status) {
			$html .= '<li><strong>' . $model->statusList[$status['status']] . '</strong>: ' . $status['cnt'] . '</li>';
		}
		$html .= '</ul>';

		return $html;
	}
} 