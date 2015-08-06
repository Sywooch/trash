<?php
/**
 * Class MailingController
 *
 * Рассылка
 *
 * @author Mikhail Vasilyev <m.vasilyev@likefifa.ru>
 * @date   29.11.2013
 *
 * @see    https://docdoc.megaplan.ru/task/1002498/card/
 */

class MailingController extends BackendController
{

	/**
	 * Отображает страницу рассылки
	 *
	 * @return void
	 */
	public function actionIndex()
	{
		$isSend = $this->sendMail();

		$masters = LfMaster::model()->findAll(array('order' => 'name'));
		$groupList = LfGroup::model()->getListItems();

		$this->render('index', compact("isSend", "masters", "groupList"));
	}

	/**
	 * Отправляет сообщения
	 *
	 * @return bool
	 */
	private function sendMail()
	{
		if (!empty($_POST["Mailing"])) {
			if (
				$_POST["Mailing"]["ids"]
				&& $_POST["Mailing"]["title"]
				&& $_POST["Mailing"]["text"]
			) {
				$title = $_POST["Mailing"]["title"];

				$message = $this->renderPartial(
					"message",
					array(
						"text" => $_POST["Mailing"]["text"]
					),
					true
				);

				$masterIds = explode(",", $_POST["Mailing"]["ids"]);
				foreach ($masterIds as $masterId) {
					if ($masterId) {
						if ($master = LfMaster::model()->findByPk($masterId)) {
							if ($master->email) {
								$letter =
									Uletter::create()
										->from(Yii::app()->params["mailing"]["email"])
										->to($master->email)
										->subject($title)
										->html($message);
								if (!empty($_FILES["mailingFile"]["tmp_name"])) {
									$letter->file(
										$_FILES["mailingFile"]["tmp_name"],
										$_FILES["mailingFile"]["name"]
									);
								}
								$letter->send();
							}
						}
					}
				}

				return true;
			}
		}

		return false;
	}
}
