<?php

use dfs\docdoc\models\ApiClinicModel;
use dfs\docdoc\extensions\CheckBoxColumn;

/**
 * @var dfs\docdoc\back\controllers\ApiDoctorController $this
 * @var dfs\docdoc\models\ApiDoctorModel                $model
 */

$this->breadcrumbs = [
	'Сопоставление врачей, загруженных из API клиник',
];
?>

	<h1>Сопоставление врачей, загруженных из API клиник</h1>

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
				'class' => CDataColumn::class,
				'name' => 'api_clinic_id',
				'type' => 'raw',
				'filter' => CHtml::listData(
					ApiClinicModel::model()->findAll(),
					'id',
					function ($model) {
						return $model->name . ' (' . $model->id . ')';
					}
				),
				'value' => '$data->api_clinic->name . " (" . $data->api_clinic->id . ")"',
			],
			[
				'class' => 'CDataColumn',
				'name'  => 'api_doctor_id',
				'type'  => 'raw',
				'value' => '($data->doctorClinic) ? $data->doctorClinic->doctor->name : ""',
			],
			[
				'class'  => 'CDataColumn',
				'name'   => 'clinic',
				'type'   => 'raw',
				'filter' => false,
				'value'  => '($data->doctorClinic) ? $data->doctorClinic->clinic->name : ""',
			],
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
				'checked'        => '(bool)$data->doctor',
			],
			[
				'class'    => 'CButtonColumn',
				'template' => '{update}',
			],
		],
	]
);
