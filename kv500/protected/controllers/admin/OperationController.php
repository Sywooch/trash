<?php

class OperationController extends BackendController
{

	public function actionIndex()
	{
		$model = new Operation('search');
		$model->unsetAttributes();
		$get = Yii::app()->request->getQuery("Operation");
		if ($get) {
			$model->attributes = $get;
		}

		$model->DbCriteria->order = "t.id DESC";

		$this->render('index', compact("model"));
	}
}