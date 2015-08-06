<?php
/**
 * @var \dfs\docdoc\back\controllers\UndergroundStationController $this
 * @var dfs\docdoc\models\UndergroundStationModel                 $model
 */

$this->breadcrumbs = array(
	'Станции метро',
);

$this->menu = array(
	array('label' => 'Добавить станцию метро', 'url' => array('create')),
);
?>

<h1>Станции метро</h1>

<?php $this->widget(
	'zii.widgets.grid.CGridView',
	array(
		'id'           => 'underground-station-grid',
		'dataProvider' => $model->search(),
		'filter'       => $model,
		'ajaxUpdate'   => true,
		'columns'      => array(
			array(
				'class' => 'CDataColumn',
				'name'  => 'id',
				'type'  => 'raw',
				'value' => '$data->id',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'name',
				'type'  => 'raw',
				'value' => '$data->name',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'rewrite_name',
				'type'  => 'raw',
				'value' => '$data->rewrite_name',
			),
			array(
				'class'  => 'CDataColumn',
				'name'   => 'underground_line_id',
				'type'   => 'raw',
				'filter' => dfs\docdoc\models\UndergroundLineModel::model()->getLineList(),
				'value'  => '$data->getLineName()',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'index',
				'type'  => 'raw',
				'value' => '$data->index',
			),
			array(
				'class'  => 'CDataColumn',
				'name'   => 'longitude',
				'type'   => 'raw',
				'filter' => false,
				'value'  => '$data->longitude',
			),
			array(
				'class'  => 'CDataColumn',
				'name'   => 'latitude',
				'type'   => 'raw',
				'filter' => false,
				'value'  => '$data->latitude',
			),
			array(
				'class'    => 'CButtonColumn',
				'template' => '{update} {delete}',
			),
		),
	)
); ?>
