<?php
use dfs\docdoc\models\PartnerModel;
use dfs\docdoc\back\controllers\PartnerController;
use dfs\docdoc\models\CityModel;
use dfs\docdoc\models\RequestModel;
use dfs\docdoc\models\QueueModel;

/**
 * @var PartnerModel      $model
 * @var CActiveForm       $form
 * @var PartnerController $this
 * @var string            $h1
 * @var array             $partnerPhonesList
 */

Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/lib/js/jquery.maskedinput.min.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/phone.js');
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
		<?php echo $form->labelEx($model, 'name'); ?>
		<?php echo $form->textField($model, 'name', array("size" => 64)); ?>
		<?php echo $form->error($model, 'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'login'); ?>
		<?php echo $form->textField($model, 'login', array("size" => 16)); ?>
		<?php echo $form->error($model, 'login'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'password'); ?>
		<?php echo $form->passwordField($model, 'password', array("value" => "")); ?>
		<?php echo $form->error($model, 'password'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'password_salt'); ?>
		<?php echo $form->textField($model, 'password_salt', array("size" => 16)); ?>
		<?php echo $form->error($model, 'password_salt'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'contact_name'); ?>
		<?php echo $form->textField($model, 'contact_name', array("size" => 64)); ?>
		<?php echo $form->error($model, 'contact_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'contact_phone'); ?>
		<?php echo $form->textField($model, 'contact_phone', array("size" => 64, "class" => "js-mask-phone")); ?>
		<?php echo $form->error($model, 'contact_phone'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'contact_email'); ?>
		<?php echo $form->textField($model, 'contact_email', array("size" => 64)); ?>
		<?php echo $form->error($model, 'contact_email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'city_id'); ?>
		<?php echo $form->dropDownList(
			$model,
			'city_id',
			CHtml::listData(CityModel::model()->findAll(), 'id_city', 'title'),
			array("empty" => "")
		); ?>
		<?php echo $form->error($model, 'city_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'request_kind'); ?>
		<?php echo $form->dropDownList(
			$model,
			'request_kind',
			RequestModel::model()->getKindList()
		); ?>
		<?php echo $form->error($model, 'request_kind'); ?>
	</div>

	<div class="row">
		<?php echo $form->checkBox($model, 'use_special_price'); ?>
		<?php echo $form->labelEx($model, 'use_special_price', [ 'class' => 'label-for-checkbox' ]); ?>
		<?php echo $form->error($model, 'use_special_price'); ?>
	</div>

	<div class="row">
		<?php echo $form->checkBox($model, 'offer_accepted'); ?>
		<?php echo $form->labelEx($model, 'offer_accepted', [ 'class' => 'label-for-checkbox' ]); ?>
		<?php echo $form->error($model, 'offer_accepted'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'cost_per_request'); ?>
		<?php echo $form->textField($model, 'cost_per_request', array("size" => 11)); ?>
		<?php echo $form->error($model, 'cost_per_request'); ?>
	</div>

	<div class="row partner-phones-list">
		<div class="title">Телефоны</div>
		<?php foreach($partnerPhonesList["visible"] as $visible) { ?>
			<div>
				<?php echo CHtml::label($visible["city"]->title, "partnerPhonesList_" . $visible["city"]->id_city) ?>
				<?php echo CHtml::telField(
					"partnerPhonesList[{$visible["city"]->id_city}]",
					$visible["phone"],
					["class" => "js-mask-phone"]
				); ?>
			</div>
		<?php } ?>
		<?php if ($partnerPhonesList["invisible"]) { ?>

			<?php echo CHtml::label("Доп. телефоны", "toggle") ?>
			<?php echo CHtml::checkBox("toggle"); ?>

			<div class="invisible">
				<?php foreach($partnerPhonesList["invisible"] as $invisible) { ?>
					<div>
						<?php echo CHtml::label($invisible["city"]->title, "partnerPhonesList_" . $invisible["city"]->id_city) ?>
						<?php echo CHtml::telField(
							"partnerPhonesList[{$invisible["city"]->id_city}]",
							"",
							["class" => "js-mask-phone"]
						); ?>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'phone_queue'); ?>
		<?php echo $form->dropDownList($model, 'phone_queue', QueueModel::getQueueNames()); ?>
		<?php echo $form->error($model, 'phone_queue'); ?>
	</div>

	<div class="row">
		<?php echo $form->checkBox($model, 'send_sms'); ?>
		<?php echo $form->labelEx($model, 'send_sms', [ 'class' => 'label-for-checkbox' ]); ?>
		<?php echo $form->error($model, 'send_sms'); ?>
	</div>

	<div class="row">
		<?php echo $form->checkBox($model, 'show_watermark'); ?>
		<?php echo $form->labelEx($model, 'show_watermark', [ 'class' => 'label-for-checkbox' ]); ?>
		<?php echo $form->error($model, 'show_watermark'); ?>
	</div>

	<div class="row">
		<?php echo $form->checkBox($model, 'send_sms_to_clinic'); ?>
		<?php echo $form->labelEx($model, 'send_sms_to_clinic', [ 'class' => 'label-for-checkbox' ]); ?>
		<?php echo $form->error($model, 'send_sms_to_clinic'); ?>
	</div>

	<div class="row">
		<?php echo $form->checkBox($model, 'show_clinics_with_contracts'); ?>
		<?php echo $form->labelEx($model, 'show_clinics_with_contracts', [ 'class' => 'label-for-checkbox' ]); ?>
		<?php echo $form->error($model, 'show_clinics_with_contracts'); ?>
	</div>

	<div class="row">
		<?php echo $form->checkBox($model, 'not_merged_requests'); ?>
		<?php echo $form->labelEx($model, 'not_merged_requests', [ 'class' => 'label-for-checkbox' ]); ?>
		<?php echo $form->error($model, 'not_merged_requests'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'json_params'); ?>
		<?php echo $form->textArea($model, 'json_params'); ?>
		<?php echo $form->error($model, 'json_params'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить'); ?>
	</div>

	<?php $this->endWidget(); ?>

</div>

