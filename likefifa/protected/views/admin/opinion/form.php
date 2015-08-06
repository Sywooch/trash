<div class="form">

	<?php $form = $this->beginWidget(
		'CActiveForm',
		array(
			'id'                   => 'lf-opinion-form',
			'enableAjaxValidation' => false,
		)
	); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php if (!$model->isNewRecord): ?>
		<div class="row">
			<p>Отзыв добавлен <b><?php echo $model->getCreated(); ?></b></p>
		</div>
	<?php endif; ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'created'); ?>
		<input type="text" class="date_date datepicker" value="<?php echo $model->getDate(); ?>"/>
		<input type="text" size="10" class="date_time" value="<?php echo $model->getTime(); ?>"/>
	</div>
	<input name="LfOpinion[created]" id="LfOpinion_created" type="hidden" value="">

	<div class="row">
		<?php echo $form->labelEx($model, 'allowed'); ?>
		<?php echo $form->checkBox($model, 'allowed'); ?>
		<?php echo $form->error($model, 'allowed'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'master_id'); ?>
		<?php echo $form->dropDownList($model, 'master_id', LfMaster::model()->getListItems(true)); ?>
		<?php echo $form->error($model, 'master_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'salon_id'); ?>
		<?php echo $form->dropDownList($model, 'salon_id', LfSalon::model()->getListItems(true)); ?>
		<?php echo $form->error($model, 'salon_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'name'); ?>
		<?php echo $form->textField($model, 'name', array('size' => 60, 'maxlength' => 128)); ?>
		<?php echo $form->error($model, 'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'tel'); ?>
		<?php echo $form->textField($model, 'tel', array('size' => 60, 'maxlength' => 128)); ?>
		<?php echo $form->error($model, 'tel'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'rating'); ?>
		<?php echo $form->textField($model, 'rating'); ?>
		<?php echo $form->error($model, 'rating'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'quality'); ?>
		<?php echo $form->textField($model, 'quality'); ?>
		<?php echo $form->error($model, 'quality'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'ratio'); ?>
		<?php echo $form->textField($model, 'ratio'); ?>
		<?php echo $form->error($model, 'ratio'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'is_more'); ?>
		<?php echo $form->textField($model, 'is_more'); ?>
		<?php echo $form->error($model, 'is_more'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'advantages'); ?>
		<?php echo $form->textArea($model, 'advantages', array('rows' => 2, 'cols' => 50)); ?>
		<?php echo $form->error($model, 'advantages'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'disadvantages'); ?>
		<?php echo $form->textArea($model, 'disadvantages', array('rows' => 2, 'cols' => 50)); ?>
		<?php echo $form->error($model, 'disadvantages'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'text'); ?>
		<?php echo $form->textArea($model, 'text', array('rows' => 6, 'cols' => 50)); ?>
		<?php echo $form->error($model, 'text'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'yes'); ?>
		<?php echo $form->textField($model, 'yes'); ?>
		<?php echo $form->error($model, 'yes'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model, 'no'); ?>
		<?php echo $form->textField($model, 'no'); ?>
		<?php echo $form->error($model, 'no'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- form -->


<script>
	$(function () {

		function get_app_date() {
			var date_date = $('.date_date').val();
			var date_time = $('.date_time').val();
			if (date_date) {
				if (!date_time) {
					date_time = "00:00";
				}
				var y = date_date[6] + date_date[7] + date_date[8] + date_date[9];
				var m = parseInt(date_date[3] + date_date[4]) - 1;
				var d = parseInt(date_date[0] + date_date[1]);
				var h = parseInt(date_time[0] + date_time[1]);
				var i = parseInt(date_time[3] + date_time[4]);
				var d = new Date(y, m, d, h, i);
				return(d.getTime() / 1000);
			}
		}

		$(".datepicker").datepicker({ dateFormat: "dd.mm.yy" });
		$('#LfOpinion_created').val(get_app_date());
		$('.date_date').on('change', function () {
			$('#LfOpinion_created').val(get_app_date());
		});
		$('.date_time').on('change', function () {
			$('#LfOpinion_created').val(get_app_date());
		});

	});
</script>