<?php
/**
 * Created by PhpStorm.
 * User: ndunaev
 * Date: 02.12.14
 * Time: 16:16
 */

namespace dfs\docdoc\front\widgets\partner;

use dfs\docdoc\extensions\TextUtils;
use dfs\docdoc\models\ClinicModel;
use \Yii;
use \CPagination;
use \CHtml;
use dfs\docdoc\front\widgets\LinkPagerWidget;

/**
 * Class PartnerWidget
 *
 * @package dfs\docdoc\front\widgets\partner
 *
 * @property \dfs\docdoc\front\controllers\WidgetController $owner
 */
class ClinicListMedportal extends ClinicList
{
	/**
	 * имя виджета
	 * @var string
	 */
	public $name = 'ClinicListMedportal';

	/**
	 * Список клиник для виджета по умолчанию
	 *
	 * @var int[]
	 */
	public $clinics =  [46, 13, 1592, 44, 2208];

	/**
	 * HTML код кнопки записи в клинику
	 *
	 * @param array $clinic
	 * @param string $label
	 *
	 * @return string
	 */
	public function getSignUpButton(array $clinic, $label)
	{
		$modal = [
			'widget' => 'Modal',
			'template' => 'Modal',
			'id' => 'DDModal',
			'action' => 'LoadWidget',
			'clinicId' => $clinic['id'],
			'partnerPhone' =>  $clinic['phone'],
		];

		return '<a
			rel="nofollow"
			class="button button-red dd-sign-up-button  dd-call-widget"
			data-partner-phone="' . $clinic['phone'] . '"
			data-widget=\'' . json_encode($modal) . '\'
			href="">Записаться</a>';
	}
}