<?php
/**
 * @var LfMaster $model
 */
?>
<div class="modal-dialog modal-sm recharge-popup">
	<form action="" method="post">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span><span class="sr-only">Закрыть</span>
				</button>
				<h4 class="modal-title" id="myModalLabel">Пополнение баланса для мастера №<?php echo $model->id; ?></h4>
			</div>
			<div class="modal-body">
				<label style="display: block;">Введите сумму, на которую хотите увеличить баланс</label>
				<?php echo CHtml::textField("addBalance", '', ['class' => 'form-control']); ?> руб.
			</div>
			<?php echo CHtml::hiddenField("masterIdRecharge", $model->id); ?>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
				<button type="submit" class="btn btn-primary recharge-balance-submit">Пополнить</button>
			</div>
		</div>
	</form>
</div>