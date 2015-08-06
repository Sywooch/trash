<?php
/**
 * @var \dfs\docdoc\back\controllers\ArticleSectionController $this
 * @var \dfs\docdoc\models\ArticleSectionModel                $model
 * @var CActiveForm                                           $form
 *
 */

use dfs\docdoc\models\SectorModel;

?>
<div class="form">

	<?php $form = $this->beginWidget(
		'CActiveForm',
		array(
			'id'                   => 'article-section-form',
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
		<?php echo $form->labelEx($model, 'rewrite_name'); ?>
		<?php echo $form->textField($model, 'rewrite_name', array('size' => 60, 'maxlength' => 512)); ?>
		<?php echo $form->error($model, 'rewrite_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'text'); ?>
		<?php echo $form->textArea($model, 'text', array('rows' => 6, 'cols' => 50, 'class' => 'rich')); ?>
		<?php echo $form->error($model, 'text'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'title'); ?>
		<?php echo $form->textField($model, 'title', array('size' => 60, 'maxlength' => 512)); ?>
		<?php echo $form->error($model, 'title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'meta_keywords'); ?>
		<?php echo $form->textField($model, 'meta_keywords', array('size' => 60, 'maxlength' => 512)); ?>
		<?php echo $form->error($model, 'meta_keywords'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'meta_description'); ?>
		<?php echo $form->textField($model, 'meta_description', array('size' => 60, 'maxlength' => 512)); ?>
		<?php echo $form->error($model, 'meta_description'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'meta_keywords'); ?>
		<?php echo $form->dropDownList($model, 'sector_id', SectorModel::model()->getListItems(true)); ?>
		<?php echo $form->error($model, 'meta_keywords'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить'); ?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- form -->