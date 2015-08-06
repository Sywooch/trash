<?php

use dfs\docdoc\back\controllers\PhoneProviderController;
use dfs\docdoc\models\PhoneProviderModel;
use dfs\docdoc\extensions\CheckBoxColumn;

/**
 * @var PhoneProviderModel $model
 * @var PhoneProviderController $this
 */
?>

<?php
$this->breadcrumbs = [
	'Телефонные провайдеры',
];

$this->menu = [
	['label' => 'Добавить провайдера', 'url' => ['create']],
];

?>

	<h1>Телефоны</h1>

<?php
$this->widget(
	'zii.widgets.grid.CGridView',
	[
		'id' => 'city-grid',
		'dataProvider' => $model->search(),
		'filter' => $model,
		'ajaxUpdate' => false,
		'columns' => [
			[
				'class' => CDataColumn::class,
				'name' => 'id',
				'type' => 'raw',
				'value' => '$data->id',
			],
			[
				'class' => CDataColumn::class,
				'name' => 'name',
				'type' => 'raw',
				'value' => '$data->name',
			],
			[
				'class' => CheckBoxColumn::class,
				'name' => 'enabled',
				'filter' => [0 => "Нет", 1 => "Да"],
				'selectableRows' => 0,
				'checked' => '(bool)$data->enabled',
			],
			[
				'class' => CDataColumn::class,
				'header' => 'Телефонов всего',
				'type' => 'raw',
				'value' => 'count($data->phones)',
			],
			[
				'class' => CDataColumn::class,
				'header' => 'Телефонов используется',
				'type' => 'raw',
				'value' => 'count($data->usedPhones)',
			],
			[
				'class' => CDataColumn::class,
				'header' => 'Телефонов свободно',
				'type' => 'raw',
				'value' => 'count($data->unusedPhones)',
			],
			[
				'class' => 'CButtonColumn',
				'template' => '{update}{delete}',
			],
		],
	]
);
