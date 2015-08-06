<?php
$this->breadcrumbs = array(
	'Новости' => array('index'),
	'Создание',
);
?>

	<h1>Создать новость</h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>