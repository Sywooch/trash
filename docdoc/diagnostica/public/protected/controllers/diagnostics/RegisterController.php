<?php

/**
 * Class RegisterController
 */
class RegisterController extends FrontendController
{

	/**
	 * Страница регистрации диагностического центра
	 */
	public function actionIndex()
	{
		if (!empty($_POST)) {

			$name = htmlspecialchars(strip_tags(trim($_POST['name'])), ENT_QUOTES);
			$phone = strip_tags(trim($_POST['phone']));
			$email = htmlspecialchars(strip_tags(trim($_POST['mail'])), ENT_QUOTES);
			$clinic = htmlspecialchars(strip_tags(trim($_POST['clinic'])), ENT_QUOTES);

			$subject = 'Заявка на размещение диагностического центра';
			$body = "<table>";
			$body .= "<tr><td><b>Контактное лицо:</b></td><td>$name</td></tr>";
			$body .= "<tr><td><b>Телефон:</b></td><td>$phone</td></tr>";
			$body .= "<tr><td><b>E-mail:</b></td><td>$email</td></tr>";
			$body .= "<tr><td><b>Название клиники:</b></td><td>$clinic</td></tr>";
			$body .= "</table>";

			emailQuery::addMessage([
				"emailTo" => Yii::app()->params['email']['clinic-registr'],
				"subj"    => $subject,
				"message" => $body
			]);

			$this->redirect(array('/register/thanks'));

		} else {
			$this->layout = null;
			$this->render('index');
		}
	}

	/**
	 * Страница - спасибо за регистрацию
	 */
	public function actionThanks()
	{
		$this->layout = null;

		$this->render('success');
	}

}