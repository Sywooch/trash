<?php
/**
 * @var dfs\docdoc\back\controllers\UndergroundLineController $this
 * @var dfs\docdoc\models\UndergroundLineModel                $model
 */

$this->breadcrumbs = array(
	'Линии метро',
);

$this->menu = array(
	array('label' => 'Добавить линию метро', 'url' => array('create')),
);
?>

<h1>Линии метро</h1>

<?php $this->widget(
	'zii.widgets.grid.CGridView',
	array(
		'id'           => 'underground-line-grid',
		'dataProvider' => $model->search(),
		'filter'       => $model,
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
				'class'  => 'CDataColumn',
				'name'   => 'color',
				'type'   => 'raw',
				'filter' => false,
				'value'  => '"<div style=\"margin-bottom: 0; height: 10px; background: #" . $data->color . "\"></div>"',
			),
			array(
				'class'  => 'CDataColumn',
				'name'   => 'city_id',
				'type'   => 'raw',
				'filter' => dfs\docdoc\models\CityModel::model()->getCityList(),
				'value'  => '$data->getCityTitle()',
			),
			array(
				'class'    => 'CButtonColumn',
				'template' => '{update} {delete}',
			),
		),
	)
); ?>
