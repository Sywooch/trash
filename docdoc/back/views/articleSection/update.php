<?php
/**
 * @var \dfs\docdoc\back\controllers\ArticleSectionController $this
 * @var \dfs\docdoc\models\ArticleSectionModel                $model
 *
 */

$this->breadcrumbs = array(
	'Разделы статей' => array('index'),
	'Изменение раздела',
);
?>

<h1>Изменение раздела <?php echo $model->name; ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>