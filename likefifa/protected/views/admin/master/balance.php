<?php
/**
 * @var LfMaster         $model
 * @var AdminsController $this
 */
?>

<span class="master-balance master-<?php echo $model->id; ?>-balance"><?php echo $model->getBalance(); ?> <i class="fa fa-rub"></i></span>
<span class="master-balance-buttons">
<?php
echo CHtml::ajaxLink(
	'<i class="fa fa-plus-square-o"></i>',
	$this->createUrl("popup/recharge", array("is_addition" => true, "master_id" => $model->id)),
	array(
		"success" => 'function(data) {
			$(".modal").empty().html(data).modal("show");
		}',
	),
	array(
		'data-toggle' => 'tooltip',
		"title"       => "Пополнить баланс",
	)
);
?>

<?php
echo CHtml::ajaxLink(
	'<i class="fa fa-minus-square-o"></i>',
	$this->createUrl("popup/recharge", array("is_addition" => false, "master_id" => $model->id)),
	array(
		"success" => 'function(data) {
			$(".modal").empty().html(data).modal("show");
		}',
	),
	array(
		'data-toggle' => 'tooltip',
		"title"       => "Уменьшить баланс",
	)
);
?>

<?php
echo CHtml::ajaxLink(
	'<i class="fa fa-money"></i>',
	$this->createUrl("popup/masterTransactions", array("master_id" => $model->id)),
	array(
		"success" => 'function(data) {
			$(".modal").empty().html(data).modal("show");
		}',
	),
	array(
		'data-toggle' => 'tooltip',
		"title"       => "История транзакций",
	)
);
?>
</span>