<?php
use likefifa\models\AdminModel;

/**
 * @var AdminModel $model
 */
$this->breadcrumbs = array(
	'Администраторы',
);

$dataProvider = $model->search();

$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'         => 'Администраторы (' . $dataProvider->getTotalItemCount() . ')',
		'headerIcon'    => 'fa fa-female',
		'headerButtons' => [
			[
				'label'     => 'Добавить администратора',
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
		'id'           => 'admins-grid',
		'dataProvider' => $dataProvider,
		'filter'       => $model,
		'columns'      => array(
			'id',
			'login',
			'name',
			array(
				'name'   => 'group_id',
				'type'   => 'raw',
				'filter' => $model->groupList,
				'value'  => '$data->getGroupName()',
			),
			array(
				'class'    => 'booster.widgets.TbButtonColumn',
				'template' => '{update}',
			),
		),
	)
);

$this->endWidget();
