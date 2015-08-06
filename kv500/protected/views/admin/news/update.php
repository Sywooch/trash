<?php
$this->breadcrumbs = array(
	'Новости' => array('index'),
	'Редактировать',
);
?>

	<h1>Редактирование новости №<?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>