<?php

class PaymentController extends BackendController
{

	public function actionIndex()
	{
		$model = new Payment('search');
		$model->unsetAttributes();
		$get = Yii::app()->request->getQuery("Payment");
		if ($get) {
			$model->attributes = $get;
		}

		$model->DbCriteria->order = "t.id DESC";

		$this->render('index', compact("model"));
	}
}