<?php
$this->breadcrumbs = array(
	'Заявки на снятие денег',
);

?>

<h1>Заявки на снятие денег</h1>

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
				'name'  => 'user_id',
				'type'  => 'raw',
				'filter' => false,
				'value' => '$data->user_id',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'withdrawal',
				'type'  => 'raw',
				'filter' => false,
				'value' => '$data->withdrawal',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'balance_personal',
				'type'  => 'raw',
				'filter' => false,
				'value' => '$data->user->getBalance()',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'text',
				'type'  => 'raw',
				'filter' => false,
				'value' => '$data->text',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'date',
				'type'  => 'raw',
				'filter' => false,
				'value' => '$data->getDate()',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'is_read',
				'type'  => 'raw',
				'filter' => $model->yesNoFlags,
				'value' => '$data->getReadFlag()',
			),
			array(
				'class'    => 'CButtonColumn',
				'template' => '{view}{delete}',
			),
		),
	)
);

?>
