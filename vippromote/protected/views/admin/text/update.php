<?php
$this->breadcrumbs = array(
	'Текстовые страницы' => array('index'),
	'Редактировать',
);
?>

	<h1>Редактирование текстовой страницы №<?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>