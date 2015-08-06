<?php
/**
 * @var PopupController $this
 * @var string          $gaType
 * @var LfAppointment   $model
 */
?>

<div class="popup-app_head"><span>Новая заявка</span>

	<div class="popup-close png"></div>
</div>
<div class="popup-app_cont">
	<?php $form = $this->beginWidget(
		'CActiveForm',
		array(
			'id'                   => 'appointment-form',
			'enableAjaxValidation' => false,
			'htmlOptions'          => array(),
		)
	); ?>
	<div class="pop-inp_marg">
		<div class="pop-head-inp">Как Вас зовут?</div>
		<?php echo LfHtml::activeTextField($model, 'name'); ?>
	</div>
	<div class="pop-inp_marg spec-selector">
		<div class="pop-head-inp">Вид услуги:</div>
		<?php echo LfHtml::activeDropDownList(
			$model,
			'specialization_id',
			LfSpecialization::model()->getListItems(),
			[
				'empty' => '',
			]
		); ?>
	</div>
	<div class="pop-inp_marg service-selector">
		<div class="pop-head-inp">Подвид услуги:</div>
		<?php echo LfHtml::activeDropDownList($model, 'service_id', LfService::model()->getListItems()); ?>
	</div>

	<div class="pop-inp_marg">
		<div class="pop-head-inp">Ваш телефон:</div>
		<?php echo LfHtml::activeTextField($model, 'phone'); ?>
	</div>

	<div class="pop-inp_marg">
		<div class="pop-head-inp">Удобное месторасположение:</div>
		<div class="form-inp appointment-metro-inp">
			<div class="form-select form-placeholder" style="top: 6px;">
				Введите название станции
			</div>
			<input type="text" value="<?php echo Yii::app()->request->getPost('metro-suggest') ?>" name="metro-suggest"
				   id="appointment-metro-suggest" class="suggest-input"/>
		</div>
	</div>

	<?php echo LfHtml::activeHiddenField($model, 'underground_station_id'); ?>

	<div class="pop-inp_marg" id="popup-appointment_check">
		<?php echo LfHtml::activeCheckBox(
			$model,
			'departure',
			array('id' => 'departure', 'label' => 'Выезд на дом')
		); ?>
	</div>

	<?php
	echo CHtml::hiddenField("gaReceiver", $model->getGaReceiver($gaType));
	echo CHtml::hiddenField("gaText", $model->getGaText($gaType));
	?>
	<div style="text-align:center">
		<div class="button button-pink"><span>Записаться</span></div>
	</div>
	<?php $this->endWidget(); ?>
</div>

<script type="text/javascript">
	$(function () {
		appointmentSuggest.formId = "appointment-form";
		appointmentSuggest.initMetro("appointment-metro-suggest", 'LfAppointment_underground_station_id', false);

		$('body').trigger('specs');

		if ($("#popup-appointment_check input").prop("checked")) {
			var idCheckAppoint = $("#popup-appointment_check input").closest(".form-inp_check").data("check-id");
			$("#i-check_" + idCheckAppoint).addClass("checked");
			$("#popup-appointment_add").show();
		}

		$("#LfAppointment_phone").mask("+7 (999) 999 99 99", {placeholder: " "});

		$("#popup-appointment_check .form-inp_check").click(function () {
			if ($("#inp-check_" + $(this).data("check-id")).prop("checked"))
				$("#popup-appointment_add").hide();
			else
				$("#popup-appointment_add").show();
		});

		var $form = $('#appointment-form'),
			submitted = false;

		$form.submit(function (e) {
			e.preventDefault();

			if (submitted) return;
			submitted = true;

			$.ajax({
				url: $form.attr('action'),
				type: $form.attr('method'),
				dataType: 'html',
				data: $form.serializeArray()
			}).done(function (response) {
				$('#popup').html(response);
				setPopupPosition();
			});
		})
			.find('.button').click(function () {
				$form.submit();
			});
		$('.form-placeholder').each(function() {
			var input = $(this).parent().find("input");
			if(input.val() != '') {
				$(this).hide();
			}
		});
	});
</script>