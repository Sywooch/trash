<?php
use dfs\docdoc\models\PartnerCostModel;
use dfs\docdoc\models\PartnerModel;
use dfs\docdoc\models\ServiceModel;
use dfs\docdoc\back\controllers\PartnerCostController;
use dfs\docdoc\models\CityModel;

/**
 * @var PartnerCostModel      $model
 * @var CActiveForm           $form
 * @var PartnerCostController $this
 * @var string                $h1
 */
?>

<h1><?php echo $h1; ?></h1>

<div class="form">

	<?php $form = $this->beginWidget(
		'CActiveForm',
		array(
			'id'                   => 'region-form',
			'enableAjaxValidation' => false,
		)
	); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'partner_id'); ?>
		<?php
		echo $form->dropDownList(
			$model,
			'partner_id',
			CHtml::listData(PartnerModel::model()->ordered()->findAll(), 'id', 'name'),
			['empty' => 'Любой партнер']
		);
		?>
		<?php echo $form->error($model, 'partner_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'service_id'); ?>
		<?php
		echo $form->dropDownList(
			$model,
			'service_id',
			ServiceModel::$service_types,
			['empty' => 'Любая услуга']
		);
		?>
		<?php echo $form->error($model, 'service_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'cost'); ?>
		<?php echo $form->textField($model, 'cost'); ?>
		<?php echo $form->error($model, 'cost'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'city_id'); ?>
		<?php
		echo $form->dropDownList(
			$model,
			'city_id',
			CHtml::listData(CityModel::model()->active()->ordered()->findAll(), 'id_city', 'title'),
			['empty' => 'Любой город']
		);
		?>
		<?php echo $form->error($model, 'city_id'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить'); ?>
	</div>

	<?php $this->endWidget(); ?>

</div>

