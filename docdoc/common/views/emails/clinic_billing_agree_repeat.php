<?php
/**
 * @var dfs\docdoc\models\ClinicBillingModel $billing
 * @var string $period
 * @var float $startSum
 * @var dfs\docdoc\models\MailQueryModel $mail
 */

$mail->subj = "Перевыставить счет для  клиники {$billing->clinic->short_name} за период {$period}";

?>
Для клиники <?=$billing->clinic->short_name?> (ID <?=$billing->clinic->id?> ) за период <?=$period?> был выставлен счет на сумму <?=$startSum?>.<br>
Счет нужно перевыставить на сумму <?=$billing->agreed_sum?>.<br>
Тариф <?=$billing->tariff->contract->title?>

