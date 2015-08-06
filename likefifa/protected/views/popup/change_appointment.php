<div class="popup-app_head">
	Выберите мастера или салон
	<div class="popup-close"></div>
</div>
<div class="popup-app_cont">
	<div class="ui-widget">
		<?php echo CHtml::form(Yii::app()->createUrl("admin/appointment")); ?>

		<select id="combobox" appointment="" name="change">
			<option value='0'></option>
			<?php foreach (LfMaster::model()->getListItems() as $id => $name) { ?>
				<option value="m<?php echo $id; ?>"><?php echo $name; ?></option>
			<?php } ?>
			<option value='0'>-----------------------------------------</option>
			<?php foreach (LfSalon::model()->getListItems() as $id => $name) { ?>
				<option value="s<?php echo $id; ?>"><?php echo $name; ?></option>
			<?php } ?>
		</select>

		<?php echo CHtml::hiddenField("appointmentId", $appointmentId, array("id" => "appointmentId")); ?>

		<?php echo CHtml::submitButton("Сохранить", array("class" => "s_button")); ?>
		<?php echo CHtml::endForm(); ?>
	</div>
</div>

<script>
	$( "#combobox" ).combobox();
	$( "#toggle" ).click(function() {
		$( "#combobox" ).toggle();
	});
</script>