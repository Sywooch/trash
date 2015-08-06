<?php
$this->breadcrumbs = array(
	'Активационные коды' => array('index'),
	'Редактировать',
);
?>

	<h1>Редактирование кода №<?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>