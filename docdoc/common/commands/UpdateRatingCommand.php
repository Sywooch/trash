<?php
/**
 * Created by PhpStorm.
 * User: a.tyutyunnikov
 * Date: 12.09.14
 * Time: 16:07
 */
use dfs\common\components\console\Command;
use dfs\docdoc\models\RatingModel;
use dfs\docdoc\models\RatingStrategyModel;
use dfs\docdoc\models\DoctorModel;
use dfs\docdoc\models\ClinicModel;

/**
 * Class UpdateRatingCommand
 */
class UpdateRatingCommand extends Command
{
	/**
	 * Обновление рейтингов для врачей
	 *
	 * @param int $strategyId
	 * @param bool $fromFile
	 */
	public function actionDoctor($strategyId = null, $fromFile = false)
	{
		if($fromFile){
			RatingModel::model()->updateRatingFromFile(RatingModel::TYPE_DOCTOR);
		} else {
			$this->_updateRatings($strategyId, RatingModel::TYPE_DOCTOR);
		}
	}

	/**
	 * Обновление рейтингов для клиник
	 *
	 * @param int $strategyId
	 * @param bool $fromFile
	 */
	public function actionClinic($strategyId = null, $fromFile = false)
	{
		if($fromFile){
			RatingModel::model()->updateRatingFromFile(RatingModel::TYPE_CLINIC);
		} else {
			$this->_updateRatings($strategyId, RatingModel::TYPE_CLINIC);
		}
	}

	/**
	 * Обновить рейтинги
	 *
	 * @param int $strategyId
	 * @param int $type
	 */
	private function _updateRatings($strategyId, $type)
	{
		if (empty($strategyId)) {
			$strategies = RatingStrategyModel::model()->findAll();
		} else {
			$strategies[] = RatingStrategyModel::model()->findByPk($strategyId);
		}

		foreach ($strategies as $strategy) {
			$this->log($strategy->name);
			$strategy->updateRating($type);
		}
	}

	/**
	 * Обновление rating_experience
	 */
	public function actionDoctorExperience()
	{
		$doctors = DoctorModel::model()
			->needToUpdateRatingExperience()
			->findAll();

		$this->log('----- Обновление rating_experience у врачей -----');
		$count = 0;
		foreach ($doctors as $doctor) {
			$doctor->rating_experience = 5;
			if ($doctor->save()) {
				$this->log("Обновлен rating_experience для врача #{$doctor->id}");
				$count++;
			}
		}
		$this->log("Всего обновлено {$count} врачей");
	}

	/**
	 * Обновление rating_show для клиник
	 */
	public function actionClinicRatingShow()
	{
		$this->log('----- Обновление rating_show у клиник -----');

		ClinicModel::model()->updateRatingShow();
	}

	/**
	 * Обновление рейтингов врачей только по необходимой стратегии
	 */
	public function actionOnlyByNeedStrategy()
	{
		$strategies = RatingStrategyModel::model()
			->needsToRecalc()
			->findAll();

		//сбрасываем перед пересчетом, чтобы следующий крон не выбрал повторно те же стратегии
		foreach ($strategies as $strategy) {
			//сбрасываем флаг необходимости пересчета
			$strategy->needs_to_recalc = 0;
			$strategy->updateByPk($strategy->id, ['needs_to_recalc' => $strategy->needs_to_recalc]);
		}

		foreach ($strategies as $strategy) {
			$this->log($strategy->name);
			$strategy->updateRating(RatingModel::TYPE_DOCTOR);
		}
	}
}
