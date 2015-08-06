<?php
$this->breadcrumbs = array(
	'Предложения' => array('index'),
	'Просмотр',
);
?>

<h1>Просмотр предложения №<?php echo $model->id; ?></h1>

<div class="row">
	<strong>Имя:</strong> <?php echo $model->name; ?>
</div>
<div class="row">
	<strong>E-mail:</strong> <?php echo $model->email; ?>
</div>
<div class="row">
	<strong>Предложение:</strong><br> <?php echo $model->text; ?>
</div>