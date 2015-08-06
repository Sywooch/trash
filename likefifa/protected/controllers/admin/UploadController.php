<?php
class UploadController extends BackendController {

	public function actionIndex() {
		$uploader = new TinyImageManager();
	}

}