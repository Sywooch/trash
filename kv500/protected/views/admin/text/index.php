<h1>Текстовые страницы</h1>

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
				'name'  => 'title',
				'type'  => 'raw',
				'value' => '$data->title',
			),
			array(
				'class'    => 'CButtonColumn',
				'template' => '{update}',
			),
		),
	)
);

?>

