<?php
/**
 * @var \dfs\docdoc\back\controllers\UndergroundLineController $this
 * @var dfs\docdoc\models\UndergroundLineModel                 $model
 * @var CActiveForm                                            $form
 */

?>
<div class="form">

	<?php $form = $this->beginWidget(
		'CActiveForm',
		array(
			'id'                   => 'underground-line-form',
			'enableAjaxValidation' => false,
		)
	); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'name'); ?>
		<?php echo $form->textField($model, 'name'); ?>
		<?php echo $form->error($model, 'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'color'); ?>
		<?php echo $form->textField($model, 'color'); ?>
		<?php echo $form->error($model, 'color'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'city_id'); ?>
		<?php echo $form->dropDownList($model, 'city_id', dfs\docdoc\models\CityModel::model()->getCityList()); ?>
		<?php echo $form->error($model, 'city_id'); ?>
	</div>

	<div class="row">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить'); ?>
	</div>

	<?php $this->endWidget(); ?>

</div>