<?php
/**
 * @var \dfs\docdoc\back\controllers\DiagnosticaController $this
 * @var Diagnostica                                        $model
 * @var array                                              $linkedCenters
 *
 */
$this->breadcrumbs = array(
	'Диагностики' => array('index'),
	'Изменение диагностики',
);
?>

<h1>Изменение диагностики <?php echo $model->name; ?></h1>

<?php $this->widget(
	'LinkedItemsWidget',
	array(
		'title' => 'Связанные диагностические центры',
		'items' => $linkedCenters,
	)
); ?>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>