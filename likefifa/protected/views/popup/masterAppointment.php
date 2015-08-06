<?php
/**
 * @var string        $type
 * @var string        $gaType
 * @var LfAppointment $model
 */
?>

<?php $id = ($type === 'full' ? 'master-appointment' : 'master-appointment-short'); ?>
<div class="popup-app_head"><span><?php echo $master->getFullName(); ?></span><div class="popup-close png"></div></div>
<div class="popup-app_cont">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=> $id,
		'enableAjaxValidation'=>false,
		'htmlOptions' => array(	),
	)); ?>
	<div class="pop-inp_marg">
		<div class="pop-head-inp">Как Вас зовут?</div>
		<?php echo LfHtml::activeTextField($model,'name'); ?>
	</div>
	<div class="pop-inp_marg spec-selector">
		<div class="pop-head-inp">Вид услуги:</div>
		<?php echo LfHtml::activeDropDownList($model,'specialization_id', $master->getSpecListForWorks());?>
	</div>
	<div class="pop-inp_marg service-selector">
		<div class="pop-head-inp">Подвид услуги:</div>
		<?php echo LfHtml::activeDropDownList($model,'service_id', $master->getSpecListForWorks(true)); ?>
	</div>
	<div class="pop-inp_marg">
		<div class="pop-head-inp">Ваш телефон:</div>
		<?php echo LfHtml::activeTextField($model,'phone'); ?>
	</div>
	<?php if ($master->has_departure) { ?>
		<div class="pop-inp_marg" id="popup-appointment_check">
			<?php echo LfHtml::activeCheckBox(
				$model,
				'departure',
				array('id' => 'departure', 'label' => 'Выезд на дом')
			); ?>
		</div>
		<div class="pop-inp_marg" id="popup-appointment_add">
			<div class="pop-head-inp">Ваш адрес:</div>
			<?php echo LfHtml::activeTextField(
				$model,
				'address',
				array('placeholder' => 'Укажите, пожалуйста, ваш адрес')
			); ?>
			<div class="pop-note-inp">например, Ул.Космонавтов д.33, к.2, кв.41</div>
		</div>
	<?php } ?>
	<?php
		echo CHtml::hiddenField("gaReceiver", $model->getGaReceiver($gaType));
		echo CHtml::hiddenField("gaText", $model->getGaText($gaType));
	?>
	<div style="text-align:center"><div class="button button-pink"><span>Записаться</span></div></div>
	<?php $this->endWidget(); ?>
</div>

<script type="text/javascript">
$(function() {

	$('body').trigger('specs');

	if ($("#popup-appointment_check input").prop("checked")) {
		var idCheckAppoint = $("#popup-appointment_check input").closest(".form-inp_check").data("check-id");
		$("#i-check_"+idCheckAppoint).addClass("checked");
		$("#popup-appointment_add").show();
	}

	$("#LfAppointment_phone").mask("+7 (999) 999 99 99",{placeholder:" "});

	$("#popup-appointment_check .form-inp_check").click(function(){
		if ($("#inp-check_"+$(this).data("check-id")).prop("checked"))
			$("#popup-appointment_add").hide();
		else
			$("#popup-appointment_add").show();
	});

	var $form = $('#<?php echo $id; ?>'),
	submitted = false;

	$form.submit(function(e) {
		e.preventDefault();

		if (submitted) return;
		submitted = true;

		$.ajax({
			url: $form.attr('action'),
			type: $form.attr('method'),
			dataType: 'html',
			data: $form.serializeArray()
		}).done(function(response) {
			$('#popup').html(response);
			setPopupPosition();
		});
	})
	.find('.button').click(function() {
		$form.submit();
	});
});
</script>