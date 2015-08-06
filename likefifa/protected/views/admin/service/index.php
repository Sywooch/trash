<?php
/**
 * @var ServiceController $this
 * @var LfService $model
 */

$this->breadcrumbs = array(
	'Услуги',
);

$dataProvider = $model->search();

$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'         => 'Услуги (' . $dataProvider->getTotalItemCount() . ')',
		'headerIcon'    => 'fa fa-cubes',
		'headerButtons' => [
			[
				'label'     => 'Добавить услугу',
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
		'id'           => 'lf-service-grid',
		'dataProvider' => $model->search(),
		'filter'       => $model,
		'ajaxUpdate'   => false,
		'columns'      => array(
			'id',
			'name',
			array(
				'name'   => 'specialization_id',
				'type'   => 'raw',
				'filter' => LfSpecialization::model()->getListItems(),
				'value'  => '$data->specialization->name'
			),
			array(
				'name'  => 'weight',
				'type'  => 'raw',
				'value' => '$data->weight',
			),
			array(
				'class'    => 'booster.widgets.TbButtonColumn',
				'template' => '{update} {delete}',
			),
		),
	)
);
$this->endWidget();
