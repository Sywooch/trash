<?php
use dfs\docdoc\models\PhoneModel;
use dfs\docdoc\back\controllers\PhoneController;
use dfs\docdoc\models\PhoneProviderModel;

/**
 * @var PhoneModel $model
 * @var CActiveForm $form
 * @var PhoneController $this
 * @var string $h1
 */
?>

<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css"/>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script src="/lib/js/jquery.maskedinput.min.js"></script>
<script src="/js/phone.js"></script>

<h1><?php echo $h1; ?></h1>

<div class="form">

	<?php $form = $this->beginWidget(
		'CActiveForm',
		[
			'id' => 'region-form',
			'enableAjaxValidation' => false,
		]
	); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'number'); ?>
		<?php echo $form->textField(
			$model,
			'number',
			[
				"size" => 20,
				"class" => "js-mask-phone phone-autocomplete",
				"value" => $model->getPhone()->prettyFormat('+7 ')
			]
		); ?>
		<?php echo $form->error($model, 'number'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'provider_id'); ?>
		<?php echo $form->dropDownList(
			$model,
			'provider_id',
			CHtml::listData(PhoneProviderModel::model()->findAll(), 'id', 'name')
		); ?>
		<?php echo $form->error($model, 'provider_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'comment'); ?>
		<?php echo $form->textArea(
			$model,
			'comment',
			[
				"size" => 20,
				"value" => $model->comment
			]
		); ?>
		<?php echo $form->error($model, 'comment'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить'); ?>
	</div>

	<?php $this->endWidget(); ?>
</div>