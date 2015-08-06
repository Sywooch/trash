<?php

/**
 * Виджет отзывов
 *
 */
class LfOpinionsWidget extends CWidget
{

	/**
	 * Модель мастера или салона
	 *
	 * @var LfMaster | LfSalon
	 */
	public $model = null;

	/**
	 * Модель Отзыва
	 *
	 * @var LfOpinion
	 */
	public $opinion = null;

	/**
	 * Куда (к мастеру / в салон)
	 *
	 * @var string
	 */
	public $where = "";

	/**
	 * Показать ли все отзывы. -1 - показать все, >-1 - показать это количество
	 *
	 * @var bool
	 */
	public $limit = -1;

	public function run()
	{
		$showAll = Yii::app()->request->getQuery('allOpinions', false);
		if($showAll != false) {
			$this->limit = -1;
		}

		$this->opinion = new LfOpinion;

		$this->render('LfOpinionsWidget');
	}
}