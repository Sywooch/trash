<?php
/**
 * @var \dfs\docdoc\back\controllers\PageController $this
 * @var dfs\docdoc\models\PageModel                 $model
 */

$this->breadcrumbs = array(
	'SEO страницы' => array('index'),
	'Редактирование SEO страницы',
);
?>

	<h1>Редактирование SEO страницы <?php echo $model->h1; ?></h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>