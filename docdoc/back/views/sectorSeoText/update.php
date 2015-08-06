<?php
/**
 * @var dfs\docdoc\back\controllers\SectorSeoTextController $this
 * @var SectorSeoText                                       $model
 *
 */

$this->breadcrumbs = array(
	'SEO-блоки' => array('index'),
	'Изменение SEO-блока',
);
?>

<h1>Изменение SEO-блока <?php echo $model->name; ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>