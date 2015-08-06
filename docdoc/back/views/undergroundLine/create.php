<?php
/**
 * @var \dfs\docdoc\back\controllers\UndergroundLineController $this
 * @var dfs\docdoc\models\UndergroundLineModel                 $model
 */

$this->breadcrumbs = array(
	'Линии метро' => array('index'),
	'Новая линия метро',
);
?>

	<h1>Новая линия метро</h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>