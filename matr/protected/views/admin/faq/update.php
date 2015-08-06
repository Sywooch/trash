<?php
$this->breadcrumbs = array(
	'Вопрос-ответ' => array('index'),
	'Редактировать',
);
?>

	<h1>Редактирование ответа №<?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>