<?php

use dfs\docdoc\back\controllers\PhoneController;
use dfs\docdoc\models\PhoneModel;
use dfs\docdoc\models\PhoneProviderModel;
use dfs\docdoc\models\PartnerModel;

/**
 * @var PhoneModel $model
 * @var PhoneController $this
 */
?>

<?php
$this->breadcrumbs = [
	'Телефоны',
];

$this->menu = [
	['label' => 'Добавить телефон', 'url' => ['create']],
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
				'class' => 'CDataColumn',
				'name' => 'id',
				'type' => 'raw',
				'value' => '$data->id',
			],
			[
				'class' => 'CDataColumn',
				'name' => 'number',
				'type' => 'raw',
				'value' => '$data->getPhone()->prettyFormat("+7 ")',
			],
			[
				'class' => 'CDataColumn',
				'header' => 'Города',
				'type' => 'raw',
				'value' => 'implode(array_map(function($x){return $x->title;}, $data->getUsedCities()), ", ")',
			],
			[
				'class' => 'CDataColumn',
				'name' => 'partner_id',
				'type' => 'raw',
				'value' => '$data->partner ? $data->partner->name : \'\'',
				'filter' => CHtml::listData(PartnerModel::model()->findAll(), 'id', 'name'),
			],
			[
				'class' => 'CDataColumn',
				'name' => 'model_name',
				'type' => 'raw',
				'value' => '$data->model_name ? $data->getAllTypes()[$data->model_name] : $data->model_name',
				'filter' => PhoneModel::model()->getAllTypes(),
			],
			[
				'class' => 'CDataColumn',
				'name' => 'status',
				'type' => 'raw',
				'filter' => PhoneModel::getStatusList(),
				'value' => '$data->getStatus()',
			],
			[
				'class' => 'CDataColumn',
				'header' => 'Кто использует',
				'type' => 'raw',
				'value' => 'implode(\'<br>\', array_map(function($x){ return \'<a href="\' . $x[\'url\'] . \'">\' . $x[\'text\'] . \'</a>\';}, $data->getRelatedUrls()))',
			],
			[
				'class' => 'CDataColumn',
				'name' => 'mtime',
				'type' => 'raw',
				'value' => '$data->mtime',
			],
			[
				'class' => 'CDataColumn',
				'name' => 'muser_id',
				'type' => 'raw',
				'value' => '$data->muser ? $data->muser->user_login : \'\'',
			],
			[
				'class' => 'CDataColumn',
				'name' => 'comment',
				'type' => 'raw',
				'value' => '$data->comment',
			],
			[
				'class' => 'CDataColumn',
				'name' => 'provider_id',
				'type' => 'raw',
				'value' => '$data->provider->name',
				'filter' => CHtml::listData(PhoneProviderModel::model()->findAll(), 'id', 'name'),
			],
			[
				'class' => 'CButtonColumn',
				'template' => '{update}',
			],
		],
	]
);
?>
