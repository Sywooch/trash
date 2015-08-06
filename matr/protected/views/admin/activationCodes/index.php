<h1>Активационные коды</h1>

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
				'name'  => 'code',
				'type'  => 'raw',
				'value' => '$data->code',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'is_active',
				'type'  => 'raw',
				'filter' => false,
				'value' => '($data->is_active) ? "Да" : "Нет"',
			),
			array(
				'class'    => 'CButtonColumn',
				'template' => '{update}{delete}',
			),
		),
	)
);

?>

<a href="/admin/activationCodes/create/">Добавить код</a>