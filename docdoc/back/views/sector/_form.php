<?php
/**
 * @var dfs\docdoc\back\controllers\SectorController $this
 * @var dfs\docdoc\models\SectorModel                $model
 * @var CActiveForm                                   $form
 *
 */

use dfs\docdoc\models\SectorModel;
?>

<div class="form">

	<?php $form = $this->beginWidget(
		'CActiveForm',
		array(
			'id'                   => 'sector-form',
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
		<?php echo $form->labelEx($model, 'name_genitive'); ?>
		<?php echo $form->textField($model, 'name_genitive', array('size' => 60, 'maxlength' => 64)); ?>
		<?php echo $form->error($model, 'name_genitive'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'name_plural'); ?>
		<?php echo $form->textField($model, 'name_plural', array('size' => 60, 'maxlength' => 64)); ?>
		<?php echo $form->error($model, 'name_plural'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'name_plural_genitive'); ?>
		<?php echo $form->textField($model, 'name_plural_genitive', array('size' => 60, 'maxlength' => 64)); ?>
		<?php echo $form->error($model, 'name_plural_genitive'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'rewrite_name'); ?>
		<?php echo $form->textField($model, 'rewrite_name', array('size' => 60, 'maxlength' => 512)); ?>
		<?php echo $form->error($model, 'rewrite_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'spec_name'); ?>
		<?php echo $form->textField($model, 'spec_name', array('size' => 60, 'maxlength' => 512)); ?>
		<?php echo $form->error($model, 'spec_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'rewrite_spec_name'); ?>
		<?php echo $form->textField($model, 'rewrite_spec_name', array('size' => 60, 'maxlength' => 512)); ?>
		<?php echo $form->error($model, 'rewrite_spec_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'hidden_in_menu'); ?>
		<?php echo $form->checkBox($model, 'hidden_in_menu'); ?>
		<?php echo $form->error($model, 'hidden_in_menu'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'clinic_seo_title'); ?>
		<?php echo $form->textField($model, 'clinic_seo_title', array('size' => 60, 'maxlength' => 512)); ?>
		<?php echo $form->error($model, 'clinic_seo_title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'sector_seo_title'); ?>
		<?php echo $form->textField($model, 'sector_seo_title', array('size' => 60, 'maxlength' => 512)); ?>
		<?php echo $form->error($model, 'sector_seo_title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'is_double'); ?>
		<?php echo $form->checkBox($model, 'is_double'); ?>
		<?php echo $form->error($model, 'is_double'); ?>
	</div>

	<div class="row checkboxgroup">
		<?php echo $form->labelEx($model, 'relatedSpecialtyIds'); ?>
		<?php echo $form->checkBoxList($model, 'relatedSpecialtyIds',
			CHtml::listData(SectorModel::model()->simple()->findAll(), 'id', 'name'),
			array(
				'separator' => '',
				'template' => "<div>{input}{label}</div>"
			));
		?>
		<?php echo $form->error($model, 'relatedSpecialtyIds'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить'); ?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- form -->
