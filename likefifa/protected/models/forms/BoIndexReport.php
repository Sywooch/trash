<?php


namespace likefifa\models\forms;

use CDbExpression;
use CFormModel;
use LfAppointment;
use LfMaster;
use LfOpinion;
use Yii;

/**
 * Модель для генерирования отчета "Анализ обращений"
 *
 * Class BoIndexReport
 *
 * @package likefifa\models\forms
 */
class BoIndexReport extends CFormModel
{
	/**
	 * Дата выборки
	 *
	 * @var string
	 */
	public $date;

	/**
	 * Дата начала выборки
	 *
	 * @var string
	 */
	private $_startDate;

	/**
	 * Дата конца выборки
	 *
	 * @var string
	 */
	private $_endDate;

	/**
	 * @return array
	 */
	public function rules()
	{
		return [
			['date', 'required'],
			['date', 'dateValidator'],
		];
	}

	/**
	 * @return array
	 */
	public function attributeLabels()
	{
		return [
			'date' => 'Даты отчета',
		];
	}

	public function dateValidator()
	{
		if ($this->date) {
			list($this->_startDate, $this->_endDate) = explode(' - ', $this->date);
			$this->_startDate = date('Y-m-d', strtotime($this->_startDate));
			$this->_endDate = date('Y-m-d', strtotime($this->_endDate));
		}
	}

	/**
	 * Сообщает, что можно генерировать отчеты
	 *
	 * @return bool
	 */
	public function isFill()
	{
		return $this->_startDate != null && $this->_endDate != null;
	}

	/**
	 * Генерирует данные для отчета по заявкам
	 *
	 * @return array
	 */
	public function getAppointmentData()
	{
		// Выбираем количество заявок в разрезе статуса
		$sourceData = Yii::app()->db->createCommand()
			->select(['status', new CDbExpression('COUNT(id) as count')])
			->from(LfAppointment::model()->tableName())
			->where('DATE(created) >= :startDate AND DATE(created) <= :endDate')
			->group('status')
			->bindValues([':startDate' => $this->_startDate, ':endDate' => $this->_endDate])
			->queryAll();

		$model = new LfAppointment();

		$columns1 = [
			[
				'name'   => 'summary',
				'header' => 'Всего',
			],
			[
				'name'   => 'summary_accepted',
				'header' => 'Всего принято',
			],
			[
				'name'   => 'c1',
				'header' => 'Конверсия из обращений в записи',
			],
		];

		$columns2 = [
			[
				'name'   => 'summary',
				'header' => 'Всего',
			],
			[
				'name'   => 's' . $model::STATUS_NEW,
				'header' => $model->statusList[$model::STATUS_NEW],
			],
			[
				'name'   => 's' . $model::STATUS_PROCESSING_BY_MASTER,
				'header' => $model->statusList[$model::STATUS_PROCESSING_BY_MASTER],
			],
			[
				'name'   => 's' . $model::STATUS_REJECTED,
				'header' => $model->statusList[$model::STATUS_REJECTED],
			],
			[
				'name'   => 's' . $model::STATUS_ACCEPTED,
				'header' => $model->statusList[$model::STATUS_ACCEPTED],
			],
			[
				'name'   => 's' . $model::STATUS_REJECTED_AFTER_ACCEPTED,
				'header' => $model->statusList[$model::STATUS_REJECTED_AFTER_ACCEPTED],
			],
			[
				'name'   => 's' . $model::STATUS_COMPLETED,
				'header' => $model->statusList[$model::STATUS_COMPLETED],
			],
			[
				'name'   => 's' . $model::STATUS_REMOVED,
				'header' => $model->statusList[$model::STATUS_REMOVED],
			],
		];

		$columns3 = [
			[
				'name'   => 'summary_accepted',
				'header' => 'Всего принято',
			],
			[
				'name'   => 's' . $model::STATUS_COMPLETED,
				'header' => $model->statusList[$model::STATUS_COMPLETED],
			],
			[
				'name'   => 'c2',
				'header' => 'Конверсия из записи в оплаченные',
			]
		];

		$data = [];
		$summary = 0;
		$summaryAccepted = 0;
		foreach ($model->statusList as $status => $statusValue) {
			$data['s' . $status] = 0;
			foreach ($sourceData as $d) {
				if ($d['status'] == $status) {
					$data['s' . $status] = $d['count'];
					$summary += $d['count'];

					if ($status == LfAppointment::STATUS_ACCEPTED ||
						$status == LfAppointment::STATUS_REJECTED_AFTER_ACCEPTED ||
						$status == LfAppointment::STATUS_COMPLETED
					) {
						$summaryAccepted += $d['count'];
					}

					continue 2;
				}
			}
		}

		// Дополнительные колонки
		$data['summary'] = $summary;
		$data['summary_accepted'] = $summaryAccepted;
		if ($data['summary'] > 0) {
			$data['c1'] = round($data['summary_accepted'] / $data['summary'] * 100, 2) . '%';
		} else {
			$data['c1'] = 0 . '%';
		}
		if ($data['summary_accepted'] > 0) {
			$data['c2'] =
				round($data['s' . LfAppointment::STATUS_COMPLETED] / $data['summary_accepted'] * 100, 2) . '%';
		} else {
			$data['c2'] = 0 . '%';
		}

		return [
			'data'     => [$data],
			'columns1' => $columns1,
			'columns2' => $columns2,
			'columns3' => $columns3,
		];
	}

	/**
	 * Генерирует данные для отчета по мастерам
	 *
	 * @return array
	 */
	public function getMastersData()
	{
		$data = Yii::app()->db->createCommand()
			->select(
				[
					new CDbExpression('COUNT(id) AS count'),
					new CDbExpression('SUM(is_published) AS active'),
					new CDbExpression('SUM(is_blocked) AS blocked'),
				]
			)
			->from(LfMaster::model()->tableName())
			->where('DATE(created) >= :startDate AND DATE(created) <= :endDate')
			->bindValues([':startDate' => $this->_startDate, ':endDate' => $this->_endDate])
			->queryRow();

		$columns = [
			[
				'name'   => 'total',
				'header' => 'Всего',
			],
			[
				'name'   => 'active',
				'header' => 'Активных',
			],
			[
				'name'   => 'blocked',
				'header' => 'Неактивных',
			],
			[
				'name'   => 'conversion',
				'header' => 'Конверсия',
			],
		];

		$data = [
			[
				'total'      => $data['count'],
				'active'     => $data['active'],
				'blocked'    => $data['blocked'] + ($data['count'] - $data['active']),
				'conversion' => ($data['count'] > 0 ? ($data['active'] / $data['count'] * 100) : 0) . '%',
			]
		];

		return compact('data', 'columns');
	}

	/**
	 * Генерирует данные для отчета по отзывам
	 *
	 * @return array
	 */
	public function getOpinionsData()
	{
		$allCount = Yii::app()->db->createCommand()
			->select([new CDbExpression('COUNT(id)')])
			->from(LfOpinion::model()->tableName())
			->where('DATE(FROM_UNIXTIME(created)) >= :startDate AND DATE(FROM_UNIXTIME(created)) <= :endDate')
			->bindValues([':startDate' => $this->_startDate, ':endDate' => $this->_endDate])
			->queryScalar();
		$acceptedCount = Yii::app()->db->createCommand()
			->select([new CDbExpression('COUNT(id)')])
			->from(LfOpinion::model()->tableName())
			->where(
				'DATE(FROM_UNIXTIME(created)) >= :startDate AND DATE(FROM_UNIXTIME(created)) <= :endDate AND allowed = 1'
			)
			->bindValues([':startDate' => $this->_startDate, ':endDate' => $this->_endDate])
			->queryScalar();
		$warningCount = Yii::app()->db->createCommand()
			->select([new CDbExpression('COUNT(id)')])
			->from(LfOpinion::model()->tableName())
			->where(
				'DATE(FROM_UNIXTIME(created)) >= :startDate AND DATE(FROM_UNIXTIME(created)) <= :endDate AND warning_level > 0'
			)
			->bindValues([':startDate' => $this->_startDate, ':endDate' => $this->_endDate])
			->queryScalar();

		$columns = [
			[
				'name'   => 'total',
				'header' => 'Всего'
			],
			[
				'name'   => 'accepted',
				'header' => 'Принятых'
			],
			[
				'name'   => 'warning',
				'header' => 'Подозрительных'
			],
		];
		$data = [
			[
				'total'    => $allCount,
				'accepted' => $acceptedCount,
				'warning'  => $warningCount,
			]
		];

		return [
			'data'    => $data,
			'columns' => $columns,
		];
	}
} 