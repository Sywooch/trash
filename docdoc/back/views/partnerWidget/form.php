<?php
use dfs\docdoc\models\PartnerWidgetModel;
use dfs\docdoc\back\controllers\PartnerWidgetController;

/**
 * @var PartnerWidgetModel      $model
 * @var CActiveForm       $form
 * @var PartnerWidgetController $this
 * @var string            $h1
 */

$action = "/2.0/partnerWidget/save/" .$model->id;

?>
<h1>Управление виджетами партнера</h1>

<div class="form">

	<?php $form = $this->beginWidget(
		'CActiveForm',
		array(
			'id'                   => 'partner-widget-form',
			'enableAjaxValidation' => false,
			'action'               => $action
		)
	);

	echo $form->errorSummary($model);
	echo $form->hiddenField($model,	'partner_id');

	?>

	<div class="row">
		<?php echo $form->labelEx($model, 'widget'); ?>
		<?php echo $form->dropDownList(
				$model,
				'widget',
				$model->getWidgetList()); ?>
		<?php echo $form->error($model, 'widget'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'json_config'); ?>
		<?php echo $form->textArea($model, 'json_config'); ?>
		<?php echo $form->error($model, 'json_config'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'is_used'); ?>
		<?php echo $form->checkBox($model, 'is_used'); ?>
		<?php echo $form->error($model, 'is_used'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить'); ?>
	</div>

	<?php $this->endWidget(); ?>

</div>

