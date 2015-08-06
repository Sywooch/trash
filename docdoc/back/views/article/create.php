<?php
/**
 * @var \dfs\docdoc\back\controllers\ArticleController $this
 * @var \dfs\docdoc\models\ArticleModel                $model
 */

$this->breadcrumbs = array(
	'Статьи' => array('index'),
	'Новая статья',
);
?>

<h1>Новая статья</h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>