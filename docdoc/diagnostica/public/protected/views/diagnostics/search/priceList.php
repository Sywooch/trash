<?php

use dfs\docdoc\models\ContractModel;

/**
 * @var dfs\docdoc\models\ClinicModel $model
 * @var array $parentDiagnostics
 * @var array $diagnosticPrices
 * @var array $diagnosticSpecialPrices
 */

$isOnline = $model->getClinicContract(ContractModel::TYPE_DIAGNOSTIC_ONLINE);

$diagnostics = array();
foreach ($model->getDiagnostics() as $diagnostic) {
	$diagnostics[$diagnostic['Id']]['id'] = $diagnostic['Id'];
	$diagnostics[$diagnostic['Id']]['name'] = $diagnostic['Name'];
	$diagnostics[$diagnostic['Id']]['pid'] = $diagnostic['Pid'];
	if ($diagnostic['ReductionName'])
		$diagnostics[$diagnostic['Id']]['rname'] = $diagnostic['ReductionName'];
	else
		$diagnostics[$diagnostic['Id']]['rname'] = $diagnostic['Name'];
}

uasort($diagnostics, function ($a, $b) {
	if ($a['pid'] == $b['pid']) {
		return 0;
	};
	return $a['pid'] < $b['pid'] ? -1 : 1;
});

/**
 * @var \dfs\docdoc\models\DiagnosticClinicModel[] $d
 */
$diags = [];
foreach ($model->diagnosticClinics as $item) {
	$diags[$item->diagnostica_id] = $item;
}
?>

<div class="clinic_detail_price_wrap">
	<h3 class="price_tbl_head">Прайс-лист на услуги</h3>
	<?php $prevPid = 0; ?>
	<?php $indexedDiagnostics = array_values($diagnostics);
	foreach ($indexedDiagnostics as $i => $diagnostic): ?>
		<?php $price = isset($diagnosticPrices[$diagnostic['id']]) ? round($diagnosticPrices[$diagnostic['id']]) : 0; ?>
		<?php $specialPrice = isset($diagnosticSpecialPrices[$diagnostic['id']]) ? round($diagnosticSpecialPrices[$diagnostic['id']]) : 0; ?>
		<?php $priceOnline = isset($onlinePrices[$diagnostic['id']]) ? round($onlinePrices[$diagnostic['id']]) : 0; ?>
		<?php if (!empty($price) && $price != 0): ?>
			<?php if ($diagnostic['pid'] != 0 && $diagnostic['pid'] != $prevPid): ?>
				<div class="price_tbl_title price_tbl_head-sub"><?php
					$return = '';
					foreach ($diagnostics as $index => $d) {
						if ($d['id'] === $diagnostic['pid']) {
							$return = $d['rname'];
						}
					}
					echo $return;
					?>
					<?php echo $parentDiagnostics[$diagnostic['pid']];?>
				</div>

				<div class="price_tbl_sub">
				<table class="price_tbl" cellspacing="0" cellpadding="0">
			<?php elseif ($diagnostic['pid'] === '0' && !empty($price)): ?>
				<table class="price_tbl" cellspacing="0" cellpadding="0">
			<?php endif; ?>
			<tr<?php echo $isOnline ? ' class="request-online js-request-link js-tooltip-tr"' : ''; ?>
				<?php if ($model->discount_online_diag && $isOnline && $priceOnline > 0) {
					echo 'title="Дополнительная скидка ' . $diags[$diagnostic['id']]->getDiscountForOnline() . '% при онлайн записи на эту услугу!"';
				} elseif ($isOnline) {
					echo 'title="Нажмите, чтобы записаться на эту услугу"';
				}?>
				data-diagnostic-id="<?php echo $diagnostic['id']; ?>">
				<td>
									<span
										class="price_tbl_name <?php echo ($diagnostic["pid"] === "0" && !empty($price)) ? "price_tbl_title" : ""; ?>"><?php echo $diagnostic['name']; ?></span>
				</td>
				<td class="price_tbl_price_wrap">
                    <span class="price_tbl_price">
                    <?php if ($specialPrice > 0): ?>
						<?php echo '<strike>' . $price . ' р.</strike> '; ?><?=$specialPrice?> руб.
					<?php else: ?>
						<?php echo $price; ?> руб.
					<?php endif; ?>
                    </span>
				</td>
			</tr>
			<?php if (!empty($specialPrice)): ?>
				<tr class="price_tbl_spec">
					<td colspan="2">спец. цена на docdoc.ru</td>
				</tr>
			<?php endif; ?>
		<?php endif; ?>
		<?php
		$nextEl = isset($indexedDiagnostics[$i + 1]) ? $indexedDiagnostics[$i + 1] : array();
		if ((!$nextEl ||
				($nextEl['pid'] != 0 && $nextEl['pid'] != $diagnostic['pid']) ||
				($diagnostic['pid'] === '0' && !empty($price))
			) && !empty($price)
		):?>

							</table>
							<?php if (!($diagnostic['pid'] === '0' && !empty($price))): ?>
			</div>
		<?php endif; ?>
		<?php endif; ?>
		<?php $prevPid = $diagnostic['pid']; ?>
	<?php endforeach; ?>
</div>
