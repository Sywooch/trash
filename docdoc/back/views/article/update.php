<?php
/**
 * @var \dfs\docdoc\back\controllers\ArticleController $this
 * @var \dfs\docdoc\models\ArticleModel                $model
 */

$this->breadcrumbs = array(
	'Статьи' => array('index'),
	'Изменение статьи',
);
?>

<h1>Изменение статьи <?php echo $model->name; ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>