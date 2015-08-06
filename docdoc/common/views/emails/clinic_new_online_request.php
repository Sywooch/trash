<?php
/**
 * @var \dfs\docdoc\models\MailQueryModel $mail
 * @var \dfs\docdoc\models\RequestModel $request
 * @var \dfs\docdoc\models\AuthTokenModel $authToken
 */

$client = $request->client;
$diagnostic = $request->diagnostics;
$diagnosticClinic = $request->diagnosticClinic;
$clinic = $request->clinic;
$parentDiagnostic = $diagnostic ? $diagnostic->parent : null;
$date = date('d.m.Y H:i', $request->date_admission);
$host = Yii::app()->params['hosts']['front'];

$mail->subj = "[DocDoc] Online-запись на диагностику (№{$request->req_id})";
?>
В Вашу клинику поступила заявка на запись.<br />
<br />
Идентификатор заявки: <?php echo $request->req_id; ?><br />
Время создания заявки: <?php echo date('d.m.Y H:i:s', $request->req_created); ?><br />
Имя пациента: <?php echo $client->name; ?><br />
Телефон: <?php echo $client->phone; ?><br />

<?php if ($diagnostic): ?>
	Тип диагностики: <?php echo $parentDiagnostic ? $parentDiagnostic->getFullName() : $diagnostic->getFullName(); ?><br />
<?php endif; ?>

<?php if ($parentDiagnostic): ?>
	Вид услуги: <?php echo $diagnostic->getFullName(); ?><br />
<?php endif; ?>

<?php if ($diagnosticClinic): ?>
	Стоимость услуги для клиентов DocDoc: <?php echo number_format($diagnosticClinic->special_price > 0  ? $diagnosticClinic->special_price  : $diagnosticClinic->price, 0, ',', ' ');?> р.<br />
	<?php if ($clinic->discount_online_diag > 0 && $diagnosticClinic->price_for_online > 0): ?>
		Стоимость услуги для клиентов, записавшихся через кнопку "Запись": <?php echo number_format($diagnosticClinic->price_for_online, 0, ',', ' ');?> р.<br />
	<?php endif; ?>
<?php endif; ?>

Клиника: <?php echo $request->clinic->name; ?><br />
Желаемая дата приёма: <?php echo $date; ?><br />
<br />
<br />
<b>Что нужно сделать:</b><br />
1. В течение 5 минут связаться с пациентом для подтверждения записи: <?php echo $client->phone; ?> (<?php echo $client->name; ?>)<br />
2. Результат обработки заявки нужно сохранить нажатием на одну из кнопок:<br />

<?php if ($authToken): ?>
<br />
<a href="https://<?php echo $host; ?>/lk/requests/changeState?action=accept&requestId=<?php echo $request->req_id; ?>&authToken=<?php echo $authToken->token; ?>">
	Принять заявку
</a>
&nbsp;&nbsp;&nbsp;
<a href="https://<?php echo $host; ?>/lk/requests/changeState?action=refused&requestId=<?php echo $request->req_id; ?>&authToken=<?php echo $authToken->token; ?>">
	Отклонить заявку
</a>
<br />
<?php endif; ?>
