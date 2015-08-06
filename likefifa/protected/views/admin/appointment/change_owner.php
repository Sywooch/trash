<?php
/**
 * @var AppointmentController $this
 * @var array                 $mastersData
 * @var array                 $salonsData
 * @var integer               $appointmentId
 */
?>

<div class="modal-dialog">
	<?php echo CHtml::form(Yii::app()->createUrl("admin/appointment")); ?>
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
					class="sr-only">Закрыть</span></button>
			<h4 class="modal-title" id="myModalLabel"> Выберите мастера или салон</h4>
		</div>
		<div class="modal-body">
			<p>
				<?php echo CHtml::dropDownList('master_id', '', $mastersData, ['empty' => 'Выберите мастера']) ?>
			</p>
			<p>
				<?php echo CHtml::dropDownList('salon_id', '', $salonsData, ['empty' => 'Выберите салон']) ?>
			</p>

			<?php echo CHtml::hiddenField("appointmentId", $appointmentId, array("id" => "appointmentId")); ?>
			<?php echo CHtml::hiddenField('change', 1) ?>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
			<button type="submit" class="btn btn-primary">Сохранить</button>
		</div>
	</div>
	<?php echo CHtml::endForm(); ?>
</div>