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
				'class'  => 'CDataColumn',
				'name'   => 'id',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'created',
				'type'  => 'raw',
				'filter' => false,
				'value' => '$data->getCreated()',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'email',
				'type'  => 'raw',
				'value' => '$data->email',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'name',
				'type'  => 'raw',
				'value' => '$data->name',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'phone',
				'type'  => 'raw',
				'value' => '$data->phone',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'balance_personal',
				'type'  => 'raw',
				'filter' => false,
				'value' => '$data->getBalance()',
			),
			array(
				'class'    => 'CButtonColumn',
				'template' => '{view}{update}{delete}',
			),
		),
	)
);

?>
