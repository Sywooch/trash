<?php
/**
 * @var \dfs\docdoc\back\controllers\ClinicController $this
 * @var dfs\docdoc\models\ClinicModel               $model
 * @var CActiveForm                                 $form
 */

?>

<?php $form = $this->beginWidget(
	'CActiveForm',
	array(
		'id'                   => 'clinic-details-form',
		'enableAjaxValidation' => false,
	)
); ?>

<div class="row">
	<div class="checkbox">
		<?php echo $form->checkBox($model, 'contract_signed'); ?>
	</div>
	<?php echo $form->labelEx($model, 'contract_signed', ['class' => 'checkboxLab']); ?>
	<?php echo $form->error($model, 'contract_signed'); ?>
</div>

<input type="hidden" name="dfs_docdoc_models_ClinicModel[id]" value="<?=$model->id;?>" />

<div class="row-buttons">
	<div class="form" style="width:100px; float:right; margin-left: 10px" onclick="(modalWinKey === 'close') ? $('#modalWin').hide() : window.location.reload()">ЗАКРЫТЬ</div>
	<div class="form btn-save-clinic-details" style="width:100px; float:right;">СОХРАНИТЬ</div>
</div>

<?php $this->endWidget(); ?>

