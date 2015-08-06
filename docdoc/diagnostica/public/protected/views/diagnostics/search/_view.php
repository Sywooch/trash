<?php
/**
 * Краткая анкета клиники
 */
use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\ContractModel;
use dfs\docdoc\components\Partner;


$isOnline = $data->getClinicContract(ContractModel::TYPE_DIAGNOSTIC_ONLINE);

/**
 * Альтернативная версия страницы
 */
$isInBTest = Yii::app()->referral->isABTest() === Partner::AB_TEST_B;

$advert = false;

//если на вход приходит массив преобразуем его в модель Clinic
if (is_array($data)) {
	$attributes = $data;
	$data = ClinicModel::model();
	$data->setAttributes($attributes, false);
}

if (isset($_GET['page'])) {
	$index = 10 * ($_GET['page'] - 1) + $index;
}

$prices = Diagnostica4clinic::model()->count("diagnostica_id=:id", ["id" => $data->id]);

$scheduleTime = $data->getScheduleTime();

?>

<article
	class="clinic_card<?php
		echo $advert ? ' result-item-adv' : '';
		echo in_array($data->id, $this->advertisements) ? ' clinic_card_recommend' : '';
		echo $isInBTest ? ' online_booking_first' : '';
	?>">
	<div class="clinic_person">
		<a class="clinic_img_link <?php echo in_array($data->id, $this->advertisements) ? 'js-tooltip-tr" title="Рекомендовано порталом DocDoc.ru' : ''; ?>"
		   href="<?php echo $this->createUrl('diagnostics' . '/search/guess', ['rewriteName' => $data->rewrite_name ?: $data->id]); ?>">
			<?php echo in_array($data->id, $this->advertisements) ? '<span class="clinic_img_link__recommend"></span>' : ''; ?>
			<img alt="<?php $data->name; ?>" title="<?php $data->name; ?>" src="<?php echo $data->getLogo(); ?>" width="120"
			     class="clinic_img"/>
		</a>
	</div>
	<div class="clinic_info">
	<div class="clinic_info_aside">

		<?php if (!$isInBTest || !$isOnline): ?>
		<div class="clinic_request_phone">
			Запись по телефону
			<div class="clinic_request_phone_num">
				<?php if (!$data->isPayForDiagnostic()) { ?>
					<a
						href="#"
						class="show-clinic-phone"
						data-id="<?php echo $data->id; ?>"
						data-name="<?php echo $data->name; ?>"
						data-phone="<?php echo $data->diagnosticPhone()->prettyFormat(); ?>"
						title="Нажмите, чтобы увидеть телефон"
						><?php echo $data->diagnosticPhone()->getIncompleteNumber(); ?></a>
				<?php } else { ?>
					<?php echo $data->diagnosticPhone()->prettyFormat(); ?>
				<?php } ?>
			</div>
		</div>
		<?php endif; ?>

		<?php if ($isOnline): ?>

			<?php if ($data->discount_online_diag) {?>
			<div class="btn-discount-wrap js-tooltip-tr" title="При онлайн записи вы получаете дополнительную скидку <?=$data->discount_online_diag?>%">
			<?php }?>
			
			<input
				class="req_submit ui-btn ui-btn_teal js-request-popup js-popup-tr online-diagnostics-open-modal"
				type="submit"
				value="<?=$isInBTest?'Онлайн запись':'Записаться'?>"
				data-popup-id="js-popup-request-clinic"
				data-popup-width="734"
				<?php if ($scheduleTime): ?>
					data-clinic-min-time="<?php echo $scheduleTime['start_time']; ?>"
					data-clinic-max-time="<?php echo $scheduleTime['end_time']; ?>"
				<?php endif; ?>
				data-clinic-name="<?php echo htmlspecialchars($data->name); ?>"
				data-clinic-address="<?php echo htmlspecialchars($data->address); ?>"
				data-clinic-id="<?php echo $data->id;?>"
				data-clinic-phone="<?php echo $data->diagnosticPhone()->prettyFormat(); ?>"
				data-clinic-discount-online="<?=$data->discount_online_diag?>"
				data-validate-phone="<?php echo (int)$data->validate_phone; ?>" />

			<?php if ($data->discount_online_diag) {?>
				<div class="btn-label-discount">-<?=$data->discount_online_diag?>%</div>
			</div>
			<?php }?>

			<?php if ($isInBTest): ?>

				<div class="clinic_request_phone mode2">
					или по телефону
					<div class="clinic_request_phone_num">
						<?php if (!$data->isPayForDiagnostic()) { ?>
							<a
								href="#"
								class="show-clinic-phone"
								data-id="<?php echo $data->id; ?>"
								data-name="<?php echo $data->name; ?>"
								data-phone="<?php echo $data->diagnosticPhone()->prettyFormat(); ?>"
								title="Нажмите, чтобы увидеть телефон"
								><?php echo $data->diagnosticPhone()->getIncompleteNumber(); ?></a>
						<?php } else { ?>
							<?php echo $data->diagnosticPhone()->prettyFormat(); ?>
						<?php } ?>
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		<div class="clinic_work_time">
			<p>График работы центра:</p>
			<?php
				foreach ($data->getSchedule() as $v) {
					echo '<div class="l-ib">' . $v['DayTitle'] . ':</div> ' . $v['StartTime'] . '-' . $v['EndTime'] . '<br/>';
				}
			?>

			<div class="clinic_link_map">
				<a href="/map.php?id=<?php echo $data->id . ($this->parentDiagnostic ? '&diagnostic=' . $this->parentDiagnostic->id : '') . ($this->diagnostic ? '&subDiagnostica=' . $this->diagnostic->id : ''); ?>">показать на карте</a>
			</div>
		</div>
	</div>
	<h2 class="clinic_name"><a
			href="<?php echo $this->createUrl('/kliniki/' . $data->rewrite_name . '/'); ?>"><?php echo $data->name; ?></a>
	</h2>
<?php
if ($data->stations) {
	echo '<ul class="metro_list">';
	$stations = [];
	foreach ($data->stations as $station) {

		if (!$this->isLandingPage) {
			$urlParams = ['stations' => [$station->id]];
			if ($this->diagnostic) {
				$urlParams['rewriteName'] = $diagnostic->rewrite_name;
			}

			if ($this->diagnostic) {
				$href = "/{$this->parentDiagnostic->getRewriteName()}/{$this->diagnostic->getRewriteName()}/station/{$station->rewrite_name}";
			} elseif ($this->parentDiagnostic) {
				$href = "/{$this->parentDiagnostic->getRewriteName()}/station/{$station->rewrite_name}";
			} else {
				$href = "/station/{$station->rewrite_name}";
			}
			$stations[] = '<li class="metro_item" data-dist="' . $station->getDistanceToClinic($data->id) . '" data-line-id="' . $station->underground_line_id . '">' .
				'<a class="metro_link metro_line_' . $station->underground_line_id . '" href="' . $href . '"> ' . $station->name . '</a></li>';
		} else {
			$stations[] = "<li class='metro_item' data-dist='{$station->getDistanceToClinic($data->id)}' data-line-id='{$station->underground_line_id}'>" .
				"<span class='metro_link metro_line_{$station->underground_line_id}'>{$station->name}</span></li>";
		}

	}
	echo $stations ? implode(', ', $stations) : '';
	echo "</ul>";
}
?>
<span class="link-adress js-ymap-tr"><?php echo $data->getAddress(); ?></span>

<div data-mobile="0"
	 data-longitude="<?php echo $data->longitude; ?>"
	 data-latitude="<?php echo $data->latitude; ?>"
     data-adress="<?php echo $data->address; ?>"
	 class="js-ymap js-ymap-data"
	 id="map_<?php echo $data->id; ?>"></div>
<?php if (!in_array($data->id, $this->advertisements)): ?>
	<p data-ellipsis-height="40" class="clinic_desc"><?php echo $data->shortDescription; ?></p>
<?php endif; ?>

<?php
if ($diagnostic) {
	$diagnosticSearch = $diagnostic;
} else {
	$diagnosticSearch = null;
}
$diagnostics = [];
$diagModels = $data->cache(3600)->diagnosticClinics;
$diagnosticPrices = CHtml::listData($diagModels, 'diagnostica_id', 'price');
$diagnosticSpecialPrices = CHtml::listData($diagModels, 'diagnostica_id', 'special_price');
$onlinePrices = CHtml::listData($diagModels, 'diagnostica_id', 'price_for_online');

/**
 * @var \dfs\docdoc\models\DiagnosticClinicModel[] $d
 */
$d = [];
foreach ($data->diagnosticClinics as $item) {
	$d[$item->diagnostica_id] = $item;
}

$showPrice = false;
foreach ($diagnosticPrices as $price) {
	if ($price)
		$showPrice = true;
}

if ($diagnosticSearch) {
	foreach ($data->getDiagnosticsWithPrices() as $diagnostic) {
		if ($diagnosticSearch->id == $diagnostic['parent_id'] || $diagnosticSearch->id == $diagnostic['id']) {
			$diagnostics[$diagnostic['id']] = $diagnostic;
		}
	}
} else {
	foreach ($data->getDiagnosticsWithPrices() as $diagnostic) {
		$diagnostics[$diagnostic['id']] = $diagnostic;
	}
}

$diagnosticsFirst = [];
$diagnosticsOther = [];
$i = 0;

foreach ($diagnostics as $diag) {
	$diag['pid'] = $diag['parent_id'];
	if (!empty($diagnosticPrices[$diag['id']])) {
		if ($i < 3) {
			$diagnosticsFirst[$diag['id']] = $diag;
		} elseif ($i > 2) {
			$diagnosticsOther[$diag['id']] = $diag;
		}
		$i++;
	}
}
?>

<?php if ($showPrice) { ?>
	<?php if (!$diagnosticSearch) { ?>
		<table class="<?php echo !$advert ? 'price_tbl' : ''; ?> " cellspacing="0" cellpadding="0">
			<?php foreach ($diagnosticsFirst as $diagnostic) { ?>
				<?php $price = round($diagnosticPrices[$diagnostic['id']]); ?>
				<?php $specialPrice = round($diagnosticSpecialPrices[$diagnostic['id']]); ?>
				<?php $priceOnline = round($onlinePrices[$diagnostic['id']]); ?>
				<?php if ($price) { ?>
					<tr<?php echo $isOnline ? ' class="request-online js-request-link js-tooltip-tr"' : ''; ?>
						<?php if ($data->discount_online_diag && $isOnline && $priceOnline > 0) {?>
							<?php echo 'title="Дополнительная скидка ' . $d[$diagnostic['id']]->getDiscountForOnline() . '% при онлайн записи на эту услугу!"';?>
						<?php } elseif ($isOnline) {?>
							<?php echo 'title="Нажмите, чтобы записаться на эту услугу"';?>
						<?php }?>
						data-diagnostic-id="<?php echo $diagnostic['id']; ?>">
						<td>
							<span class="price_tbl_name">
								<?php echo $diagnostic['parent_short_name'] . ' ' . $diagnostic['name']; ?>
							</span>
						</td>
						<td class="price_tbl_price_wrap">
							<span class="price_tbl_price">
							<?php if ($specialPrice > 0) { ?>
								<?php echo
									'<strike>' .
									$price .
									' р.</strike> '; ?><?=$specialPrice?> руб.
							<?php } else { ?>
								<?php echo $price; ?> руб.
							<?php } ?>
							</span>
						</td>
					</tr>
					<?php if ($specialPrice > 0) { ?>
						<tr class="price_tbl_spec">
							<td colspan="2">спец. цена на docdoc.ru</td>
						</tr>
					<?php } ?>
				<?php } ?>
			<?php } ?>
		</table>
		<div class="price_tbl_hidden" data-price-id="<?php echo $data->id; ?>">
			<table class="<?php echo !$advert ? 'price_tbl' : ''; ?> " cellspacing="0" cellpadding="0">
				<?php foreach ($diagnosticsOther as $diagnostic) { ?>
					<?php $price = round($diagnosticPrices[$diagnostic['id']]); ?>
					<?php $specialPrice = round($diagnosticSpecialPrices[$diagnostic['id']]); ?>
					<?php $priceOnline = round($onlinePrices[$diagnostic['id']]); ?>
					<?php if ($diagnostic['pid'] != 0 && !empty($price) && $price != 0) { ?>
						<tr<?php echo $isOnline ? ' class="request-online js-request-link js-tooltip-tr"' : ''; ?>
							<?php if ($data->discount_online_diag && $isOnline && $priceOnline > 0) {?>
								<?php echo 'title="Дополнительная скидка ' . $d[$diagnostic['id']]->getDiscountForOnline() . '% при онлайн записи на эту услугу!"';?>
							<?php } elseif ($isOnline) {?>
								<?php echo 'title="Нажмите, чтобы записаться на эту услугу"';?>
							<?php }?>
							data-diagnostic-id="<?php echo $diagnostic['id']; ?>">
							<td>
								<span
									class="price_tbl_name"><?php echo $diagnostic['parent_short_name'] . ' ' . $diagnostic['name']; ?></span>
							</td>
							<td class="price_tbl_price_wrap">
								<span class="price_tbl_price">
								<?php if ($specialPrice > 0): ?>
									<?php echo '<strike>' . $price . ' р.</strike> '; ?><?=$specialPrice?> руб.
								<?php else : ?>
									<?php echo $price; ?> руб.
								<?php endif; ?>
								</span>
							</td>
						</tr>
						<?php if ($specialPrice > 0) { ?>
							<tr class="price_tbl_spec">
								<td colspan="2">спец. цена на docdoc.ru</td>
							</tr>
						<?php } ?>
					<?php } ?>
				<?php } ?>
			</table>
		</div>
		<?php if ($diagnosticsOther): ?>
			<div class="price_tbl_show_wrap">
				<div class="price_tbl_show_btn" data-id="<?php echo $data->id; ?>">весь прайс-лист</div>
			</div>
		<?php endif; ?>
	<?php } else { ?>
		<?php if (!$this->oneDiagnostic) { ?>
			<table class="<?php echo !$advert ? 'price_tbl' : ''; ?>" cellspacing="0" cellpadding="0">
				<?php foreach ($diagnosticsFirst as $diagnostic): ?>
					<?php $price = round($diagnosticPrices[$diagnostic['id']]); ?>
					<?php $specialPrice = round($diagnosticSpecialPrices[$diagnostic['id']]); ?>
					<?php $priceOnline = round($onlinePrices[$diagnostic['id']]); ?>
					<?php if ($price): ?>
						<tr<?php echo $isOnline ? ' class="request-online js-request-link js-tooltip-tr"' : ''; ?>
							<?php if ($data->discount_online_diag && $isOnline && $priceOnline > 0) {?>
								<?php echo 'title="Дополнительная скидка ' . $d[$diagnostic['id']]->getDiscountForOnline() . '% при онлайн записи на эту услугу!"';?>
							<?php } elseif ($isOnline) {?>
								<?php echo 'title="Нажмите, чтобы записаться на эту услугу"';?>
							<?php }?>
							data-diagnostic-id="<?php echo $diagnostic['id']; ?>">
							<td><span
									class="price_tbl_name"><?php echo $diagnosticSearch->getParentName() . (empty($diagnostic['parent_id']) ? '' : ' ' . $diagnostic['name']); ?></span>
							</td>
							<td class="price_tbl_price_wrap">
								<span class="price_tbl_price">
								<?php if ($specialPrice > 0) { ?>
									<?php echo '<strike>' . $price . ' р.</strike> '; ?><?=$specialPrice?> руб.
								<?php } else { ?>
									<?php echo $price; ?> руб.
								<?php } ?>
								</span>
							</td>
						</tr>
						<?php if ($specialPrice > 0): ?>
							<tr class="price_tbl_spec">
								<td colspan="2">спец. цена на docdoc.ru</td>
							</tr>
						<?php endif; ?>
					<?php endif; ?>
				<?php endforeach; ?>
			</table>
			<div class="price_tbl_hidden" data-price-id="<?php echo $data->id; ?>">
				<table class="<?php echo !$advert ? 'price_tbl' : ''; ?> " cellspacing="0" cellpadding="0">
					<?php foreach ($diagnosticsOther as $diagnostic) { ?>
						<?php $price = round($diagnosticPrices[$diagnostic['id']]); ?>
						<?php $specialPrice = round($diagnosticSpecialPrices[$diagnostic['id']]); ?>
						<?php $priceOnline = round($onlinePrices[$diagnostic['id']]); ?>
						<?php if ($diagnostic['pid'] != 0 && !empty($price) && $price != 0) { ?>
							<tr<?php echo $isOnline ? ' class="request-online js-request-link js-tooltip-tr"' : ''; ?>
								<?php if ($data->discount_online_diag && $isOnline && $priceOnline > 0) {?>
									<?php echo 'title="Дополнительная скидка ' . $d[$diagnostic['id']]->getDiscountForOnline() . '% при онлайн записи на эту услугу!"';?>
								<?php } elseif ($isOnline) {?>
									<?php echo 'title="Нажмите, чтобы записаться на эту услугу"';?>
								<?php }?>
								data-diagnostic-id="<?php echo $diagnostic['id']; ?>">
								<td><span
										class="price_tbl_name"><?php echo $diagnosticSearch->getParentName() . ' ' . $diagnostic['name']; ?></span>
								</td>
								<td class="price_tbl_price_wrap">
									<span class="price_tbl_price">
									<?php if ($specialPrice > 0) { ?>
										<?php echo '<strike>' . $price . ' р.</strike> '; ?><?=$specialPrice?> руб.
									<?php } else { ?>
										<?php echo $price; ?> руб.
									<?php } ?>
									</span>
								</td>
							</tr>
							<?php if ($specialPrice > 0) { ?>
								<tr class="price_tbl_spec">
									<td colspan="2">спец. цена на docdoc.ru</td>
								</tr>
							<?php } ?>
						<?php } ?>
					<?php } ?>
				</table>
			</div>
			<?php if ($diagnosticsOther): ?>
				<div class="price_tbl_show_wrap">
					<div class="price_tbl_show_btn" data-id="<?php echo $data->id; ?>">весь прайс-лист</div>
				</div>
			<?php endif; ?>
		<?php } else { ?>
			<table class="<?php echo !$advert ? 'price_tbl' : ''; ?> " cellspacing="0" cellpadding="0">
				<?php $price = isset($diagnosticPrices[$diagnosticSearch->id]) ? round($diagnosticPrices[$diagnosticSearch->id]) : null; ?>
				<?php $specialPrice = isset($diagnosticSpecialPrices[$diagnosticSearch->id]) ? round($diagnosticSpecialPrices[$diagnosticSearch->id]) : null; ?>
				<?php $priceOnline = isset ($onlinePrices[$diagnosticSearch->id]) ? round($onlinePrices[$diagnosticSearch->id]) : null; ?>
				<?php if (!empty($price) && $price != 0) { ?>
					<tr<?php echo $isOnline ? ' class="request-online js-request-link js-tooltip-tr"' : ''; ?>
						<?php if ($data->discount_online_diag && $isOnline && $priceOnline > 0) {?>
							<?php echo 'title="Дополнительная скидка ' . $d[$diagnosticSearch->id]->getDiscountForOnline() . '% при онлайн записи на эту услугу!"';?>
						<?php } elseif ($isOnline) {?>
							<?php echo 'title="Нажмите, чтобы записаться на эту услугу"';?>
						<?php }?>
						data-diagnostic-id="<?php echo $diagnosticSearch->id; ?>">
						<td><span
								class="price_tbl_name"><?php echo $diagnosticSearch->parent_id <> 0 ? $this->parentDiagnostic['reduction_name'] . ' ' . $diagnosticSearch->name : $diagnosticSearch->name; ?></span>
						</td>
						<td class="price_tbl_price_wrap">
							<span class="price_tbl_price">
							<?php if ($specialPrice > 0) { ?>
								<?php echo '<strike>' . $price . ' р.</strike> '; ?><?=$specialPrice?> руб.
							<?php } else { ?>
								<?php echo $price; ?> руб.
							<?php } ?>
							</span>
						</td>
					</tr>
					<?php if ($specialPrice > 0) { ?>
						<tr class="price_tbl_spec">
							<td colspan="2">спец. цена на docdoc.ru</td>
						</tr>
					<?php } ?>
				<?php } ?>
			</table>
		<?php } ?>
	<?php } ?>
<?php } ?>
</div>
</article>
