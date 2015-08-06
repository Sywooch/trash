<?php

use dfs\docdoc\models\CityModel;

/**
 * @var \dfs\docdoc\back\controllers\PageController $this
 * @var dfs\docdoc\models\PageModel                 $model
 * @var CActiveForm                                 $form
 */

?>
<div class="form">

	<?php $form = $this->beginWidget(
		'CActiveForm',
		array(
			'id'                   => 'page-form',
			'enableAjaxValidation' => false,
		)
	); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'url'); ?>
		<?php echo $form->textField($model, 'url', array("size" => 100)); ?>
		<?php echo $form->error($model, 'url'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'title'); ?>
		<?php echo $form->textField($model, 'title', array("size" => 100)); ?>
		<?php echo $form->error($model, 'title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'h1'); ?>
		<?php echo $form->textField($model, 'h1', array("size" => 100)); ?>
		<?php echo $form->error($model, 'h1'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'keywords'); ?>
		<?php echo $form->textField($model, 'keywords', array("size" => 100)); ?>
		<?php echo $form->error($model, 'keywords'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'description'); ?>
		<?php echo $form->textArea($model, 'description'); ?>
		<?php echo $form->error($model, 'description'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'seo_text_top'); ?>
		<?php echo $form->textArea($model, 'seo_text_top'); ?>
		<?php echo $form->error($model, 'seo_text_top'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'seo_text_bottom'); ?>
		<?php echo $form->textArea($model, 'seo_text_bottom'); ?>
		<?php echo $form->error($model, 'seo_text_bottom'); ?>
	</div>

	<div class="row">
		<?php echo $form->checkBox($model, 'is_show'); ?>
		<?php echo $form->labelEx($model, 'is_show', array("style" => "display: inline-block;")); ?>
		<?php echo $form->error($model, 'is_show'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'id_city'); ?>
		<?php echo $form->dropDownList($model, 'id_city', CityModel::model()->getCityListWithAny()); ?>
		<?php echo $form->error($model, 'id_city'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'site'); ?>
		<?php echo $form->dropDownList($model, 'site', Yii::app()->params['siteList']); ?>
		<?php echo $form->error($model, 'site'); ?>
	</div>

	<div class="row">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить'); ?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- form -->