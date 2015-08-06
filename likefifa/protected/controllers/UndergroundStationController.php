<?php
class UndergroundStationController extends FrontendController {
	public function loadModel($id)
	{
		$model=UndergroundStation::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	public function actionAjaxupdate() {
			$act = $_GET['act'];
	
		if($act == 'indexUpdate') {
			$indexAll = $_POST['index'];
			if(count($indexAll) > 0){
				foreach ($indexAll as $stationId => $index) {
					$model=$this->loadModel($stationId);
					$model->index = $index;
					$model->save();
				}
			}
		}
	}
}