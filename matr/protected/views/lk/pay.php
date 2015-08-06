<?php $this->renderPartial("_header", compact("model")); ?>

<div class="lk-payment">
	<p>Сумма для оплаты: <strong><?php echo $balanceAdd * DOLLAR; ?> рублей</strong></p>

	<?php if (Yii::app()->session['payTo'] == 2) { ?>
		<?php
		$m_shop = '26975491';
		$m_orderid = '1';
		$m_amount = number_format($balanceAdd / PAY_DIFF * DOLLAR, 2, '.', '');
		$m_curr = 'RUB';
		$m_desc = base64_encode('Русская матрешка');
		$m_key = 'vT2WzoKy3dLOx6YF';

		$arHash = array(
			$m_shop,
			$m_orderid,
			$m_amount,
			$m_curr,
			$m_desc,
			$m_key
		);
		$sign = strtoupper(hash('sha256', implode(':', $arHash)));
		?>
		<form method="GET" action="//payeer.com/merchant/">
			<input type="hidden" name="m_shop" value="<?=$m_shop?>">
			<input type="hidden" name="m_orderid" value="<?=$m_orderid?>">
			<input type="hidden" name="m_amount" value="<?=$m_amount?>">
			<input type="hidden" name="m_curr" value="<?=$m_curr?>">
			<input type="hidden" name="m_desc" value="<?=$m_desc?>">
			<input type="hidden" name="m_sign" value="<?=$sign?>">
			<input type="submit" name="m_process" value="Оплатить" class='btn' />
		</form>
	<?php } ?>
</div>