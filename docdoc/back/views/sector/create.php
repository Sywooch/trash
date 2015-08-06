<?php
/**
 * @var dfs\docdoc\back\controllers\SectorController $this
 * @var dfs\docdoc\models\SectorModel                $model
 *
 */

$this->breadcrumbs = array(
	'Направления' => array('index'),
	'Новое направление',
);
?>

<h1>Новое направление</h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>