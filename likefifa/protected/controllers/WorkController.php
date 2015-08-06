<?php
use likefifa\extensions\image\Image;

class WorkController extends FrontendController
{
	/**
	 * Возвращает модель текущей работы
	 *
	 * @param $id
	 *
	 * @return LfWork
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model = LfWork::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

	public function actionAjaxupdate()
	{
		$act = $_GET['act'];

		if ($act == 'altUpdate') {
			$altAll = $_POST['alt'];
			if (count($altAll) > 0) {
				foreach ($altAll as $workId => $alt) {
					$model = $this->loadModel($workId);
					$model->alt = $alt;
					$model->save();
				}
			}
		}
	}

	/**
	 * Переворачивает изображение
	 * Выводит на экран ссылку на новое изображение
	 *
	 * @return void
	 */
	public function actionRotate()
	{
		$id = Yii::app()->request->getQuery("id");
		$direction = Yii::app()->request->getQuery("direction");
		$name = Yii::app()->request->getQuery("name");
		if ($id && $direction) {
			$model = LfWork::model()->findByPk($id);
			if ($model) {
				echo $model->rotateImage($direction) . '?' . rand();
			}
		} else {
			if ($name) {
				$model = new LfWork();
				echo $model->rotateImage($direction, $name) . '?' . rand();
			}
		}
	}

	/**
	 * Выводит на экран окно для обрезки изображения
	 *
	 * @return void
	 */
	public function actionCropWindow()
	{
		$id = Yii::app()->request->getQuery("id");
		$name = Yii::app()->request->getQuery("name");
		if ($id) {
			$model = LfWork::model()->findByPk($id);
			if ($model) {
				$name = '';
				$imageUrl = $model->getOriginalUrl();
				$this->renderPartial("crop_window", compact('model', 'imageUrl', 'id', 'name'));
			}
		}
		if ($name) {
			$id = '';
			$model = new LfWork();
			$imageUrl = $model->getTempUrl() . '/' . $name;
			$this->renderPartial("crop_window", compact('model', 'imageUrl', 'id', 'name'));
		}
	}

	/**
	 * Обрезает фото работы и выводит путь до изображения на экран
	 *
	 * @throws CHttpException
	 *
	 * @return void
	 */
	public function actionCropImage()
	{
		if (Yii::app()->user->isGuest) {
			throw new CHttpException("403", "У вас нет прав на выполнение этого действия");
		}

		$id = Yii::app()->request->getPost("workId");
		$name = Yii::app()->request->getPost("workName");
		if (!$id && !$name) {
			throw new CHttpException("404", "Не указан ID работы");
		}

		$crops = [
			'x1' => Yii::app()->request->getPost('x1'),
			'y1' => Yii::app()->request->getPost('y1'),
			'x2' => Yii::app()->request->getPost('x2'),
			'y2' => Yii::app()->request->getPost('y2'),
			'height' => Yii::app()->request->getPost('jcropHeight'),
			'width'  => Yii::app()->request->getPost('jcropWidth'),
		];

		if ($id) {
			$model = LfWork::model()->findByPk($id);
			$model->crop_coordinates = serialize($crops);
			$model->generatePreview();
			$model->saveAttributes(['crop_coordinates']);
			echo $model->preview('big') . '?' . rand();
		} else {
			$model = new LfWork();
			$crops = $model->getCropCoordinates($crops, $name);
			(new Image($model->getTempPath() . $name))
				->crop($crops['x2'] - $crops['x1'], $crops['y2'] - $crops['y1'], $crops['y1'], $crops['x1'])
				->save($model->getTempPath() . 'crop_' . $name);


			echo $model->getTempUrl() . '/' . 'crop_' . $name . '?' . rand();
		}
	}

	/**
	 * Добавляет работу в избранное
	 *
	 * @param $id
	 *
	 * @throws CHttpException
	 */
	public function actionMarkAsMain($id)
	{
		$model = $this->loadModel($id);
		$count = $model::getTop10Count($model->master_id);
		if ($count < 10 || $model->is_main == 1) {
			$model->is_main = !$model->is_main;
			$model->save(false);
			echo 1;
		} else {
			echo 0;
		}
	}
}