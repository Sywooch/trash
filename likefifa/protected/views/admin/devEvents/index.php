<?php
/**
 * @var DevEventsController $this
 * @var CActiveDataProvider $dataProvider
 */
$this->breadcrumbs = array(
	'События',
);

$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'         => 'События (' . $dataProvider->getTotalItemCount() . ')',
		'headerIcon'    => 'fa fa-calendar',
		'headerButtons' => [
			[
				'label'     => 'Добавить событие',
				'url'       => $this->createUrl('create'),
				'icon'      => 'fa fa-plus',
				'showLabel' => false,
			]
		],
	]
);

$this->widget(
	'likefifa\components\system\admin\YbGridView',
	[
		'id'           => 'admins-grid',
		'dataProvider' => $dataProvider,
		'columns'      => [
			'value',
			[
				'name' => 'date',
				'type' => 'raw',
				'value' => 'Yii::app()->dateFormatter->format("dd MMMM yyyy", strtotime($data->date))',
			],
			[
				'class'    => 'booster.widgets.TbButtonColumn',
				'template' => '{update}',
			],
		],
	]
);

$this->endWidget();
