<?php

/**
 * @var \dfs\docdoc\back\controllers\ArticleSectionController $this
 * @var \dfs\docdoc\models\ArticleSectionModel                $model
 *
 */

$this->breadcrumbs = array(
	'Разделы статей' => array('index'),
	'Новый раздел',
);
?>

<h1>Новый раздел</h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>