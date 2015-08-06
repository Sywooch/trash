<?php

/**
 * @var \dfs\docdoc\back\controllers\PageController $this
 * @var dfs\docdoc\models\RatingStrategyModel                 $model
 * @var CActiveForm                                 $form
 */
$this->breadcrumbs = array(
	'Стратегии' => array('index'),
	'Редактирование стратегии',
);

?>

<h1>Редактирование стратегии <?php echo $model->name; ?></h1>
<div style="float:left;">
<?php
$this->widget('zii.widgets.CBreadcrumbs', array(
		'links'=> $this->breadcrumbs,
		'htmlOptions' => ['class'=>'']
	));
?>
<div class="form">

	<?php $form = $this->beginWidget(
		'CActiveForm',
		array(
			'id'                   => 'strategy-form',
			'enableAjaxValidation' => false,
			'action'               => '/2.0/ratingStrategy/save/' .$model->id
		)
	); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'name'); ?>
		<?php echo $form->textField($model, 'name'); ?>
		<?php echo $form->error($model, 'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'type'); ?>
		<?php echo $form->dropDownList($model, 'type', $model->getStrategies()); ?>
		<?php echo $form->error($model, 'type'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'for_object'); ?>
		<?php echo $form->dropDownList($model, 'for_object', $model->getForObjects()); ?>
		<?php echo $form->error($model, 'for_object'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'chance'); ?>
		<?php echo $form->textField($model, 'chance', array("size" => 2)); ?>
		<?php echo $form->error($model, 'chance'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'needs_to_recalc'); ?>
		<?php echo $form->checkBox($model, 'needs_to_recalc'); ?>
		<?php echo $form->error($model, 'needs_to_recalc'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'params'); ?>
		<?php echo $form->textArea($model, 'params'); ?>
		<?php echo $form->error($model, 'params'); ?>
	</div>

	<div class="row">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить'); ?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- form -->