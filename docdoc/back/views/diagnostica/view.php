<?php
/**
 * @var \dfs\docdoc\back\controllers\DiagnosticaController $this
 * @var Diagnostica                                        $model
 * @var array                                              $linkedCenters
 *
 */

$this->breadcrumbs = array(
	'Диагностики' => array('index'),
	$model->name,
);

$this->menu = array(
	array('label' => 'Вернуться', 'url' => array('index')),
	array('label' => 'Изменить диагностику', 'url' => array('update', 'id' => $model->id)),
	array('label'       => 'Удалить диагностику',
		  'url'         => '#',
		  'linkOptions' => array('submit'  => array('delete', 'id' => $model->id),
								 'confirm' => 'Are you sure you want to delete this item?'
		  )
	),
);
?>

<h1>Диагностика - <?php echo $model->name; ?></h1>

<?php $this->widget(
	'LinkedItemsWidget',
	array(
		'title' => 'Связанные диагностические центры',
		'items' => $linkedCenters,
	)
); ?>

<?php $this->widget(
	'zii.widgets.CDetailView',
	array(
		'data'       => $model,
		'attributes' => array(
			'id',
			'name',
			'rewrite_name',
			'title',
			'meta_keywords',
			'meta_description',
			'parent_id',
			array( // related city displayed as a link
				'label' => 'Привязка к диагностике:',
				'type'  => 'raw',
				'value' => $model->parent ? $model->parent->name : "Верхний уровень",
			),
		),
	)
); ?>
