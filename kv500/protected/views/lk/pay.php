<?php $this->renderPartial("_header", compact("model")); ?>

<div class="lk-payment">
	<p>Сумма для оплаты: <strong><?php echo $balanceAdd; ?>$</strong></p>

	<?php

	//Секретный ключ интернет-магазина
	$key = "575878487278714632654546624a42685f4a61783137636f394948";

	$fields = array();

	// Добавление полей формы в ассоциативный массив
	$fields["WMI_MERCHANT_ID"]    = "163388444121";
	$fields["WMI_PAYMENT_AMOUNT"] = $balanceAdd / PAY_DIFF;
	$fields["WMI_CURRENCY_ID"]    = "840";
	$fields["WMI_PAYMENT_NO"]     = uniqid();
	$fields["WMI_DESCRIPTION"]    = "BASE64:".base64_encode("Квартал 500");
	$fields["WMI_SUCCESS_URL"]    = "http://kv500.com/payment/payment/";
	$fields["WMI_FAIL_URL"]       = "http://kv500.com/lk/";

	//Сортировка значений внутри полей
	foreach($fields as $name => $val)
	{
		if (is_array($val))
		{
			usort($val, "strcasecmp");
			$fields[$name] = $val;
		}
	}

	// Формирование сообщения, путем объединения значений формы,
	// отсортированных по именам ключей в порядке возрастания.
	uksort($fields, "strcasecmp");
	$fieldValues = "";

	foreach($fields as $value)
	{
		if (is_array($value))
			foreach($value as $v)
			{
				//Конвертация из текущей кодировки (UTF-8)
				//необходима только если кодировка магазина отлична от Windows-1251
				$v = iconv("utf-8", "windows-1251", $v);
				$fieldValues .= $v;
			}
		else
		{
			//Конвертация из текущей кодировки (UTF-8)
			//необходима только если кодировка магазина отлична от Windows-1251
			$value = iconv("utf-8", "windows-1251", $value);
			$fieldValues .= $value;
		}
	}

	// Формирование значения параметра WMI_SIGNATURE, путем
	// вычисления отпечатка, сформированного выше сообщения,
	// по алгоритму MD5 и представление его в Base64

	$signature = base64_encode(pack("H*", md5($fieldValues . $key)));

	//Добавление параметра WMI_SIGNATURE в словарь параметров формы

	$fields["WMI_SIGNATURE"] = $signature;

	// Формирование HTML-кода платежной формы

	print '<form action="https://www.walletone.com/checkout/default.aspx" method="POST">';

  foreach($fields as $key => $val)
  {
	  if (is_array($val))
		  foreach($val as $value)
		  {
			  print "<input type='hidden' name='$key' value='$value'/>
";
         }
	  else
		  print "<input type='hidden' name='$key'' value='$val'/>
";
  }

  print "<input type='submit' class='btn' value='Оплатить' /></form>";
  ?>

</div>