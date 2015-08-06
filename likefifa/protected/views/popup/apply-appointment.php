<div class="popup-close"></div>
<div class="popup-note_cont">
	<p>Выберите дату и время:</p>
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'apply-appointment',
		'enableAjaxValidation'=>false,
		'htmlOptions' => array(	),
	)); ?>
		<p style="margin-bottom:4px;">Дата</p>
		<div class="form-inp"><?php echo LfHtml::textField('LfAppointment[date]', $model->getDate()); ?></div>
		<p style="margin:12px 0 4px;">Время</p>
		<?php echo LfHtml::activeDropDownList($model,'time', $model->getTimes()); ?>
		
		<div class="popup-abuse_btn">Сохранить</div>
		<div class="clearfix"></div>
	<?php $this->endWidget(); ?>
</div>
<div class="popup-arr"></div>

<script type="text/javascript">
$(function() {
	var $dp = $("#LfAppointment_date").datepicker({
		minDate: 0, 
		showOtherMonths: true, 
		firstDay: 1, 
		defaultDate: +1, 
		dateFormat: "dd.mm.yy"
	});

	$.datepicker.setDefaults( $.datepicker.regional[ "ru" ] );


	$(".popup-abuse_btn").click(function() {
		$(this).closest("form").submit();
	});
});
</script>