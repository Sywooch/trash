<?php
use dfs\docdoc\models\ClinicPartnerPhoneModel;
use dfs\docdoc\back\controllers\ClinicPartnerPhoneController;
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\PartnerModel;

/**
 * @var ClinicPartnerPhoneModel      $model
 * @var CActiveForm                  $form
 * @var ClinicPartnerPhoneController $this
 * @var string                       $h1
 */
?>

<script src="/lib/js/jquery.maskedinput.min.js"></script>
<script src="/js/phone.js"></script>

<h1><?php echo $h1; ?></h1>

<div class="form">

	<?php $form = $this->beginWidget(
		'CActiveForm',
		[
			'id'                   => 'clinic-partner-phone-form',
			'enableAjaxValidation' => false,
		]
	); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'clinic_id'); ?>
		<?php echo $form->dropDownList(
			$model,
			'clinic_id',
			CHtml::listData(ClinicModel::model()->active()->ordered()->findAll(), 'id', 'name'),
			["empty" => ""]
		); ?>
		<?php echo $form->error($model, 'clinic_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'partner_id'); ?>
		<?php echo $form->dropDownList(
			$model,
			'partner_id',
			CHtml::listData(PartnerModel::model()->ordered()->findAll(), 'id', 'name'),
			["empty" => ""]
		); ?>
		<?php echo $form->error($model, 'clinic_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'phoneNumber'); ?>
		<?php echo $form->textField(
			$model,
			'phoneNumber',
			[
				"size" => 20,
				"class" => "js-mask-phone phone-autocomplete",
				"value" => $model->phone ? $model->phone->getPhone()->prettyFormat('+7 ') : ""
			]
		); ?>
		<?php echo $form->error($model, 'phone_id'); ?>
	</div>

	<div class="row">
		<label for="clinic-phone">Телефон клиники</label>
		<input id="clinic-phone" type="text" disabled value="<?php echo $model->clinic ? $model->clinic->phone : ''?>">
	</div>


	<?php if (!$model->isNewRecord && $model->clinic && $branches = $model->clinic->branches) { ?>
		<div id="branches-container">
			<label for="isBranches" id="isBranchesLabel">Филиалы </label>
			<input type="checkbox" id="isBranches" name="isBranches"/>

			<div id="branches-content">
				<?php foreach ($branches as $branch) { ?>
					<div class="row">
						<label>Клиника: </label>
						<?php echo $branch->name; ?>
					</div>
					<div class="row">
						<label>Партнер: </label>
						<?php echo CHtml::dropDownList(
							"BranchClinic[{$branch->id}][partner_id]",
							$model->partner_id,
							CHtml::listData(PartnerModel::model()->ordered()->findAll(), 'id', 'name'),
							["empty" => ""]
						); ?>
					</div>
					<?php
					$phone = "";
					$clinicPhone = "";

					$branchClinicPartnerPhoneModel = ClinicPartnerPhoneModel::model()->findByPk(
						[
							"clinic_id"  => $branch->id,
							"partner_id" => $model->partner_id
						]
					);
					if ($branchClinicPartnerPhoneModel && $branchClinicPartnerPhoneModel->phone) {
						$phone = $branchClinicPartnerPhoneModel->phone->getPhone()->prettyFormat('+7 ');
					}

					if ($branchClinicPartnerPhoneModel && $branchClinicPartnerPhoneModel->clinic) {
						$clinicPhone = $branchClinicPartnerPhoneModel->clinic->phone;
					}
					?>
					<div class="row">
						<label>Подменный телефон: </label>
						<?php echo CHtml::textField(
							"BranchClinic[{$branch->id}][phone]",
							$phone,
							["size" => 20, "class" => "js-mask-phone"]
						); ?>
					</div>
					<div class="row">
						<label>Телефон клиники: </label>
						<?php echo CHtml::textField(
							"BranchClinic[{$branch->id}][clinicPhone]",
							$clinicPhone,
							["size" => 20, "class" => "js-mask-phone"]
						); ?>
					</div>
					<hr>
				<?php } ?>
			</div>
		</div>
	<?php } ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить'); ?>
	</div>

	<?php $this->endWidget(); ?>
</div>