<h1>Вопрос-ответ</h1>

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
				'class' => 'CDataColumn',
				'name'  => 'sort',
				'type'  => 'raw',
				'value' => '$data->sort',
			),
			array(
				'class'    => 'CButtonColumn',
				'template' => '{update}{delete}',
			),
		),
	)
);

?>

<a href="/admin/faq/create/">Добавить ответ</a>