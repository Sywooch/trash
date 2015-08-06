<?php
use likefifa\models\LfServiceAlias;

/**
 * @var ServiceAliasesController $this
 * @var LfServiceAlias $model
 */
$this->breadcrumbs = array(
	'Алиасы услуг',
);

$dataProvider = $model->search();

$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'         => 'Алиасы услуг (' . $dataProvider->getTotalItemCount() . ')',
		'headerIcon'    => 'fa fa-cube',
		'headerButtons' => [
			[
				'label'     => 'Добавить алиас',
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
		'id'           => 'aliases-grid',
		'dataProvider' => $dataProvider,
		'filter'       => $model,
		'columns'      => array(
			[
				'name' => 'specialization_id',
				'value' => '$data->specialization_id ? $data->specialization->name : ""'
			],
			[
				'name' => 'service_id',
				'value' => '$data->service_id ? $data->service->name : ""'
			],
			'alias',
			array(
				'class'    => 'booster.widgets.TbButtonColumn',
				'template' => '{update} {delete}',
			),
		),
	)
);

$this->endWidget();
