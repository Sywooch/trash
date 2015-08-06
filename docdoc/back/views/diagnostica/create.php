<?php
/**
 * @var \dfs\docdoc\back\controllers\DiagnosticaController $this
 * @var Diagnostica                                        $model
 *
 */

$this->breadcrumbs = array(
	'Диагностики' => array('index'),
	'Новая диагностика',
);
?>

<h1>Новая диагностика</h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>