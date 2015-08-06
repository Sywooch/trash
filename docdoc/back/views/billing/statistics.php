<?php
/**
 * @var string $dateFrom
 * @var string $dateTo
 * @var array $stat
 * @var \dfs\docdoc\models\ClinicContractModel $tariff
 */
?>

<h2><a href="/2.0/clinicBilling/?dateFrom=<?=$dateFrom?>"><b>Назад к списку клиник</b></a></h2>

<p><b>Период:</b> c <?=date('d.m.y', strtotime($dateFrom))?> по <?=date('d.m.y', strtotime($dateTo))?></p>

<?php if ($tariff): ?>

	<p><b>Город:</b> <?php echo $tariff->clinic->clinicCity->title; ?></p>
	<p><b>Тариф:</b> <?php echo $tariff->contract->title; ?></p>

	<?php foreach ($stat as $item): ?>
		<p><b>Заявок с типом "<?=$item['groupName']?>":</b> <?=$item['requests']?></p>
		<p><b>Тариф заявок с типом "<?=$item['groupName']?>":</b> <?=$item['requestCost']?></p>
		<p><b>Итого сумма для заявок с типом "<?=$item['groupName']?>":</b> <?=$item['cost']?></p>
	<?php endforeach; ?>

	<p><b>Всего заявок в биллинге:</b> <?=$tariff->getTotalRequestNumInBilling($dateFrom, $dateTo . " 23:59:59")?></p>
	<p><b>Общая сумма:</b> <?=$tariff->getTotalRequestCostInBilling($dateFrom, $dateTo . " 23:59:59")?></p>

<?php endif; ?>
