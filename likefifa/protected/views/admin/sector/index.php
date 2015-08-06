<?php
$this->breadcrumbs = array(
	'Направления',
);

$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'         => 'Направления',
		'headerIcon'    => 'fa fa-group',
		'headerButtons' => [
			[
				'label'     => 'Добавить направление',
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
		'id'           => 'sector-grid',
		'dataProvider' => $dataProvider,
		'columns'      => array(
			'id',
			array(
				'class'    => 'CDataColumn',
				'name'     => 'name',
				'sortable' => true,
			),
			array(
				'class'    => 'CDataColumn',
				'name'     => 'rewrite_name',
				'sortable' => true,
			),
			array(
				'class'    => 'booster.widgets.TbButtonColumn',
				'template' => '{update} {delete}',
			),
		),
	)
);

$this->endWidget();