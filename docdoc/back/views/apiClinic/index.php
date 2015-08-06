<?php
use dfs\docdoc\extensions\CheckBoxColumn;

/**
 * @var dfs\docdoc\back\controllers\ApiClinicController $this
 * @var dfs\docdoc\models\ApiClinicModel                $model
 */

$this->breadcrumbs = [
	'Сопоставление API клиник',
];
?>

	<h1>Сопоставление API клиник</h1>

<?php
$this->widget(
	'zii.widgets.grid.CGridView',
	[
		'id'           => 'city-grid',
		'dataProvider' => $model->search(),
		'filter'       => $model,
		'ajaxUpdate'   => false,
		'columns'      => [
			"id",
			"name",
			[
				'header' => 'Название клиник у нас',
				'name'   => 'clinic.name',
				'type'   => 'raw',
				'value'  => '$data->clinic ? $data->clinic->name : ""',
			],
			"phone",
			"city",
			[
				'class'          => CheckBoxColumn::class,
				'name'           => 'enabled',
				'filter'         => [1 => 'Да', 0 => 'Нет'],
				'selectableRows' => 0,
				'checked'        => '$data->enabled',
			],
			[
				'class'          => CheckBoxColumn::class,
				'name'           => 'is_merged',
				'filter'         => [0 => "Нет", 1 => "Да"],
				'selectableRows' => 0,
				'checked'        => '(bool)$data->clinic',
			],
			[
				'class'    => 'CButtonColumn',
				'template' => '{update}',
			],
		],
	]
);
