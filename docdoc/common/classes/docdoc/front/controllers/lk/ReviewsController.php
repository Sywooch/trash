<?php

namespace dfs\docdoc\front\controllers\lk;

use dfs\docdoc\models\DoctorOpinionModel;


/**
 * Class ReviewsController
 *
 * @package dfs\docdoc\front\controllers\lk
 */
class ReviewsController extends FrontController
{
	/**
	 * Дефолтные параметры для поиска
	 *
	 * @var array
	 */
	protected $_findParams = [];

	protected $_fields = [
		'created' => [
			'sort' => 'created_',
			'label' => 'Дата отзыва',
			'width' => 80,
		],
		'doctor_name' => [
			'label' => 'Имя врача',
			'width' => 200,
		],
		'specialty' => [
			'label' => 'Специальность',
			'width' => 200,
		],
		'evaluation' => [
			'orderable' => false,
			'label' => 'Оценки',
			'width' => 100,
		],
		'comment' => [
			'orderable' => false,
			'label' => 'Комментарий',
			'width' => 400,
		],
	];

	protected $_columns = [ 'created', 'doctor_name', 'specialty', 'evaluation', 'comment' ];


	/**
	 * Страница отзывов клиники
	 */
	public function actionIndex()
	{
		$reviews = DoctorOpinionModel::model()
			->allowed()
			->byClinic($this->_clinic->id)
			->findAll();

		$data = [];

		foreach ($reviews as $review) {
			$data[] = $this->buildDataTableReviewData($review);
		}

		$request = \Yii::app()->request;

		if ($request->isAjaxRequest) {
			$this->renderJSON([
				'data' => $data,
			]);
		} else {
			$vars = [
				'tableConfig' => [
					'url' => '/lk/reviews',
					'fields' => $this->_fields,
					'columns' => $this->_columns,
					'dtDom' => 'lfrtip',
				]
			];

			$this->render('index', $vars);
		}
	}


	/**
	 * Данные доктора отправляемые в datatables
	 *
	 * @param DoctorOpinionModel $review
	 *
	 * @return array
	 */
	protected function buildDataTableReviewData($review)
	{
		$doctor = $review->doctor;
		$created = strtotime($review->created);
		$specialty = [];

		if ($doctor && $doctor->sectors) {
			foreach ($doctor->sectors as $sector) {
				$specialty[] = $sector->name;
			}
		}

		return [
			'id'          => $review->id,
			'created'     => $review->created ? date('d.m.Y H:i', $created) : '',
			'created_'    => date('Y.m.d H:i', $created),
			'doctor_name' => $doctor ? $doctor->name : '',
			'specialty'   => implode(', ', $specialty),
			'evaluation'  =>
				"Врач: {$review->rating_qualification}<br />" .
				"Внимание: {$review->rating_attention}<br />" .
				"Цена / качество: {$review->rating_room}",
			'comment'     => $review->text,
		];
	}
}
