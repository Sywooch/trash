<?php
/**
 * @var \dfs\docdoc\back\controllers\CityController $this
 * @var dfs\docdoc\models\CityModel                 $model
 * @var CActiveForm                                 $form
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

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'title'); ?>
		<?php echo $form->textField($model, 'title'); ?>
		<?php echo $form->error($model, 'title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'title_genitive'); ?>
		<?php echo $form->textField($model, 'title_genitive'); ?>
		<?php echo $form->error($model, 'title_genitive'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'title_prepositional'); ?>
		<?php echo $form->textField($model, 'title_prepositional'); ?>
		<?php echo $form->error($model, 'title_prepositional'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'title_dative'); ?>
		<?php echo $form->textField($model, 'title_dative'); ?>
		<?php echo $form->error($model, 'title_dative'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'rewrite_name'); ?>
		<?php echo $form->textField($model, 'rewrite_name'); ?>
		<?php echo $form->error($model, 'rewrite_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'long'); ?>
		<?php echo $form->textField($model, 'long'); ?>
		<?php echo $form->error($model, 'long'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'lat'); ?>
		<?php echo $form->textField($model, 'lat'); ?>
		<?php echo $form->error($model, 'lat'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'prefix'); ?>
		<?php echo $form->textField($model, 'prefix'); ?>
		<?php echo $form->error($model, 'prefix'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'has_diagnostic'); ?>
		<?php echo $form->checkBox($model, 'has_diagnostic'); ?>
		<?php echo $form->error($model, 'has_diagnostic'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'has_mobile'); ?>
		<?php echo $form->checkBox($model, 'has_mobile'); ?>
		<?php echo $form->error($model, 'has_mobile'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'search_type'); ?>
		<?php echo $form->dropDownList($model, 'search_type', $model->searchTypes); ?>
		<?php echo $form->error($model, 'search_type'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'site_phone'); ?>
		<?php echo $form->textField($model, 'site_phone'); ?>
		<?php echo $form->error($model, 'site_phone'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'site_office'); ?>
		<?php echo $form->textField($model, 'site_office'); ?>
		<?php echo $form->error($model, 'site_office'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'opinion_phone'); ?>
		<?php echo $form->textField($model, 'opinion_phone'); ?>
		<?php echo $form->error($model, 'opinion_phone'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'site_YA'); ?>
		<?php echo $form->textField($model, 'site_YA'); ?>
		<?php echo $form->error($model, 'site_YA'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'gtm'); ?>
		<?php echo $form->textField($model, 'gtm'); ?>
		<?php echo $form->error($model, 'gtm'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'diagnostic_site_YA'); ?>
		<?php echo $form->textField($model, 'diagnostic_site_YA'); ?>
		<?php echo $form->error($model, 'diagnostic_site_YA'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'diagnostic_gtm'); ?>
		<?php echo $form->textField($model, 'diagnostic_gtm'); ?>
		<?php echo $form->error($model, 'diagnostic_gtm'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'time_zone'); ?>
		<?php echo $form->textField($model, 'time_zone'); ?>
		<?php echo $form->error($model, 'time_zone'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'is_active'); ?>
		<?php echo $form->checkBox($model, 'is_active'); ?>
		<?php echo $form->error($model, 'is_active'); ?>
	</div>

	<div class="row">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить'); ?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- form -->