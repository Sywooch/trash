<?php
/**
 * @var CDataProvider                                  $dataProvider
 * @var \dfs\docdoc\back\controllers\ArticleController $this
 */
$this->breadcrumbs = array(
	'Статьи',
);

$this->menu = array(
	array(
		'label' => 'Добавить статью',
		'url'   => array('create')
	),
);
?>

<h1>Статьи</h1>

<?php $this->widget(
	'zii.widgets.grid.CGridView',
	array(
		'id'           => 'article-grid',
		'dataProvider' => $dataProvider,
		'columns'      => array(
			'id',
			array(
				'class'    => 'CDataColumn',
				'name'     => 'name',
				'sortable' => true,
			),
			array(
				'class'    => 'CDataColumn',
				'name'     => 'disabled',
				'value'    => '$data->disabled ? "да" : "нет"',
				'sortable' => true,
			),
			array(
				'class'    => 'CButtonColumn',
				'template' => '{update} {delete}',
			),
		),
	)
); ?>
