<?php
/**
 * @var \dfs\docdoc\back\controllers\DiagnosticaController $this
 * @var Diagnostica                                        $model
 *
 */

$this->breadcrumbs = array(
	'Диагностики',
);

$this->menu = array(
	array('label' => 'Добавить диагностику', 'url' => array('create')),
);
?>

<h1>Диагностики</h1>

<?php $this->widget(
	'zii.widgets.grid.CGridView',
	array(
		'id'           => 'diagnostica-grid',
		'dataProvider' => $model->search(),
		'filter'       => $model,
		'columns'      => array(
			'id',
			'name',
			'rewrite_name',
			'title',
			'sort',
			array( // related city displayed as a link
				'name'   => 'parent_id',
				'header' => 'Привязка к диагностике',
				'type'   => 'raw',
				//	'filter'=>false,
				'filter' => Diagnostica::model()->getListItems(),
				'value'  => '$data->parent ? $data->parent->name : "Верхний уровень"',
			),
			array(
				'class' => 'CButtonColumn',
			),
		),
	)
); ?>
