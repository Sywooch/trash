<?php
/**
 * @var \dfs\docdoc\back\controllers\CityController $this
 * @var dfs\docdoc\models\CityModel                 $model
 */

$this->breadcrumbs = array(
	'Города' => array('index'),
	'Новый город',
);
?>

	<h1>Новый город</h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>