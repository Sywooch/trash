<?php
use dfs\docdoc\models\RequestModel;

?>
<h1>Пересчет заявок</h1>

<h2><a href="/2.0/clinic/list/?dateFrom=<?=$dateFrom?>&dateTo=<?=$dateTo?>"><b>Назад к списку клиник</b></a></h2>

<?php

foreach ($clinicRequests as $tariffTitle => $requests) {

?>
<br/>
<h2><?=$tariffTitle?></h2>

<table>
	<tr>
		<th>#</th>
		<th>Заявка</th>
		<th>Тип</th>
		<th>Услуга</th>
		<th>Стоимость</th>
		<th>Статус</th>
		<th>Дата биллинга</th>
	</tr>
<?php
$i = 1;
foreach ($requests as $r) {

	$kind = ($r->kind == RequestModel::KIND_DOCTOR) ? "Врач" : "Диагностика";

	$service = ($r->kind == RequestModel::KIND_DIAGNOSTICS) ? $r->diagnostics : $r->sector;

	$serviceName = "Все услуги";
	if ($service != null) {
		$serviceName = ($r->kind == RequestModel::KIND_DIAGNOSTICS) ? $service->getFullName() : $service->name;
	}

	$billing = $r->getBillingStatusName();

	$billingDate = $r->getBillingDate();

	echo "<tr>
			<td>{$i}</td><td>{$r->req_id}</td><td>{$kind}</td>
			<td>{$serviceName}</td><td>$r->request_cost</td><td>{$billing}</td>
			<td>{$billingDate}</td>
		</tr>";
	$i++;
}
?>
</table>

<?php
}

?>
