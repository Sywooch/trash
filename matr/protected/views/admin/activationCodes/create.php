<?php
$this->breadcrumbs = array(
	'Активационные коды' => array('index'),
	'Создание',
);
?>

	<h1>Создать код</h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>