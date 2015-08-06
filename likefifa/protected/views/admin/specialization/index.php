<?php
/**
 * @var SpecializationController $this
 * @var CActiveDataProvider      $dataProvider
 */

$this->breadcrumbs = array(
	'Сециализации',
);

$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'         => 'Сециализации',
		'headerIcon'    => 'fa fa-graduation-cap',
		'headerButtons' => [
			[
				'label'     => 'Добавить специализацию',
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
		'id'           => 'lf-specialization-grid',
		'dataProvider' => $dataProvider,
		'ajaxUpdate'   => false,
		'columns'      => array(
			'id',
			'name',
			array(
				'name'  => 'sector',
				'type'  => 'raw',
				'value' => '@$data->sector->name'
			),
			array(
				'name'  => 'group',
				'type'  => 'raw',
				'value' => '$data->getGroupsConcatenated()'
			),
			array(
				'class'    => 'booster.widgets.TbButtonColumn',
				'template' => '{update} {delete}',
			),
		),
	)
);

$this->endWidget();
