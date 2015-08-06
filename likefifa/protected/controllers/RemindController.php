<?php 
class RemindController extends FrontendController {
	
	public function actionIndex() {
		$model = new RemindForm;
		
		if (isset($_POST['RemindForm'])) {
			$model->attributes = $_POST['RemindForm'];
			if ($model->validate()) {
				$model->createRemind();
				$this->redirect(array('remind/sent'));
			}
		}
		
		$this->render('index', compact('model'));
	}
	
	public function actionSent() {
		$this->render('sent');
	}
	
	public function actionHash($hash) {
		$remind = LfRemind::model()->findByHash($hash);
		if (!$remind)
			throw new CHttpException(404, 'Ссылка устарела.');
		
		$remind->apply();
		
		$this->render('hash');
	}
	
}