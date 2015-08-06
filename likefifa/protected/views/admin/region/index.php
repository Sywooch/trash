<?php

use likefifa\models\RegionModel;

/**
 * @var RegionModel      $model
 * @var RegionController $this
 */
?>

<?php
$this->breadcrumbs = array(
	'Регионы',
);

$this->menu = array(
	array('label' => 'Добавить регион', 'url' => array('create')),
);

?>

<h1>Регионы</h1>

<?php
$this->widget(
	'zii.widgets.grid.CGridView',
	array(
		'id'           => 'city-grid',
		'dataProvider' => $model->search(),
		'filter'       => $model,
		'ajaxUpdate'   => false,
		'columns'      => array(
			"id",
			"prefix",
			"name",
			"name_genitive",
			"name_prepositional",
			array(
				'class'  => 'CDataColumn',
				'name'   => 'is_active',
				'type'   => 'raw',
				'filter' => $model::$activeFlags,
				'value'  => '$data->getActiveFlag()',
			),
			array(
				'class'    => 'CButtonColumn',
				'template' => '{update}',
			),
		),
	)
);
?>
