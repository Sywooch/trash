<?php 
class PageController extends FrontendController {

	public function actionIndex($pageName) {
		if (preg_match('/[^a-z0-9_-]+/i', $pageName))
			throw new CHttpException(404, 'page not found');

		$this->render($pageName);
	}

}