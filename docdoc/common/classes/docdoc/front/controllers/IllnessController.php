<?php

namespace dfs\docdoc\front\controllers;

use dfs\docdoc\models\IllnessModel;
use dfs\docdoc\models\DoctorModel;
use dfs\docdoc\models\SectorModel;

class IllnessController extends FrontController
{

	/**
	 * Страница со списком заболеваний
	 */
	public function actionIndex()
	{
		$illnesses = IllnessModel::model()
			->active()
			->findAll(['order' => 't.name']);

		$this->initXslTemplates();

		$this->render('index', [
			'illnesses' => $illnesses,
			'specialities' => SectorModel::getItemsByCity(\Yii::app()->city->getCityId()),
		]);
	}

	/**
	 * Страница заболевания
	 *
	 * @param string $alias
	 *
	 * @throws \CHttpException
	 */
	public function actionShow($alias)
	{
		$specAlias = \Yii::app()->request->getQuery('spec');

		$illnessModel = IllnessModel::model()->active();

		if (is_numeric($alias)) {
			$illness = $illnessModel->findByPk($alias);
			if ($illness) {
				$this->redirect('/illness/' . $illness->rewrite_name);
			}
		} else {
			$illness = $illnessModel->byRewriteName($alias)->find();
		}

		if (!$illness) {
			throw new \CHttpException(404, 'Заболевание не найдено');
		}

		if ($specAlias && $illness) {
			if ($illness->sector && $illness->sector->rewrite_name === $specAlias) {
				$this->redirect('/illness/' . $illness->rewrite_name);
			} else {
				throw new \CHttpException(404, 'Заболевание не найдено');
			}
		}

		if ($illness->sector) {
			$relatedIllnesses = IllnessModel::model()
				->active()
				->bySector($illness->sector->id)
				->excludeIds([$illness->id])
				->findAll(['order' => 't.name']);
		} else {
			$relatedIllnesses = [];
		}

		$letterIllnesses = IllnessModel::model()
			->active()
			->byFirstLetter($illness->getFirstLetter())
			->excludeIds([$illness->id])
			->findAll(['order' => 't.name']);

		$this->initXslTemplates();

		$vars = [
			'illness' => $illness,
			'letterIllnesses' => $letterIllnesses,
			'relatedIllnesses' => $relatedIllnesses,
			'doctorsData' => $this->findDoctors($illness),
			'specialities' => SectorModel::getItemsByCity(\Yii::app()->city->getCityId()),
		];

		if ($vars['doctorsData']['doctors']) {
			$this->setSessionSpeciality($illness->sector);
		}

		$this->render('illness', $vars);
	}

	/**
	 * Страница со списком заболеваний начинающихся с заданой буквы
	 *
	 * @param $letter
	 *
	 * @throws \CHttpException
	 */
	public function actionAlphabet($letter)
	{
		if (empty(IllnessModel::$alphabet[$letter])) {
			throw new \CHttpException(404, 'Неверная буква');
		}

		$currentLetter = IllnessModel::$alphabet[$letter];

		$illnesses = IllnessModel::model()
			->active()
			->byFirstLetter($currentLetter)
			->findAll(['order' => 't.name']);

		$this->initXslTemplates();

		$this->render('alphabet', [
			'letter' =>$currentLetter,
			'illnesses' => $illnesses,
			'specialities' => SectorModel::getItemsByCity(\Yii::app()->city->getCityId()),
		]);
	}


	/**
	 * Выборка докторов лечащих заболевание
	 *
	 * @param IllnessModel $illness
	 * @param int          $limit
	 * @param int | null   $offset
	 *
	 * @return array
	 */
	protected function findDoctors($illness, $limit = 5, $offset = null)
	{
		$doctors = null;
		$countAll = 0;

		if ($illness->sector) {
			$scopes = [
				'inCity' => [\Yii::app()->city->getCityId()],
				'active' => [],
				'inSector' => [$illness->sector->id],
			];

			$doctors = DoctorModel::model()->findAll([
				'scopes' => $scopes,
				'order' => 't.rating_internal DESC',
				'distinct' => true,
				'limit' => $limit,
				'offset' => $offset,
			]);

			$countAll = (int) DoctorModel::model()->count(['scopes' => $scopes]);
		}

		return [
			'illness' => $illness,
			'doctors' => $doctors,
			'countAll' => $countAll,
		];
	}
}
