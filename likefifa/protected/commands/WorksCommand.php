<?php

use dfs\common\components\console\Command;
use likefifa\extensions\image\Image;
use Racecore\GATracking\Exception;

/**
 * DeleteBrokenImagesCommand class file.
 *
 * Удаляет битые изображения
 *
 * @author  Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @see     https://docdoc.megaplan.ru/task/1002955/card/
 * @package commands
 */
class WorksCommand extends CConsoleCommand
{
	public function removeBroken()
	{
		$this->log("=======================================");
		$this->log(
			"Началась проверка битых изображений...",
			CLogger::LEVEL_INFO,
			"protected.commands.DeleteBrokenImagesCommand"
		);

		$works = LfWork::model()->findAll();
		$i = 0;

		/**
		 * @var LfWork $model
		 */
		foreach ($works as $model) {
			if (!$model->image || !$model->hasImage()) {
				if ($model->delete()) {
					$this->log(
						"Битая работа ID = {$model->id} успешно удалена!",
						CLogger::LEVEL_INFO,
						"protected.commands.DeleteBrokenImagesCommand"
					);
					$i++;
				}
			}
		}

		$this->log(
			"Удалено битых изображений: {$i}",
			CLogger::LEVEL_INFO,
			"protected.commands.DeleteBrokenImagesCommand"
		);
		$this->log(
			"Проверка битых изображений успешно закончена!",
			CLogger::LEVEL_INFO,
			"protected.commands.DeleteBrokenImagesCommand"
		);
		$this->log("=======================================");
	}

	/**
	 * Рандомно сортирует работы мастеров
	 * Работает по следующему принципу:
	 * 1. Создать временную таблицу
	 * 2. Заполнить временную таблицу id работ, выбранных рандомной сортировкой. PK временной таблицы сгенерируется автоматом
	 * 3. Поле sort приравнять PK из временной таблицы
	 * 4. Удалить временную таблицу
	 */
	public function actionSorting()
	{
		Yii::app()->db->createCommand(
			"DROP TABLE IF EXISTS lf_work_sort_tmp;
			CREATE TEMPORARY TABLE `lf_work_sort_tmp` (
			  `id` INT NOT NULL AUTO_INCREMENT,
			  `work_id` INT NOT NULL,
			  PRIMARY KEY (`id`));

			INSERT INTO lf_work_sort_tmp (work_id) SELECT id FROM lf_work ORDER BY RAND();

			UPDATE lf_work w
			JOIN lf_work_sort_tmp t ON t.work_id = w.id
			SET w.sort = t.id;"
		)->execute();
	}

	/**
	 * Удаляет больше 50 работ у мастера
	 */
	public function actionRemoveMore50Works()
	{
		$masters = Yii::app()->db->createCommand()
			->select('master_id')
			->from(LfWork::model()->tableName())
			->group('master_id')
			->having('count(*) > 50')
			->queryColumn();

		foreach ($masters as $master_id) {
			$works = Yii::app()->db->createCommand()
				->select(['id'])
				->from(LfWork::model()->tableName())
				->where('master_id = :master_id')
				->bindValues([':master_id' => $master_id])
				->order('t.created DESC')
				->queryColumn();
			$id = array_slice($works, 50, count($works) - 50);

			Yii::app()->db->createCommand()->delete(
				LfWork::model()->tableName(),
				'id IN (' . implode(',', $id) . ')'
			);
		}
	}

	public function actionMoveOther() {
		Yii::app()->db->setActive(false);
		Yii::app()->db->setActive(true);
		echo 'Copy masters' . PHP_EOL;
		$model = new LfMaster;
		foreach(Yii::app()->db->createCommand()->select('photo')->where('photo is not null')->from('lf_master')->queryColumn() as $image) {
			$file = './../upload/masters/' . $image;
			if(!file_exists($file)) {
				$parts = explode('.', $image);
				$newImage = $parts[0] . '_full.' . $parts[1];
				$file = './../upload/masters/' . $newImage;

				if(file_exists($file) && is_file($file)) {
					$target = $model->getOriginalPath() . $image;
					copy($file, $target);

					$is = @getimagesize($target);
					if($is[0] > 1500 || $is[1] > 1500) {
						$image = new Image($target);
						$image->resize(1500, 1500, $is[0] > $is[1] ? Image::WIDTH : Image::HEIGHT);
						$image->save();
					}
				}
			}
		}
	}

	public function actionPreview() {
		// Фиксы для правильной генерации урлов
		$_SERVER['SERVER_NAME'] = str_replace('http://', '', Yii::app()->params['baseUrl']);
		$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'];

		echo 'Generate masters...', PHP_EOL;
		foreach(Yii::app()->db->createCommand()->select('id')->where('photo is not null')->from('lf_master')->queryColumn() as $id) {
			$model = LfMaster::model()->findByPk($id);
			try {
				$model->avatar(true);
			} catch(Exception $e) {

			}
			unset($model);
		}
	}
}