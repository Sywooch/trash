<?php
$this->breadcrumbs = array(
	'Пользователи',
);

$this->menu = array(
	array('label' => 'Добавить пользователя', 'url' => array('create')),
);

?>

<h1>Пользователи</h1>

<?php
$this->widget(
	'zii.widgets.grid.CGridView',
	array(
		'id'           => 'user-grid',
		'dataProvider' => $model->search(),
		'filter'       => $model,
		'ajaxUpdate'   => false,
		'columns'      => array(
			array(
				'class' => 'CDataColumn',
				'name'  => 'user_from',
				'type'  => 'raw',
				'value' => '$data->user_from',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'user_to',
				'type'  => 'raw',
				'value' => '$data->user_to',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'sum',
				'type'  => 'raw',
				'value' => '$data->sum',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'date',
				'type'  => 'raw',
				'filter' => false,
				'value' => '$data->getDate()',
			),
		),
	)
);

?>
