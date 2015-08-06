<?php

class OpinionController extends BackendController
{
	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$model = new LfOpinion('search');
		$model->allowed = 0;
		if (isset($_GET['LfOpinion'])) {
			$model->attributes = $_GET['LfOpinion'];
		}

		$this->render(
			'index',
			array(
				'model' => $model,
			)
		);
	}

	/**
	 * @param integer $id
	 *
	 * @throws CHttpException
	 */
	public function actionDelete($id)
	{
		if (Yii::app()->request->isPostRequest) {
			$this->loadModel($id)->delete();

			if (!isset($_GET['ajax'])) {
				$this->saveRedirect();
			}
		} else {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}
	}

	/**
	 * Изменяет значение атрибута. Используется в гридах.
	 *
	 * @throws CHttpException
	 */
	public function actionEditField()
	{
		$pk = Yii::app()->request->getPost('pk');
		$name = Yii::app()->request->getPost('name');
		$value = Yii::app()->request->getPost('value');

		if ($pk === null || $name === null || $value === null) {
			throw new CHttpException(400);
		}

		$model = $this->loadModel($pk);
		$model->$name = $value;
		$model->save(true, [$name]);
	}

	/**
	 * @param $id
	 *
	 * @return LfOpinion
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model = LfOpinion::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Возаращет нужный цвет для лейбла статуса
	 *
	 * @param LfOpinion $model
	 *
	 * @return string
	 */
	public function getStatusLabel($model)
	{
		$color = '';
		switch ($model->allowed) {
			case -1 :
				$color = 'danger';
				break;
			case 0 :
				$color = 'default';
				break;
			case 1 :
				$color = 'success';
				break;
		}
		return $color;
	}
}
