<?php

namespace dfs\docdoc\back\controllers;

use dfs\docdoc\models\DoctorClinicModel;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\RatingModel;
use dfs\docdoc\models\RatingStrategyModel;

/**
 * Контроллер рейтингов
 */
class RatingController extends BackendController
{
	public $breadcrumbs = [
		'Рейтинги врачей' => "/2.0/rating",
		"Стратегии" => "/2.0/ratingStrategy/index"
	];

	/**
	 * Рейтинги по врачам
	 *
	 * @return void
	 */
	public function actionDoctor()
	{
		// Загрузка параметров для расчета рейтинга
		if(isset(\Yii::app()->session['doctorRatings'])){
			$message = \Yii::app()->session['doctorRatings'];
			\Yii::app()->session->remove('doctorRatings');
		} else {
			$message = '';
		}

		if (\Yii::app()->request->isPostRequest && isset($_FILES['ratingFile'])) {
			try {
				$this->saveDoctorFile($_FILES['ratingFile']);
				$message = 'Данные успешно загружены!';
			} catch (\CException $e) {
				$message = $e->getMessage();
			}
		}

		$dataProvider = new \CActiveDataProvider(
			DoctorClinicModel::class,
			[
				'criteria' => [
					'condition' => 'type = :type',
					'params' => [':type' => DoctorClinicModel::TYPE_DOCTOR],
					'with' => [
						'doctor',
						'ratings',
						'clinic'
					]
				],
				'sort' => [
					'defaultOrder' => 'doctor.conversion DESC',
				],
				'pagination' => [
					'pageSize' => 100,
				]
			]
		);

		$columns = [
			[
				'name' => 'id',
				'value' => '$data->doctor->id'
			],
			[
				'name' => 'name',
				'value' => '$data->doctor->name',
			],
			[
				'name' => 'conversion',
				'value' => '$data->doctor->conversion',
			],
			[
				'name' => 'clinic',
				'value' => '$data->clinic->name',
			]
		];

		$columns = array_merge($columns, $this->getDoctorRatingColumns());

		$this->render("doctor", [
			'dataProvider'  => $dataProvider,
			'columns'       => $columns,
			'message'       => $message,
		]);
	}

	/**
	 * Рейтинги по клиникам
	 *
	 * @return void
	 */
	public function actionClinic()
	{
		if(isset(\Yii::app()->session['doctorRatings'])){
			$message = \Yii::app()->session['doctorRatings'];
			\Yii::app()->session->remove('doctorRatings');
		} else {
			$message = '';
		}

		if (\Yii::app()->request->isPostRequest && isset($_FILES['ratingFile'])) {
			try {
				$this->saveClinicFile($_FILES['ratingFile']);
				$message = 'Данные успешно загружены!';
			} catch (\CException $e) {
				$message = $e->getMessage();
			}
		}

		$dataProvider = new \CActiveDataProvider(
			ClinicModel::class,
			[
				'criteria'   => [
					'with'     => [
						'ratings' => [
							'joinType' => 'inner join'
						]
					],
				],
				'sort'       => [
					'defaultOrder' => 'conversion DESC, name',
				],
				'pagination' => [
					'pageSize' => 100,
				],
			]
		);

		$columns = [
			'id',
			'name',
			'conversion',
			'hand_factor',
			'admission_cost',
		];

		$columns = array_merge($columns, $this->getRatingColumns());

		$this->render("clinic", [
			'dataProvider' => $dataProvider,
			'columns' => $columns,
			'message' => $message,
		]);
	}

	/**
	 * Сохранение параметров рейтинга врачей
	 *
	 * @param array $file
	 *
	 * @throws \CException
	 */
	private function saveDoctorFile($file)
	{
		$this->validateUploadedFile($file);

		if ($data = RatingModel::model()->parseCSV(RatingModel::TYPE_DOCTOR, $file['tmp_name'])){
			RatingModel::model()->updateDoctorRatingFromFile($data);
		}
	}

	/**
	 * Сохранение параметров рейтинга клиник
	 *
	 * @param array $file
	 *
	 * @throws \CException
	 */
	private function saveClinicFile($file)
	{

		$this->validateUploadedFile($file);

		if ($data = RatingModel::model()->parseCSV(RatingModel::TYPE_CLINIC, $file['tmp_name'])){
			RatingModel::model()->updateClinicRatingFromFile($data);
		}
	}

	/**
	 * Валидация загружаемого файла
	 *
	 * @param array $file
	 * @throws \CException
	 * @return bool
	 */
	protected function validateUploadedFile($file)
	{
		if(!isset($file['error']) || is_array($file['error'])){
			throw new \CException('Ошибка загрузки данных!');
		}

		$info = pathinfo($file['name']);

		if (!isset($info['extension']) || $info['extension'] != 'csv') {
			throw new \CException('Неверное расширение файла');
		}

		return true;
	}

	/**
	 * Получение колонок с рейтингами
	 *
	 * @return array
	 */
	private function getRatingColumns()
	{
		$ratingStrategies = RatingStrategyModel::model()->findAll();
		$ratings = [];

		foreach ($ratingStrategies as $item) {
			$ratings[] = [
				'header' => "Рейтинг {$item->name}",
				'name'   => "ratings_{$item->name}",
				'value'  => 'isset($data->getRatings()[' . $item->id . ']) ? $data->getRatings()[' . $item->id . '] : 0',
				'sortable' => true,
			];
		}

		return $ratings;
	}

	/**
	 * Получение колонок с рейтингами
	 *
	 * @return array
	 */
	private function getDoctorRatingColumns()
	{
		$ratingStrategies = RatingStrategyModel::model()->findAll();
		$ratings = [];

		foreach ($ratingStrategies as $item) {
			$ratings[] = [
				'name'   => "Рейтинг {$item->name}",
				'value'  => 'isset($data->getRatings()[' . $item->id . ']) ? $data->getRatings()[' . $item->id . '] : 0',
			];
		}

		return $ratings;
	}

	/**
	 * Пересчитать рейтинги
	 *
	 * @throws \CException
	 * @throws \CHttpException
	 */
	public function actionRecalculate()
	{
		$type = \Yii::app()->request->getQuery('type');
		$strategyId = \Yii::app()->request->getQuery('strategyId');

		if(!$type || !in_array($type, ['doctor', 'clinic'])){
			throw new \CHttpException(400, 'BadRequest');
		}

		$paramType = RatingModel::TYPE_DOCTOR;
		$type == 'clinic' && $paramType = RatingModel::TYPE_CLINIC;

		set_time_limit(600);//ставлю 10 мин на всякий случай.

		if (!empty($strategyId)) {
			$strategies[] = RatingStrategyModel::model()->findByPk($strategyId);
		} else {
			$strategies[] = RatingStrategyModel::model()->findAll();
		}

		try{
			foreach ($strategies as $strategy) {
				$strategy->updateRating($paramType);
			}
			$message = 'Рейтинги успешно пересчитаны';
		} catch (\Exception $e){
			$message = $e->getMessage();
		}

		\Yii::app()->session['doctorRatings'] = $message;

		$this->redirect('/2.0/rating/' . $type);
	}
}
