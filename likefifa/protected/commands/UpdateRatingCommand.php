<?php

use dfs\common\components\console\Command;

/**
 * Обновляет рейтинг у мастеров
 *
 * Запускать раз в день (желательно в ночное время)
 *
 */
class UpdateRatingCommand extends Command
{

	/**
	 * Размер пачки для выбора мастеров
	 *
	 * @var int
	 */
	const PACK_SIZE = 100;

	/**
	 * @param array $args
	 *
	 * @return void
	 */
	public function run($args)
	{
		$this->log("=======================================");

		$this->_masters();
		$this->_salons();

		$this->log("=======================================");
	}

	private function _masters()
	{
		$this->log(
			"Начался пересчет рейтинга у мастеров...",
			CLogger::LEVEL_INFO,
			"protected.commands.UpdateRatingCommand"
		);

		$ratings = [];

		// Получаем рейтинг по заявкам (скоринг Вильсона)
		$appointmentsSource = Yii::app()->db->createCommand(
			"SELECT
			m.id,
			((positive + 1.9208) / (positive + negative) -
				1.96 * SQRT((positive * negative) / (positive + negative) + 0.9604) /
				(positive + negative)) / (1 + 3.8416 / (positive + negative))
			AS score
			FROM
		(SELECT
			t.master_id,
			count(*) cnt,
			(SELECT count(*) FROM lf_appointment WHERE master_id = t.master_id AND status = 30) negative,
			(SELECT count(*) FROM lf_appointment WHERE master_id = t.master_id AND status = 50) positive
		FROM lf_appointment t
		WHERE t.master_id IS NOT NULL
		GROUP BY t.master_id
		) a
		JOIN lf_master m ON m.id = a.master_id
		WHERE positive + negative > 0
			ORDER BY score DESC;"
		)->queryAll();

		foreach ($appointmentsSource as $appointment) {
			$ratings[$appointment['id']] = [
				'appointment' => $appointment['score'],
				'opinion'     => 0,
				'profile'     => 0,
			];
		}

		// Получаем рейтинг по отзывам (с учетом времени публикации отзыва)
		$opinionSource = Yii::app()->db->createCommand(
			"SELECT
			m.id,
			(((cnt / (cnt + 2)) * r) + (2 / (cnt + 2))) * 7.2453 * time_score AS score
		FROM (
		SELECT
			t.master_id,
			count(t.id) cnt,
			avg(t.rating) r,
			AVG(if(CEIL((UNIX_TIMESTAMP(NOW()) - created) / 86400) > 90,
				1.106*EXP(0.001697 * -1 * CEIL((UNIX_TIMESTAMP(NOW()) - created)) / 86400), 1)) AS time_score
		FROM lf_opinion t
		WHERE t.master_id IS NOT NULL AND t.allowed = 1
		GROUP BY t.master_id) a
		JOIN lf_master m ON m.id = a.master_id
		ORDER BY score DESC"
		)->queryAll();

		$maxScore = $opinionSource[0]['score'];
		foreach ($opinionSource as $opinion) {
			if (!array_key_exists($opinion['id'], $ratings)) {
				$ratings[$opinion['id']] = [
					'appointment' => 0,
					'opinion'     => 0,
					'profile'     => 0,
				];
			}

			$ratings[$opinion['id']]['opinion'] = $opinion['score'] / $maxScore;
		}

		// Получаем рейтинг по заполненности ЛК
		$masters = Yii::app()->db->createCommand()->select('id')->from(LfMaster::model()->tableName())->queryColumn();
		foreach ($masters as $master_id) {
			$master = LfMaster::model()->resetScope()->findByPk($master_id);
			if (!array_key_exists($master->id, $ratings)) {
				$ratings[$master->id] = [
					'appointment' => 0,
					'opinion'     => 0,
					'profile'     => 0,
				];
			}
			$ratings[$master->id]['profile'] = $master->getProfileRating() / 100;
			$master->updateRating();
			$ratings[$master->id]['days'] = round((time() - strtotime($master->created)) / 86400);
		}

		Yii::app()->db->createCommand('UPDATE lf_master SET rating_composite = 0')->execute();
		$finishRatings = [];
		$maxRating = 0;
		foreach ($ratings as $master_id => $r) {
			$finishRatings[$master_id] = [
				'full'    => 0,
			];
			// Разные веса скорингов в зависимости от даты регистрации мастера
			if ($r['days'] <= 45) {
				$finishRatings[$master_id]['full'] =
					((40 * $r['appointment']) + (10 * $r['opinion']) + (50 * $r['profile']));
			} else {
				$finishRatings[$master_id]['full'] =
					((60 * $r['appointment']) + (20 * $r['opinion']) + (20 * $r['profile']));
			}
			if ($finishRatings[$master_id]['full'] > $maxRating) {
				$maxRating = $finishRatings[$master_id]['full'];
			}
		}

		// Округляем максимальный рейтинг до пяти
		$maxRating = (round($maxRating / 5)) * 5;

		// Сохраняем рейтинги
		foreach ($finishRatings as $master_id => $finishRating) {
			$mod = fmod($finishRating['full'], 1);
			// вычисляем процент относительно максимального рейтинга
			$rating = ((round($finishRating['full'] / 5)) * 5 - $mod) / $maxRating * 100;

			Yii::app()->db->createCommand()->update(
				'lf_master',
				[
					'rating_composite' => $rating,
				],
				'id = :id',
				[':id' => $master_id]
			);
		}

		$this->log(
			"Пересчет рейтинга у мастеров успешно закончен!",
			CLogger::LEVEL_INFO,
			"protected.commands.UpdateRatingCommand"
		);
	}

	private function _salons()
	{
		$this->log(
			"Начался пересчет рейтинга у салонов...",
			CLogger::LEVEL_INFO,
			"protected.commands.UpdateRatingCommand"
		);

		$totalCount = LfSalon::model()->count();

		if ($totalCount) {

			$packs = ceil($totalCount / self::PACK_SIZE);

			for ($j = 0; $j < $packs; $j++) {
				$criteria = new CDbCriteria;
				$criteria->offset = $j * self::PACK_SIZE;
				$criteria->limit = self::PACK_SIZE;

				$dataProvider =
					new CActiveDataProvider("LfSalon", array("criteria" => $criteria, "pagination" => false));

				/**
				 * @var LfSalon $model
				 */
				foreach ($dataProvider->getData() as $model) {
					$model->updateRating();
				}
			}
		}

		$this->log(
			"Пересчет рейтинга у салонов успешно закончен!",
			CLogger::LEVEL_INFO,
			"protected.commands.UpdateRatingCommand"
		);
	}
}