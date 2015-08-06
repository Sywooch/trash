<?php

use dfs\docdoc\models\ArticleSectionModel;

/**
 * @var \dfs\docdoc\back\controllers\ArticleController $this
 * @var \dfs\docdoc\models\ArticleModel                $model
 * @var CActiveForm                                    $form
 *
 *
 */

$form = $this->beginWidget(
	'CActiveForm',
	array(
		'id'                   => 'article-form',
		'enableAjaxValidation' => false,
	)
);
?>
<div class="form">

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

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
		<?php echo $form->labelEx($model, 'meta_description'); ?>
		<?php echo $form->textField($model, 'meta_description', array('size' => 60, 'maxlength' => 512)); ?>
		<?php echo $form->error($model, 'meta_description'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'meta_keywords'); ?>
		<?php echo $form->textField($model, 'meta_keywords', array('size' => 60, 'maxlength' => 512)); ?>
		<?php echo $form->error($model, 'meta_keywords'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'name'); ?>
		<?php echo $form->textField($model, 'name', array('size' => 60, 'maxlength' => 512)); ?>
		<?php echo $form->error($model, 'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'description'); ?>
		<?php echo $form->textArea($model, 'description', array('rows' => 6, 'cols' => 50, 'class' => 'rich')); ?>
		<?php echo $form->error($model, 'description'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'text'); ?>
		<?php echo $form->textArea($model, 'text', array('rows' => 6, 'cols' => 50, 'class' => 'rich')); ?>
		<?php echo $form->error($model, 'text'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'disabled'); ?>
		<?php echo $form->checkBox($model, 'disabled'); ?>
		<?php echo $form->error($model, 'disabled'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'is_memo'); ?>
		<?php echo $form->checkBox($model, 'is_memo'); ?>
		<?php echo $form->error($model, 'is_memo'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'article_section_id'); ?>
		<?php echo $form->dropDownList(
			$model,
			'article_section_id',
			ArticleSectionModel::model()->getListItems(),
			array('empty' => 'Выберите раздел')
		); ?>
		<?php echo $form->error($model, 'article_section_id'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить'); ?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- form -->