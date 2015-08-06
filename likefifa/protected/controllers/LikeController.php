<?php
class LikeController extends FrontendController {
	
	public function actionIndex($id) {
		$work = LfWork::model()->findByPk($id);
		if ($work) {
			if (empty(Yii::app()->session['work'.$work->id])) {
				$work->likes++;
				$work->save();
				
				Yii::app()->session['work'.$work->id] = true;
			}
			$likes = $work->likes;
		}
		else {
			$likes = 0;
		}
		
		echo json_encode(compact('likes'));
		Yii::app()->end();
	}
	
}