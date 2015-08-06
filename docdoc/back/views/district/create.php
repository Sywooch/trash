<?php
/**
 * @var \dfs\docdoc\back\controllers\DistrictController $this
 * @var dfs\docdoc\models\DistrictModel                 $model
 */

$this->breadcrumbs = array(
	'Районы' => array('index'),
	'Новый район',
);
?>

	<h1>Новый район</h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>