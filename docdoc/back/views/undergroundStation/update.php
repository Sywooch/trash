<?php
/**
 * @var \dfs\docdoc\back\controllers\UndergroundStationController $this
 * @var dfs\docdoc\models\UndergroundStationModel                 $model
 */

$this->breadcrumbs = array(
	'Станции метро' => array('index'),
	'Редактирование станции метро',
);
?>

	<h1>Редактирование станции метро <?php echo $model->name; ?></h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>