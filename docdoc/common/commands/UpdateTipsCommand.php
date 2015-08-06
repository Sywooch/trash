<?php

use dfs\common\components\console\Command;
use dfs\docdoc\models\DoctorModel;
use dfs\docdoc\models\TipsModel;
use dfs\docdoc\models\TipsMessageModel;

/**
 * Обновление подсказок (tips)
 *
 * Примеры команд:
 *    ./yiic updateTips doctors
 */
class UpdateTipsCommand extends Command
{
	const LIMIT_DOCTORS = 500;

	/**
	 * Формирование подсказок для докторов (пересчёт метрик)
	 *
	 * @param int $all
	 */
	public function actionDoctors($all = 0)
	{
		$this->log('----- Формирование подсказок для докторов -----');

		$tips = TipsModel::model()->findAll();

		$count = 0;

		for ($i = 0; $i < 100; $i++) {
			$model = DoctorModel::model();

			$params = [
				'order' => 't.id ASC',
				'limit' => self::LIMIT_DOCTORS,
			];

			if ($all) {
				$params['offset'] = $count;
			} else {
				$model->byUpdateTips();
			}

			$n = 0;
			foreach ($model->findAll($params) as $doctor) {
				TipsMessageModel::model()->deleteAllByAttributes(['record_id' => $doctor->id]);

				$weightSum = 0;
				$items = [];
				foreach ($tips as $tip) {
					$tipMessage = new TipsMessageModel();

					if ($tipMessage->recalculate($tip, $doctor)) {
						$weightSum += $tip->weight;
						$items[] = [
							'tipMessage' => $tipMessage,
							'weight'     => $tip->weight,
						];
					}
				}

				shuffle($items);

				$w = 0.0;
				foreach ($items as $item) {
					$w += $item['weight'] * 100 / $weightSum;
					$item['tipMessage']->weight = $w;
					$item['tipMessage']->save();
				}

				DoctorModel::model()->updateByPk($doctor->id, ['update_tips' => 0]);

				$n++;
			}

			$count += $n;
			$this->log('Обработано врачей: ' . $count);

			if ($n < self::LIMIT_DOCTORS) {
				break;
			}
		}
	}
}
