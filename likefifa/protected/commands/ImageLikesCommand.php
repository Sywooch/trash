<?php

use dfs\modules\likes\commands\LikesCommand;

/**
 * Class AppointmentsCommand
 * Ведет пересчет лайков к фото работ
 *
 * @author Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @date   21.11.2013
 *
 * @see    https://docdoc.megaplan.ru/task/1002382/card/
 */
class ImageLikesCommand extends LikesCommand
{

	/**
	 * Размер пачки
	 *
	 * @var int
	 */
	const PACK_SIZE = 100;

	/**
	 * Количество всех работ
	 *
	 * @var int
	 */
	private $totalCount = 0;

	/**
	 * Выполняется при вызове команды
	 *
	 * @param array $args command line parameters for this command.
	 *
	 * @return void
	 */
	public function run($args)
	{
		$this->totalCount = LfWork::model()->count();
		$packs = ceil($this->totalCount / self::PACK_SIZE);

		$this->log("Start geting likes... \n");

		if ($this->totalCount) {
			$i = 1;
			for ($j = 0; $j < $packs; $j++) {
				$criteria = new CDbCriteria;
				$criteria->offset = $j * self::PACK_SIZE;
				$criteria->limit = self::PACK_SIZE;

				$dataProvider = new CActiveDataProvider(
					"LfWork",
					array(
						'criteria'   => $criteria,
						"pagination" => false
					)
				);

				/**
				 * @var LfWork $work
				 */
				foreach ($dataProvider->getData() as $work) {
					if ($work->preview('full')) {
						$url = Yii::app()->params["baseUrl"] . substr($work->preview('full'), 1);
						$likes = $this->getAllLikes($url);

					} else {
						$likes = 0;
						$this->log(
							"Пересчет лайков для изображения с ID={$work->id} невозможен из-за отсутствия изображения",
							CLogger::LEVEL_WARNING,
							"image.likes"
						);
					}

					$work->likes = $likes;
					$work->save();

					$percent = (int)($i / $this->totalCount * 100);
					$this->log("{$percent}% completed ({$i}/{$this->totalCount})... \n");

					$i++;
				}
			}
		}

		$this->log("Completed!");
	}
}