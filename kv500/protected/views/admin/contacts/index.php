<h1>Обратная связь</h1>

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
				'name'  => 'name',
				'type'  => 'raw',
				'value' => '$data->name',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'email',
				'type'  => 'raw',
				'value' => '$data->email',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'phone',
				'type'  => 'raw',
				'value' => '$data->phone',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'text',
				'type'  => 'raw',
				'value' => '$data->text',
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