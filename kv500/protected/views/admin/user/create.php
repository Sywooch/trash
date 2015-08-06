<?php
$this->breadcrumbs = array(
	'Пользователи' => array('index'),
	'Создание',
);
?>

	<h1>Создать пользователя</h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>