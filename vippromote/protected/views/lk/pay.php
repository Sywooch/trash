<?php $this->renderPartial("_header", compact("model")); ?>

<div class="lk-payment">
	<p>Сумма для оплаты: <strong><?php echo $balanceAdd * DOLLAR; ?> рублей</strong></p>

	<?php if (Yii::app()->session['payTo'] == 3) { ?>
		<form method="POST" action="https://paysto.com/ru/upBalance">
			<input type="hidden" name="PAYSTO_SHOP_ID" value="21995">
			<input type="hidden" name="PAYSTO_SUM" value="<?php echo $balanceAdd / PAY_DIFF * DOLLAR; ?>">
			<input type="hidden" name="PAYSTO_PAYER_EMAIL" value="<?php echo $model->email; ?>">
			<input type="submit" name="m_process" value="Оплатить" class='btn' />
		</form>
	<?php } else if (Yii::app()->session['payTo'] == 2) { ?>
		<?php
		$m_shop = '26975491';
		$m_orderid = '1';
		$m_amount = number_format($balanceAdd / PAY_DIFF * DOLLAR, 2, '.', '');
		$m_curr = 'RUB';
		$m_desc = base64_encode('VIP-promote');
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
	<?php } else { ?>
	<form action="https://perfectmoney.is/api/step1.asp" method="POST">
		<input type="hidden" name="PAYEE_ACCOUNT" value="U7394697">
		<input type="hidden" name="PAYEE_NAME" value="VIP-PROMOTE">
		<input type="hidden" name="PAYMENT_ID" value="1">
		<input type="hidden" name="PAYMENT_AMOUNT" value="<?php echo $balanceAdd / PAY_DIFF * DOLLAR; ?>">
		<input type="hidden" name="PAYMENT_UNITS" value="USD">
		<input type="hidden" name="STATUS_URL" value="http://vip-promote.com/payment/payment/">
		<input type="hidden" name="PAYMENT_URL" value="http://vip-promote.com/payment/payment/">
		<input type="hidden" name="PAYMENT_URL_METHOD" value="POST">
		<input type="hidden" name="NOPAYMENT_URL" value="http://vip-promote.com/lk">
		<input type="hidden" name="NOPAYMENT_URL_METHOD" value="post">
		<input type="hidden" name="SUGGESTED_MEMO" value="">
		<input type="hidden" name="BAGGAGE_FIELDS" value="">
		<input type='submit' name="PAYMENT_METHOD" class='btn' value='Оплатить' />
	</form>
	<?php } ?>

</div>