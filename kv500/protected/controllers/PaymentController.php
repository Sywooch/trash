<?php

class PaymentController extends FrontendController
{

	public function actionPayment()
	{
		if (Yii::app()->session['balanceAdd'] && Yii::app()->session['userId']) {

			$model = Payment::model()->getNewModel(Yii::app()->session['userId'], Yii::app()->session['balanceAdd']);

			Yii::app()->session['balanceAdd'] = false;
			Yii::app()->session['userId'] = false;

			if ($model->save()) {
				$this->redirect("/lk");
			}
		}

		$this->redirect("/lk");
	}
}