<?php
/**
 * @var \dfs\docdoc\back\controllers\UndergroundStationController $this
 * @var dfs\docdoc\models\UndergroundStationModel                 $model
 */

$this->breadcrumbs = array(
	'Станции метро' => array('index'),
	'Новая станция метро',
);
?>

	<h1>Новая станция метро</h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>