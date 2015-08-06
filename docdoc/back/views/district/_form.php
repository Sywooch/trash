<?php
/**
 * @var \dfs\docdoc\back\controllers\DistrictController $this
 * @var dfs\docdoc\models\DistrictModel                 $model
 * @var CActiveForm                                     $form
 */

?>
<div class="form">

	<?php $form = $this->beginWidget(
		'CActiveForm',
		array(
			'id'                   => 'district-form',
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
		<?php echo $form->labelEx($model, 'rewrite_name'); ?>
		<?php echo $form->textField($model, 'rewrite_name'); ?>
		<?php echo $form->error($model, 'rewrite_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'id_city'); ?>
		<?php echo $form->dropDownList($model, 'id_city', dfs\docdoc\models\CityModel::model()->getCityList()); ?>
		<?php echo $form->error($model, 'id_city'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'id_area'); ?>
		<div id="areas" data-id="<?php echo $model->id_area; ?>"></div>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'closestDistricts'); ?>
		<div id="closestDistricts" data-id="<?php echo $model->id; ?>"></div>
	</div>

	<div class="row">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить'); ?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- form -->