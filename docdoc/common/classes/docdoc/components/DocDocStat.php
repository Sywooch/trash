<?php
namespace dfs\docdoc\components;

use dfs\docdoc\models\DoctorOpinionModel;
use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\DoctorModel;
use dfs\docdoc\models\PartnerModel;

/**
 * Class DocdocStat
 *
 * Статистика Докдока
 *
 */
class DocDocStat
{

	private $_factor = 1;

	/**
	 * Количество отзывов до новой логики расчета
	 */
	const REVIEWS_BEFORE_NEW_CALC = 21600;

	/**
	 * Дата с которой кол-во отзывов рассчитываются по новой логике
	 */
	const DATE_NEW_REVIEWS_CALC = '2014-11-01';

	/**
	 * создание объекта
	 *
	 * @param int $factor
	 */
	public function __construct($factor)
	{
		$this->_factor = $factor;
	}

	/**
	 * Возвращает количество заявок, поданных за последние сутки
	 *
	 * @param bool $isLastDay выбирать ли заявки за последние сутки
	 *
	 * @return int
	 */
	public function getRequestsCount($isLastDay = true)
	{
		$model = new RequestModel;

		if ($isLastDay) {
			$model->cache(60)->createdInInterval(time() - 3600 * 24);
		} else {
			$model->cache(3600);
		}

		return intval($model->count() * $this->_factor);
	}

	/**
	 * Возвращает количество докторов в системе
	 *
	 * @return int
	 */
	public function getDoctorsCount()
	{
		$doc_count = DoctorModel::model()
			->cache(3600)
			->count();

		return intval($doc_count * $this->_factor);
	}

	/**
	 * Возвращает количество отзывов в системе
	 *
	 * @return int
	 */
	public function getReviewsCount()
	{
		$review_count = DoctorOpinionModel::model()
			->allowed()
			->createdInInterval(self::DATE_NEW_REVIEWS_CALC)
			->cache(3600)
			->count();

		return self::REVIEWS_BEFORE_NEW_CALC + $review_count;
	}

	/**
	 * Возвращает количество партнеров
	 *
	 * @return integer
	 */
	public function getPartnersCount()
	{
		return intval(PartnerModel::model()->cache(3600)->count() * $this->_factor);
	}
}