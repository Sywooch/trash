<?php
/**
 * @var \dfs\docdoc\back\controllers\DiagnosticaController $this
 * @var Diagnostica                                        $model
 * @var CActiveForm                                        $form
 *
 */

?>
<div class="form">

	<?php $form = $this->beginWidget(
		'CActiveForm',
		array(
			'id'                   => 'diagnostica-form',
			'enableAjaxValidation' => false,
		)
	); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'name'); ?>
		<?php echo $form->textField($model, 'name', array('size' => 60, 'maxlength' => 512)); ?>
		<?php echo $form->error($model, 'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'reduction_name'); ?>
		<?php echo $form->textField($model, 'reduction_name', array('size' => 60, 'maxlength' => 512)); ?>
		<?php echo $form->error($model, 'reduction_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'accusative_name'); ?>
		<?php echo $form->textField($model, 'accusative_name', array('size' => 60, 'maxlength' => 512)); ?>
		<?php echo $form->error($model, 'accusative_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'genitive_name'); ?>
		<?php echo $form->textField($model, 'genitive_name', array('size' => 60, 'maxlength' => 512)); ?>
		<?php echo $form->error($model, 'genitive_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'rewrite_name'); ?>
		<?php echo $form->textField($model, 'rewrite_name', array('size' => 60, 'maxlength' => 512)); ?>
		<?php echo $form->error($model, 'rewrite_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'title'); ?>
		<?php echo $form->textField($model, 'title', array('size' => 60, 'maxlength' => 512)); ?>
		<?php echo $form->error($model, 'title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'meta_keywords'); ?>
		<?php echo $form->textArea($model, 'meta_keywords', array('rows' => 6, 'cols' => 50)); ?>
		<?php echo $form->error($model, 'meta_keywords'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'meta_desc'); ?>
		<?php echo $form->textArea($model, 'meta_desc', array('rows' => 6, 'cols' => 50)); ?>
		<?php echo $form->error($model, 'meta_desc'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'meta_description'); ?>
		<?php echo $form->textArea($model, 'meta_description', array('rows' => 6, 'cols' => 50, 'class' => 'rich')); ?>
		<?php //echo $form->textArea($model,'meta_description',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model, 'meta_description'); ?>
	</div>

	<div class="row" style="padding-top:10px;">
		<?php echo $form->labelEx($model, 'parent_id'); ?>
		<?php echo $form->dropDownList($model, 'parent_id', Diagnostica::model()->getListItems(true)); ?>
		<?php echo $form->error($model, 'parent_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'sort'); ?>
		<?php echo $form->textField($model, 'sort', array('size' => 10, 'maxlength' => 10)); ?>
		<?php echo $form->error($model, 'sort'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить'); ?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- form -->
