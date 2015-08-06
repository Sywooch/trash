<?php
use dfs\docdoc\models\PhoneProviderModel;
use dfs\docdoc\back\controllers\PhoneProviderController;

/**
 * @var PhoneProviderModel $model
 * @var CActiveForm $form
 * @var PhoneProviderController $this
 * @var string $h1
 */
?>

<!--<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css"/>-->
<!--<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>-->
<!--<script src="/lib/js/jquery.maskedinput.min.js"></script>-->

<h1><?php echo $model->id ? ('Редактирование провайдера № ' . $model->id) : 'Добавление провайдера'; ?></h1>

<div class="form">

	<?php $form = $this->beginWidget(
		'CActiveForm',
		[
			'id' => 'phone-provider-form',
			'enableAjaxValidation' => false,
		]
	); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'name'); ?>
		<?php echo $form->textField(
			$model,
			'name',
			["size" => 20, "value" => $model->name]
		); ?>
		<?php echo $form->error($model, 'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'enabled'); ?>
		<?php echo $form->checkBox($model, 'enabled', ["checked" => $model->enabled]); ?>
		<?php echo $form->error($model, 'enabled'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить'); ?>
	</div>

	<?php $this->endWidget(); ?>

</div>

