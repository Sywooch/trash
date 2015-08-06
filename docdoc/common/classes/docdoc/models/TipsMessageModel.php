<?php

namespace dfs\docdoc\models;

use dfs\docdoc\extensions\TextUtils;


/**
 * This is the model class for table "tips_message".
 *
 * The followings are the available columns in table 'tips_message':
 *
 * @property int $tips_id
 * @property int $record_id
 * @property int $weight
 * @property array $values
 *
 * @property TipsModel tips
 *
 * @method TipsMessageModel[] findAll
 * @method TipsMessageModel find
 */
class TipsMessageModel extends \CActiveRecord
{
	const TIME_DAY = 86400;

	/**
	 * Активна или нет подсказка
	 *
	 * @var bool
	 */
	protected $_isActive = false;

	/**
	 * Значения для формирования подсказок
	 *
	 * @var array
	 */
	public $values = [];


	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className
	 *
	 * @return TipsMessageModel the static model class
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
		return 'tips_message';
	}

	/**
	 * @return mixed имя первичного ключа
	 */
	public function primaryKey()
	{
		return [ 'record_id', 'tips_id' ];
	}

	/**
	 * Отношения
	 *
	 * @return array
	 */
	public function relations()
	{
		return [
			'tips' => [ self::BELONGS_TO, TipsModel::class, 'tips_id' ],
		];
	}

	/**
	 * Действия после выборки
	 */
	protected function afterFind()
	{
		$this->values = json_decode($this->values, true);

		return parent::afterFind();
	}

	/**
	 * Действия перед сохранением
	 *
	 * @return bool
	 */
	protected function beforeSave()
	{
		$this->values = json_encode($this->values);

		return parent::beforeSave();
	}

	/**
	 * Действия после сохранением
	 */
	protected function afterSave()
	{
		$this->values = json_decode($this->values, true);

		return parent::afterSave();
	}

	/**
	 * Получить значение параметра
	 *
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function getValue($name)
	{
		return isset($this->values[$name]) ? $this->values[$name] : null;
	}

	/**
	 * Установить значение параметра
	 *
	 * @param string $name
	 * @param mixed $value
	 *
	 * @return $this
	 */
	public function setValue($name, $value)
	{
		if (!is_array($this->values)) {
			$this->values = [];
		}

		$this->values[$name] = $value;

		return $this;
	}

	public function getMessage()
	{
		$method = 'textTips' . $this->tips->name;

		if (!method_exists($this, $method)) {
			return null;
		}

		return $this->{$method}();
	}

	/**
	 * Поиск по id записи
	 *
	 * @param int | array $ids
	 *
	 * @return $this
	 */
	public function byRecord($ids)
	{
		$this->getDbCriteria()->addInCondition($this->getTableAlias() . '.record_id', is_array($ids) ? $ids : [ $ids ]);

		return $this;
	}

	/**
	 * Получение рандомной подсказки для записи используя веса
	 *
	 * @param int $recordId
	 *
	 * @return TipsMessageModel | null
	 */
	public function findRandomForRecord($recordId)
	{
		return $this
			->byRecord($recordId)
			->find([
				'condition' => 't.weight >= ' . $this->getUserRandNumber(),
				'order' => 't.weight ASC',
				'limit' => 1,
			]);
	}

	/**
	 * Получение рандомной подсказки для записи используя веса
	 *
	 * @param array $recordIds
	 *
	 * @return TipsMessageModel[]
	 */
	public function findRandomForRecords($recordIds)
	{
		$result = [];
		$r = $this->getUserRandNumber();

		$items = $this
			->byRecord($recordIds)
			->with('tips')
			->findAll([ 'order' => 't.weight ASC' ]);

		foreach ($items as $item) {
			if ($item->weight >= $r) {
				$rid = $item->record_id;
				if (!isset($result[$rid]) || $result[$rid]->weight > $item->weight) {
					$result[$rid] = $item;
				}
			}
		}

		return $result;
	}

	/**
	 * Получаем число от 1 до 100, которое не меняется для каждого пользователя
	 *
	 * @return int
	 */
	public function getUserRandNumber()
	{
		$request = \Yii::app()->request;

		if (isset($request->cookies['rand'])) {
			return abs(intval($request->cookies['rand']->value)) % 100;
		}

		$r = rand(1, 100);
		if (php_sapi_name() !== 'cli') {
			$request->cookies['rand'] = new \CHttpCookie('rand', $r, ['expire' => time() + 2592000]);
		}

		return $r;
	}

	/**
	 * Активна посказка или нет
	 *
	 * @return bool
	 */
	public function isActive()
	{
		return $this->_isActive;
	}

	/**
	 * Пересчёт значения подсказки
	 *
	 * @param TipsModel      $tip
	 * @param \CActiveRecord $record
	 *
	 * @return bool
	 */
	public function recalculate(TipsModel $tip, \CActiveRecord $record)
	{
		$this->tips_id = $tip->id;
		$this->record_id = $record->id;
		$this->_isActive = false;

		$method = 'tips' . $tip->name;

		if (method_exists($this, $method)) {
			$this->_isActive = $this->{$method}($record);
		}

		return $this->_isActive;
	}

	/**
	 * Подсказки:
	 *    "Последняя запись к врачу была 2 часа назад" (от 1 часа до 6 часов)
	 *    "Вчера к этому специалисту записались 35 человек"
	 *    "На прошлой неделе записалось 25 человек"
	 *    "Принял 156 пациентов за последний месяц"
	 *    "Всего записалось 333 человек" (только 3-значное число влезает в 1 строку)
	 *    "Всего было 11555 записей"
	 *
	 * @param DoctorModel $doctor
	 *
	 * @return bool
	 */
	protected function tipsRequestsInfo(DoctorModel $doctor)
	{
		$lastRequest = RequestModel::model()
			->byDoctor($doctor->id)
			->find(['order' => 't.req_id DESC']);

		$lastCreated = $lastRequest ? intval($lastRequest->req_created) : 0;

		$today = mktime(0, 0, 0);
		$weekBegin = $today - (date('N') - 1) * self::TIME_DAY;

		$countYesterday = (int) RequestModel::model()
			->byDoctor($doctor->id)
			->createdInInterval($today - self::TIME_DAY, $today)
			->count();

		$countLastWeek = (int) RequestModel::model()
			->byDoctor($doctor->id)
			->createdInInterval($weekBegin - 7 * self::TIME_DAY, $weekBegin)
			->count();

		$countMonth = (int) RequestModel::model()
			->byDoctor($doctor->id)
			->createdInInterval(strtotime('-1 month'))
			->count();

		$countAll = (int) RequestModel::model()
			->byDoctor($doctor->id)
			->count();

		$this->setValue('LastCreated', $lastCreated);
		$this->setValue('CountYesterday', $countYesterday);
		$this->setValue('CountLastWeek', $countLastWeek);
		$this->setValue('CountMonth', $countMonth);
		$this->setValue('CountAll', $countAll);
		$this->setValue('Sex', $doctor->sex);

		return (time() - $lastCreated) < self::TIME_DAY || $countYesterday > 1 || $countLastWeek > 1 || $countMonth > 10 || $countAll > 10;
	}

	/**
	 * Текстовки:
	 *    "Последняя запись к врачу была 2 часа назад" (от 1 часа до 6 часов)
	 *    "Вчера к этому специалисту записались 35 человек"
	 *    "На прошлой неделе записалось 25 человек"
	 *    "Принял 156 пациентов за последний месяц"
	 *    "Всего записалось 333 человек" (только 3-значное число влезает в 1 строку)
	 *    "Всего было 11555 записей"
	 *
	 * @return string
	 */
	protected function textTipsRequestsInfo()
	{
		$lastCreated = intval($this->getValue('LastCreated'));
		if ($lastCreated) {
			$hours = intval((time() - $lastCreated) / 3600);
			if ($hours < 1) {
				$hours = 1;
			}
			if ($hours <= 6) {
				return 'Последняя запись к этому врачу была ' . TextUtils::caseForNumber($hours, [
					"{$hours} час назад",
					"{$hours} часа назад",
					"{$hours} часов назад",
				]);
			}
		}

		$today = mktime(0, 0, 0);

		$countYesterday = intval($this->getValue('CountYesterday'));
		if ($countYesterday > 1 && $lastCreated > ($today - 2 * self::TIME_DAY)) {
			return 'Вчера к этому специалисту ' . TextUtils::caseForNumber($countYesterday, [
				"записался {$countYesterday} человек",
				"записалось {$countYesterday} человека",
				"записалось {$countYesterday} человек",
			]);
		}

		$countLastWeek = intval($this->getValue('CountLastWeek'));
		if ($countLastWeek > 1 && $lastCreated >= ($today - (date('N') + 6) * self::TIME_DAY)) {
			return 'На прошлой неделе ' . TextUtils::caseForNumber($countLastWeek, [
				"записался {$countLastWeek} человек",
				"записалось {$countLastWeek} человека",
				"записалось {$countLastWeek} человек",
			]);
		}

		$countMonth = intval($this->getValue('CountMonth'));
		if ($countMonth > 10 && $lastCreated >= strtotime('-1 month')) {
			return 'Принял' . ($this->getValue('Sex') == 2 ? 'а' : '') . ' ' . TextUtils::caseForNumber($countMonth, [
				"{$countMonth} пациента",
				"{$countMonth} пациента",
				"{$countMonth} пациентов",
			]) . ' за последний месяц';
		}

		$countAll = intval($this->getValue('CountAll'));
		if ($countAll > 1000) {
			return 'Всего было ' . TextUtils::caseForNumber($countAll, [
				"{$countAll} запись",
				"{$countAll} записи",
				"{$countAll} записей",
			]);
		}
		if ($countAll > 10) {
			return 'Всего ' . TextUtils::caseForNumber($countAll, [
				"записался {$countAll} человек",
				"записалось {$countAll} человека",
				"записалось {$countAll} человек",
			]);
		}

		return null;
	}


	/**
	 * Подсказка "Только положительные отзывы!"
	 *
	 * @param DoctorModel $doctor
	 *
	 * @return bool
	 */
	protected function tipsPositiveOpinion(DoctorModel $doctor)
	{
		$countAll = DoctorOpinionModel::model()
			->byDoctor($doctor->id)
			->allowed()
			->count();

		if ($countAll > 1) {
			$countNegative = DoctorOpinionModel::model()
				->byDoctor($doctor->id)
				->allowed()
				->inRatingColor([ DoctorOpinionModel::RATING_COLOR_NEGATIVE, DoctorOpinionModel::RATING_COLOR_NEUTRAL ])
				->count();

			if ($countNegative < 1) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Текстовка для подсказки "Только положительные отзывы!"
	 *
	 * @return string
	 */
	protected function textTipsPositiveOpinion()
	{
		return 'Только положительные отзывы!';
	}

	/**
	 * Подсказки:
	 *    "Последний отзыв был опубликован вчера"
	 *    "На этой неделе опубликовано 5 отзывов об этом враче"
	 *    "За последний месяц опубликовано 33 отзыва"
	 *
	 * @param DoctorModel $doctor
	 *
	 * @return bool
	 */
	protected function tipsOpinionInfo(DoctorModel $doctor)
	{
		$lastOpinion = DoctorOpinionModel::model()
			->byDoctor($doctor->id)
			->allowed()
			->find(['order' => 't.id DESC']);

		$lastCreated = $lastOpinion ? strtotime($lastOpinion->created) : 0;

		$today = mktime(0, 0, 0);
		$weekBegin = $today - (date('N') - 1) * self::TIME_DAY;

		$countWeek = (int) DoctorOpinionModel::model()
			->byDoctor($doctor->id)
			->allowed()
			->createdInInterval(date('c', $weekBegin))
			->count();

		$countMonth = (int) DoctorOpinionModel::model()
			->byDoctor($doctor->id)
			->allowed()
			->createdInInterval(date('c', strtotime('-1 month')))
			->count();

		$this->setValue('LastCreated', $lastCreated);
		$this->setValue('CountWeek', $countWeek);
		$this->setValue('CountMonth', $countMonth);

		return $lastCreated > ($today - self::TIME_DAY) || $countWeek > 1 || $countMonth > 1;
	}

	/**
	 * Текстовки:
	 *    "Последний отзыв был опубликован вчера"
	 *    "На этой неделе опубликовано 5 отзывов об этом враче"
	 *    "За последний месяц опубликовано 33 отзыва"
	 *
	 * @return string
	 */
	protected function textTipsOpinionInfo()
	{
		$today = mktime(0, 0, 0);

		$lastCreated = intval($this->getValue('LastCreated'));
		if ($lastCreated) {
			if ($lastCreated > $today) {
				return 'Последний отзыв был опубликован сегодня';
			}
			if ($lastCreated > ($today - self::TIME_DAY)) {
				return 'Последний отзыв был опубликован вчера';
			}
		}

		$countWeek = intval($this->getValue('CountWeek'));
		if ($countWeek > 1 && $lastCreated >= ($today - (date('N') - 1) * self::TIME_DAY)) {
			return 'На этой неделе ' . TextUtils::caseForNumber($countWeek, [
				"опубликован {$countWeek} отзыв",
				"опубликовано {$countWeek} отзыва",
				"опубликовано {$countWeek} отзывов",
			]) . ' об этом враче';
		}

		$countMonth = intval($this->getValue('CountMonth'));
		if ($countMonth > 1 && $lastCreated >= strtotime('-1 month')) {
			return 'За последний месяц ' . TextUtils::caseForNumber($countMonth, [
				"опубликован {$countMonth} отзыв",
				"опубликовано {$countMonth} отзыва",
				"опубликовано {$countMonth} отзывов",
			]);
		}

		return null;
	}


	/**
	 * Подсказка "Входит в сотню лучших врачей"
	 *
	 * @param DoctorModel $doctor
	 *
	 * @return bool
	 */
	protected function tipsTheBest(DoctorModel $doctor)
	{
		static $top = null;

		if ($top === null) {
			$doctors = DoctorModel::model()->findAll([
					'order' => 't.rating_internal DESC',
					'limit' => 100,
				]);

			$top[] = [];
			foreach ($doctors as $d) {
				$top[$d->id] = true;
			}
		}

		return isset($top[$doctor->id]);
	}

	/**
	 * Текстовка для подсказки "Входит в сотню лучших врачей"
	 *
	 * @return string
	 */
	protected function textTipsTheBest()
	{
		return 'Входит в сотню лучших врачей';
	}
}
