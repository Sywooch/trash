<?php
/**
 * @var GroupController $this
 * @var CActiveDataProvider $dataProvider
 */
$this->breadcrumbs = array(
	'Группы',
);

$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'         => 'Группы',
		'headerIcon'    => 'fa fa-copy',
		'headerButtons' => [
			[
				'label'     => 'Добавить группу',
				'url'       => $this->createUrl('create'),
				'icon'      => 'fa fa-plus',
				'showLabel' => false,
			]
		],
	]
);

$this->widget(
	'likefifa\components\system\admin\YbGridView',
	array(
		'id'           => 'lf-group-grid',
		'dataProvider' => $dataProvider,
		'columns'      => array(
			'id',
			'name',
			array(
				'class'    => 'booster.widgets.TbButtonColumn',
				'template' => '{update} {delete}',
			),
		),
	)
);

$this->endWidget();