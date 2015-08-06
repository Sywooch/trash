<?php

namespace likefifa\components\likefifa\widgets\lk;

use Yii;
use CWidget;

/**
 * Class PaymentsWidget
 */

/**
 * Class Command
 *
 * Абстракция для создания консольных команд
 *
 * @author Aleksey Parshukov <parshukovag@gmail.com>
 * @date 11.10.2013
 *
 * @package likefifa\component\likefifa\widgets\lk
 */
class PaymentsWidget extends CWidget
{
	/**
	 * Мастер
	 *
	 * @var \LfMaster
	 */
	public $model;

	/**
	 * Запуск виджета
	 */
	public function run() {
		if (!Yii::app()->getModule('payments')->isActive()) {
			return;
		}

		$model = $this->model;
		if (!$model->is_popup){
			if (!isset($_SESSION['is_popup_denied'])) {
				require(Yii::getPathOfAlias('webroot.protected.views.lk') . '/popup-lk.php');
			}
		}
		$this->render("PaymentsWidget", compact("model"));
	}
} 