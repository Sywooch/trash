<?php
/**
 * @var dfs\docdoc\back\controllers\SectorSeoTextController $this
 * @var SectorSeoText                                       $model
 *
 */

$this->breadcrumbs = array(
	'SEO-блоки' => array('index'),
	'Новый SEO-блок',
);
?>

<h1>Новый SEO-блок</h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>