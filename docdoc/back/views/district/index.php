<?php
/**
 * @var \dfs\docdoc\back\controllers\DistrictController $this
 * @var dfs\docdoc\models\DistrictModel                 $model
 */

$this->breadcrumbs = array(
	'Районы',
);

$this->menu = array(
	array('label' => 'Добавить район', 'url' => array('create')),
);
?>

<h1>Районы</h1>

<?php $this->widget(
	'zii.widgets.grid.CGridView',
	array(
		'id'           => 'district-grid',
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
				'class' => 'CDataColumn',
				'name'  => 'rewrite_name',
				'type'  => 'raw',
				'value' => '$data->rewrite_name',
			),
			array(
				'class'  => 'CDataColumn',
				'name'   => 'id_city',
				'type'   => 'raw',
				'filter' => dfs\docdoc\models\CityModel::model()->getCityList(),
				'value'  => '$data->getCityTitle()',
			),
			array(
				'class'  => 'CDataColumn',
				'name'   => 'id_area',
				'type'   => 'raw',
				'filter' => dfs\docdoc\models\AreaModel::model()->getAreaList(),
				'value'  => '$data->getAreaName()',
			),
			array(
				'class'    => 'CButtonColumn',
				'template' => '{update} {delete}',
			),
		),
	)
); ?>
