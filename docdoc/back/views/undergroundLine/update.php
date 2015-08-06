<?php
/**
 * @var \dfs\docdoc\back\controllers\UndergroundLineController $this
 * @var dfs\docdoc\models\UndergroundLineModel                 $model
 */

$this->breadcrumbs = array(
	'Линии метро' => array('index'),
	'Редактирование линии метро',
);
?>

	<h1>Редактирование линии метро <?php echo $model->name; ?></h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>