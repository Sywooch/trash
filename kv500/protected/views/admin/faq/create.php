<?php
$this->breadcrumbs = array(
	'Вопрос-ответ' => array('index'),
	'Добавление',
);
?>

	<h1>Добавить ответ</h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>