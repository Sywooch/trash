<h1>Новости</h1>

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
				'name'  => 'date',
				'type'  => 'raw',
				'filter' => false,
				'value' => '$data->getDate()',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'title',
				'type'  => 'raw',
				'value' => '$data->title',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'description',
				'type'  => 'raw',
				'value' => '$data->description',
			),
			array(
				'class'    => 'CButtonColumn',
				'template' => '{update}{delete}',
			),
		),
	)
);

?>

<a href="/admin/news/create/">Добавить новость</a>