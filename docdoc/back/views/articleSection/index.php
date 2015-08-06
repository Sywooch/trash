<?php
/**
 * @var \dfs\docdoc\back\controllers\ArticleSectionController $this
 * @var \dfs\docdoc\models\ArticleSectionModel                $model
 *
 */

$this->breadcrumbs = array(
	'Разделы статей',
);

$this->menu = array(
	array('label' => 'Добавить раздел', 'url' => array('create')),
);
?>

<h1>Разделы статей</h1>

<?php $this->widget(
	'zii.widgets.grid.CGridView',
	array(
		'id'           => 'article-section-grid',
		'dataProvider' => $model->search(),
		//	'filter'=>$model,
		'columns'      => array(
			'id',
			'name',
			'rewrite_name',
			//		'text',
			//		'title',
			//		'meta_keywords',
			/*
			'meta_description',
			*/
			array(
				'class'    => 'CButtonColumn',
				'template' => '{update}{delete}',
			),
		),
	)
); ?>
