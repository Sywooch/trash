<?php
/**
 * Created by PhpStorm.
 * User: atyutyunnikov
 * Date: 18.03.15
 * Time: 15:16
 */
use dfs\common\components\console\Command;
use dfs\docdoc\models\DoctorModel;
use Gregwar\Image\Image;

class DoctorImageCommand extends Command
{
	/**
	 * Генерация из иходной фотки врача - 3 фотки без логотипа
	 *
	 * @param array $args
	 *
	 * @throws Exception
	 *
	 * @return bool
	 */
	public function run($args)
	{
		$uploadPath = \Yii::app()->params['path']['upload'];

		$toGenerate = [
			'.160x218.jpg' => ['w' => 160, 'h' => 218],
			'.110x150.jpg' => ['w' => 110, 'h' => 150],
			'.73x100.jpg' => ['w' => 73, 'h' => 100],
		];

		$total = 0;

		$dataProvider = new CActiveDataProvider(
			DoctorModel::class,
			[
				'criteria' => [
					'condition' => "image is not null and image != ''",
					'order' => 'id desc',
				]
			]
		);

		/** @var DoctorModel[] $clinicIterator */
		$iterator = new CDataProviderIterator($dataProvider, 500);

		foreach ($iterator as $doctor) {
			$fromImage = $uploadPath . "/doctor/" . $doctor->id . '.jpg';

			$this->log("Текущий доктор " . $doctor->id);
			$this->log("Картинка источник $fromImage");

			if(file_exists($fromImage)){
				$total++;

				foreach($toGenerate as $name => $size){
					$toImage = $uploadPath . "/doctor/" . $doctor->id  . $name;

					$this->log("Генерируемая картинка  $toImage");

					$image = Image::open($fromImage);
					$image->cropResize($size['w'], $size['h'])->save($toImage);
				}
			} else {
				$this->log("Картинка источник не существует. Пропускаю.....", CLogger::LEVEL_ERROR);
			}
		}

		$this->log('Всего обработано ' . $total . ' врачей');
		$this->log('Работа скрипта закончена');
	}
}