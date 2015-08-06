<?php
/**
 * @var \dfs\docdoc\back\controllers\CityController $this
 * @var dfs\docdoc\models\CityModel $model
 */

$this->breadcrumbs = [
	'Города',
];

$this->menu = [
	['label' => 'Добавить город', 'url' => ['create']],
];
?>

<h1>Города</h1>

<?php $this->widget(
	'zii.widgets.grid.CGridView',
	[
		'id' => 'city-grid',
		'dataProvider' => $model->search(),
		'ajaxUpdate' => false,
		'columns' => [
			[
				'class' => 'CDataColumn',
				'name' => 'id_city',
				'type' => 'raw',
				'value' => '$data->id_city',
			],
			[
				'class' => 'CDataColumn',
				'name' => 'title',
				'type' => 'raw',
				'value' => '$data->title',
			],
			[
				'class' => 'CDataColumn',
				'name' => 'rewrite_name',
				'type' => 'raw',
				'value' => '$data->rewrite_name',
			],
			[
				'class' => 'CDataColumn',
				'name' => 'site_phone',
				'type' => 'raw',
				'value' => '$data->site_phone',
			],
			[
				'class' => 'CDataColumn',
				'name' => 'site_office',
				'type' => 'raw',
				'value' => '$data->site_office',
			],
			[
				'class' => 'CDataColumn',
				'name' => 'opinion_phone',
				'type' => 'raw',
				'value' => '$data->opinion_phone',
			],
			[
				'class' => 'CDataColumn',
				'name' => 'long',
				'type' => 'raw',
				'value' => '$data->long',
			],
			[
				'class' => 'CDataColumn',
				'name' => 'lat',
				'type' => 'raw',
				'value' => '$data->lat',
			],
			[
				'class' => 'CDataColumn',
				'name' => 'prefix',
				'type' => 'raw',
				'value' => '$data->prefix',
			],
			[
				'class' => 'CDataColumn',
				'name' => 'is_active',
				'filter' => $model->activeFlags,
				'type' => 'raw',
				'value' => '$data->getActiveFlag()',
			],
			[
				'class' => 'CButtonColumn',
				'template' => '{update}',
			],
		],
	]
); ?>
