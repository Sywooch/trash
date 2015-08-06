<?php

use dfs\modules\payments\models\PaymentsOperations;

/**
 * @var LfMaster           $model
 * @var PaymentsOperations $paymentsOperation
 */
?>

<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">
				<span aria-hidden="true">&times;</span><span class="sr-only">Закрыть</span>
			</button>
			<h4 class="modal-title">Транзакции для мастера №<?php echo $model->id; ?></h4>
		</div>
		<div class="modal-body">
			<table class="table table-striped table-bordered table-condensed">
				<tr>
					<th colspan="6" class="title">Операции</th>
					<th colspan="3" class="title">Счета на оплату</th>
				</tr>
				<tr>
					<th>ID</th>
					<th>Дата</th>
					<th>Сумма Реал</th>
					<th>Сумма Фейк</th>
					<th>Тип</th>
					<th>Сообщение</th>
					<th>ID счета</th>
					<th>Статус</th>
					<th>E-mail</th>
				</tr>
				<?php
				$countAmountReal = 0;
				$countAmountFake = 0;
				foreach ($model->getAccount()->paymentsOperations as $paymentsOperation) {
					?>
					<tr<?php if ($paymentsOperation->invoice_id) { ?> class="transaction-invoice"<?php } ?>>
						<td><?php echo $paymentsOperation->id; ?></td>
						<td><?php echo $paymentsOperation->getFormatDate(); ?></td>
						<td><?php echo $paymentsOperation->amount_real; ?></td>
						<td><?php echo $paymentsOperation->amount_fake; ?></td>
						<td><?php echo $paymentsOperation->getType(); ?></td>
						<td><?php echo $paymentsOperation->message; ?></td>
						<td><?php echo $paymentsOperation->invoice_id; ?></td>
						<td><?php echo $paymentsOperation->getInvoiceStatus(); ?></td>
						<td><?php echo $paymentsOperation->getInvoiceEmail(); ?></td>
					</tr>
					<?php
					$countAmountReal += $paymentsOperation->amount_real;
					$countAmountFake += $paymentsOperation->amount_fake;
				}
				if ($countAmountReal || $countAmountFake) {
					?>
					<tr>
						<td colspan="2"><strong>Итого</strong></td>
						<td><?php echo $countAmountReal; ?></td>
						<td><?php echo $countAmountFake; ?></td>
						<td colspan="5">&nbsp;</td>
					</tr>
				<?php } ?>
			</table>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
		</div>
	</div>
</div>