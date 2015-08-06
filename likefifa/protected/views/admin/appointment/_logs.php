<?php
/**
 * @var AppointmentController $this
 * @var LfAppointment         $model
 * @var CActiveDataProvider   $logsDataProvider
 */
if(!$logsDataProvider)
	return false;
$this->widget(
	'likefifa\components\system\admin\YbGridView',
	[
		'dataProvider' => $logsDataProvider,
		'columns'      => [
			[
				'name'  => 'created',
				'value' => 'date("d.m.Y H:i", strtotime($data->created))',
			],
			[
				'header' => 'Пользователь',
				'type'   => 'raw',
				'value'  => '$data->getFormattedUser()',
			],
			[
				'header' => '',
				'type'   => 'raw',
				'value'  => '$data->getFormattedText()',
			]
		]
	]
);

