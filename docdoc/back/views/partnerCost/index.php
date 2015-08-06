<?php

use dfs\docdoc\back\controllers\PartnerCostController;
use dfs\docdoc\models\PartnerCostModel;
use dfs\docdoc\models\PartnerModel;
use dfs\docdoc\models\ServiceModel;
use dfs\docdoc\models\CityModel;

/**
 * @var PartnerCostModel      $model
 * @var PartnerCostController $this
 */
?>

<?php
$this->breadcrumbs = array(
	'Стоимости заявок для партнера',
);

$this->menu = array(
	array('label' => 'Добавить стоимость', 'url' => array('create')),
);

?>

<h1>Телефоны</h1>

<?php
$this->widget(
	'zii.widgets.grid.CGridView',
	array(
		'id'           => 'city-grid',
		'dataProvider' => $model->search(),
		'filter'       => $model,
		'ajaxUpdate'   => false,
		'columns'      => array(
			'id',
			array(
				'class'  => 'CDataColumn',
				'name'   => 'partner_id',
				'type'   => 'raw',
				'filter' => CHtml::listData(PartnerModel::model()->ordered()->findAll(), 'id', 'name'),
				'value'  => '$data->partner_id ? $data->partner->name : "Любой партнер"',
			),
			array(
				'class'  => 'CDataColumn',
				'name'   => 'service_id',
				'type'   => 'raw',
				'filter' => ServiceModel::$service_types,
				'value'  => '$data->getServiceName()',
			),
			'cost',
			array(
				'class'  => 'CDataColumn',
				'name'   => 'city_id',
				'type'   => 'raw',
				'filter' => CHtml::listData(CityModel::model()->active()->ordered()->findAll(), 'id_city', 'title'),
				'value'  => '$data->city_id ? $data->city->title : "Любой город"',
			),
			array(
				'class'    => 'CButtonColumn',
				'template' => '{update}{delete}',
			),
		),
	)
);
?>
