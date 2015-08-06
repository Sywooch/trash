<?php
/**
 * @var \dfs\docdoc\back\controllers\CityController $this
 * @var dfs\docdoc\models\CityModel                 $model
 */

$this->breadcrumbs = array(
	'Города' => array('index'),
	'Редактирование города',
);
?>

	<h1>Редактирование города <?php echo $model->title; ?></h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>