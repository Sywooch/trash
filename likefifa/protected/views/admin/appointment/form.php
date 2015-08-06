<?php
/**
 * @var AppointmentController $this
 * @var LfAppointment         $model
 * @var CActiveDataProvider   $logsDataProvider
 */

$this->widget(
	'booster.widgets.TbTabs',
	array(
		'type' => 'tabs',
		'tabs' => [
			[
				'id'      => 'tab-main',
				'label'   => 'Основные настройки',
				'content' => $this->renderPartial('_main_form', compact('model'), true),
				'active'  => true,
			],
			[
				'id'      => 'tab-log',
				'label'   => 'Лог',
				'content' => $this->renderPartial('_logs', compact('model', 'logsDataProvider'), true),
				'visible' => !$model->isNewRecord,
			],
		],
	)
);

