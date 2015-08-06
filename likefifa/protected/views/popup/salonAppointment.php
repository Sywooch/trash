<?php
/**
 * @var string        $type
 * @var string        $gaType
 * @var LfAppointment $model
 */
?>

<?php $id = ($type === 'full' ? 'salon-appointment' : 'salon-appointment-short'); ?>
<div class="popup-app_head"><span>Записаться в салон &laquo;<?php echo $salon->name; ?>&raquo;</span><div class="popup-close png"></div></div>
<div class="popup-app_cont">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=> $id,
		'enableAjaxValidation'=>false,
		'htmlOptions' => array(	),
	)); ?>
	<?php echo LfHtml::activeHiddenField($model, 'dateDate', array('id' => 'appointmentDate')); ?>
	<div class="pop-inp_marg">
		<div class="pop-head-inp">Как Вас зовут?</div>
		<?php echo LfHtml::activeTextField($model,'name'); ?>
	</div>
	<div class="pop-inp_marg">
		<div class="pop-head-inp">Ваш телефон:</div>
		<?php echo LfHtml::activeTextField($model,'phone'); ?>
	</div>
	<div class="pop-inp_marg spec-selector">
		<div class="pop-head-inp">Вид услуги:</div>
		<?php echo LfHtml::activeDropDownList($model,'specialization_id', $master ? $master->getSpecListItems() : $salon->getSpecListItems());?>
	</div>
	<div class="pop-inp_marg service-selector">
		<div class="pop-head-inp">Подвид услуги:</div>
		<?php echo LfHtml::activeDropDownList($model,'service_id', $master ? $master->getServiceListItems() : $salon->getServiceListItems());?>
	</div>
	<div class="pop-inp_marg">
		<div class="pop-head-inp">Дата и время:</div>
		<div style="float:right; width:90px; margin-left:10px;">
			<?php echo LfHtml::activeDropDownList($model, 'dateTime', $model->getTimes()); ?>
		</div>
		<div class="form-inp" style="cursor:pointer; margin-bottom:10px; overflow:hidden;" id="show-appointment-calendar">
			<?php echo LfHtml::activeHiddenField($model, 'dateFormatted', array('id' => 'calendar-select-date')); ?>
			<div class="form-select">Выберите день</div>
			<div class="form-select-arr form-select-icon png"></div>
		</div>
		<div id="appointment-calendar" style="display:none;">
			<div class="calendar"></div>
			<div style="text-align:right;"><div class="button button-pink" id="btn-set-date-calendar" style="padding: 0 25px;"><span>OK</span></div></div>
		</div>
	</div>
	<div class="pop-inp_marg">
		<div class="pop-head-inp"><span id="appointment_salon__comment__link">Дополнительные пожелания:</span></div>
		<div id="popup-appointment_salon__comment" <?php if (!$model->more): ?>style="display:none;"<?php endif; ?>>
			<?php echo LfHtml::activeTextArea($model, 'more', array('rows' => 4, 'placeholder' => 'Напишите Ваши пожелания')); ?>
		</div>
	</div>
	<?php
		echo CHtml::hiddenField("gaReceiver", $model->getGaReceiver($gaType));
		echo CHtml::hiddenField("gaText", $model->getGaText($gaType));
	?>
	<div style="text-align:center; padding-top:6px;"><div class="button button-pink app-btn-sbmt"><span>Записаться</span></div></div>
	<?php $this->endWidget(); ?>
</div>
<script type="text/javascript">
$(function() {
	var $dp = $(".calendar").datepicker({
		minDate: 0, 
		maxDate: "+1M +10D", 
		showOtherMonths: true, 
		firstDay: 1, 
		defaultDate: +1, 
		altField: "#calendar-select-date", 
		altFormat: "d MM yy"
	}),
	$altField = $('#calendar-select-date'),
	$date = $('#appointmentDate');
	
	$dp.datepicker('setDate', new Date((+$date.val()) * 1000));
	if ($date.val()) $("#show-appointment-calendar .form-select").text($altField.val());

	$('body').trigger('specs');
	
	$("#LfAppointment_phone").mask("+7 (999) 999 99 99",{placeholder:" "});
	
	$("#appointment_salon__comment__link").click(function(){
		$("#popup-appointment_salon__comment").slideToggle(300);
	});
	
	$("#show-appointment-calendar").click(function(){
		$("#appointment-calendar").slideToggle(300);
	});
	
	$("#btn-set-date-calendar").click(function(){
		$date.val(parseInt($dp.datepicker('getDate').getTime() / 1000));
		
		$("#show-appointment-calendar .form-select").text($altField.val());
		$("#appointment-calendar").slideToggle(300);
	});

	if ($("#popup-appointment_salon__comment textarea").val().length > 0)
		$("#popup-appointment_salon__comment").show();
	
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
		});
	})
	.find('.app-btn-sbmt').click(function() {
		$form.submit();
	});
});
</script>