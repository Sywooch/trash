<?php
/**
 * @var \dfs\docdoc\back\controllers\DistrictController $this
 * @var dfs\docdoc\models\DistrictModel                 $model
 */

$this->breadcrumbs = array(
	'Районы' => array('index'),
	'Редактирование района',
);
?>

	<h1>Редактирование района <?php echo $model->name; ?></h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>