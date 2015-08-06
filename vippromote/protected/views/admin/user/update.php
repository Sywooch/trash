<?php
$this->breadcrumbs = array(
	'Пользователи' => array('index'),
	'Редактировать',
);
?>

	<h1>Редактирование пользователя №<?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>