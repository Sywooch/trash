<?php
use dfs\docdoc\models\DistrictModel;

/**
 * Class PopupController
 */
class PopupController extends FrontendController
{

	public function actionMap()
	{
		$this->renderPartial('map');
	}

	public function actionDiaSpec($id = 0)
	{
		$criteria = new CDbCriteria;
		$criteria->order = 'sort, name';
		$diagnostics = Diagnostica::model()->findAll($criteria);
		if ($id <> 0) {
			$diag = Diagnostica::model()->findByPk((int)$id);
			$selDiag = $diag->id;
			$selDiagParent = $diag->parent_id;
		} else {
			$selDiag = 0;
			$selDiagParent = 0;
		}
		$this->renderPartial('diaSpec', array('diagnostics' => $diagnostics, 'selDiag' => $selDiag, 'selDiagParent' => $selDiagParent));
	}

	public function actionDiagnostics($id)
	{
		$this->renderPartial('diagnostics', array('centerId' => $id));
	}

	public function actionPhoto($cid)
	{
		$images = DiagnosticCenterImage::getPhotos((int)$cid);
		$this->renderPartial('photo', array('images' => $images));
	}

	public function actionAreas()
	{
		$areas = AreaMoscow::model()->findAll();
		$districts = DistrictModel::model()->findAll();

		$areaArr = array();
		foreach ($areas as $area) {
			$areaArr[$area->id]['area']['name'] = $area->name;
			$areaArr[$area->id]['area']['rewriteName'] = $area->rewrite_name;
			foreach ($districts as $district) {
				if ($area->id == $district->id_area) {
					$areaArr[$area->id]['districts'][$district->id]['name'] = $district->name;
					$areaArr[$area->id]['districts'][$district->id]['rewriteName'] = $district->rewrite_name;
				}
			}
		}

		$areaArr = json_encode($areaArr);

		$this->renderPartial('areas', compact('areaArr', 'areas', 'districts'));
	}

}