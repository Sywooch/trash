<table style="width:100%; border:0px; border-collapse: collapse;">

<?php

foreach ($clinic->tariffs as $i => $tariff) {
	echo '<tr>';

	echo '<td>' . CHtml::link(
			$tariff->contract->title,
			"/2.0/billing/?dateFrom={$dateFrom}&clinicId={$clinic->id}&contractId={$tariff->contract_id}"
		) . '</td>';

	echo '<td style="width:50px;">' . $tariff->getTotalRequestNumInBilling($dateFrom, $dateTo." 23:59:59") . '</td>';

	echo '<td style="width:50px;">' . $tariff->getTotalRequestCostInBilling($dateFrom, $dateTo." 23:59:59") . '</td>';
	if ($i == 0) {
		echo '<td style="width:180px;" rowspan="' . count($clinic->tariffs) . '">' .
			CHtml::link(
				'Пересчитать стоимость заявок',
				"/2.0/clinic/recalculate/?dateFrom={$dateFrom}&dateTo={$dateTo}&clinicId={$clinic->id}&tariff={$tariff->id}"
			) .
		'</td>';
	}

	echo '</tr>';
}
?>
</table>
