<?php
/**
 * @var \dfs\docdoc\back\controllers\PageController $this
 * @var dfs\docdoc\models\PageModel                 $model
 */

$this->breadcrumbs = array(
	'SEO страницы' => array('index'),
	'Новая SEO страница',
);
?>

	<h1>Новая SEO страница</h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>