<?php
/**
 * Полная анкета клиники
 */
use dfs\docdoc\models\ContractModel;
use dfs\docdoc\components\Partner;

/**
 * @var dfs\docdoc\models\ClinicModel $model
 * @var array $parentDiagnostics
 * @var array $childDiagnostics
 * @var array $diagnosticPrices
 * @var array $diagnosticSpecialPrices
 * @var array $onlinePrices
 * @var string $refUrl
 */

$scheduleTime = $model->getScheduleTime();
$isOnline = $model->getClinicContract(ContractModel::TYPE_DIAGNOSTIC_ONLINE);

/**
 * Альтернативная версия страницы
 */
$isInBTest = Yii::app()->referral->isABTest() === Partner::AB_TEST_B;

?>
<div class="has-aside">
	<a class="i-goback link-goback" href="<?=$refUrl?>"><span class="link">Вернуться к результатам поиска</span></a>
	<section class="clinic_detail full">
		<article class="clinic_card default <?php
			echo $isInBTest ? ' online_booking_first' : '';
		?>">
			<div class="clinic_info">
			<div class="clinic_title">

				<div class="clinic_person">
					<img title="<?php echo $model->name; ?>" alt="<?php echo $model->name; ?>" class="clinic_img"
						 src="<?php echo $model->getLogo(); ?>">
				</div>

				<div class="clinic_info_middle">

				<div class="clinic_info_aside">

					<?php if (!$isInBTest || !$isOnline): ?>
					<div class="clinic_request_phone">
						Запись по телефону
						<div class="clinic_request_phone_num">
							<?php if (!$model->isPayForDiagnostic()) { ?>
								<a
									href="#"
									class="show-clinic-phone"
									data-id="<?php echo $model->id; ?>"
									data-name="<?php echo $model->name; ?>"
									data-phone="<?php echo $model->diagnosticPhone()->prettyFormat(); ?>"
									title="Нажмите, чтобы увидеть телефон"
									><?php echo $model->diagnosticPhone()->getIncompleteNumber(); ?></a>
							<?php } else { ?>
								<?php echo $model->diagnosticPhone()->prettyFormat(); ?>
							<?php } ?>
						</div>
					</div>
					<?php endif; ?>

					<?php if ($isOnline) {?>
						<?php if ($model->discount_online_diag) {?>
							<div class="btn-discount-wrap js-tooltip-tr" title="При онлайн записи вы получаете дополнительную скидку <?=$model->discount_online_diag?>%">
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
							data-clinic-name="<?php echo htmlspecialchars($model->name); ?>"
							data-clinic-id="<?php echo $model->id;?>"
							data-clinic-phone="<?php echo $model->diagnosticPhone()->prettyFormat(); ?>"
							data-clinic-discount-online="<?=$model->discount_online_diag?>"
							data-validate-phone="<?php echo (int)$model->validate_phone; ?>">

						<?php if ($model->discount_online_diag) {?>
							<div class="btn-label-discount">-<?=$model->discount_online_diag?>%</div>
							</div>
						<?php }?>


							<?php if ($isInBTest): ?>
								<div class="clinic_request_phone mode2">
									или по телефону
									<div class="clinic_request_phone_num">
										<?php if (!$model->isPayForDiagnostic()) { ?>
											<a
												href="#"
												class="show-clinic-phone"
												data-id="<?php echo $model->id; ?>"
												data-name="<?php echo $model->name; ?>"
												data-phone="<?php echo $model->diagnosticPhone()->prettyFormat(); ?>"
												title="Нажмите, чтобы увидеть телефон"
												><?php echo $model->diagnosticPhone()->getIncompleteNumber(); ?></a>
										<?php } else { ?>
											<?php echo $model->diagnosticPhone()->prettyFormat(); ?>
										<?php } ?>
									</div>
								</div>
							<?php endif; ?>
					<?php } ?>
					<div class="clinic_work_time">
						<p>График работы центра:</p>
						<?php
							foreach ($model->getSchedule() as $v) {
								echo '<div class="l-ib">' . $v['DayTitle'] . ':</div> ' . $v['StartTime'] . '-' . $v['EndTime'] . '<br/>';
							}
						?>
					</div>
				</div>



					<h1 class="clinic_name"><?php echo $model->name; ?></h1>
					<?php
					$undergroundStations = $model->stations;
					if ($undergroundStations) {
						echo '<ul class="metro_list">';
						$stations = array();

						foreach ($undergroundStations as $station) {
							$stations[] = '<li class="metro_item metro_line_' . $station->underground_line_id . '" data-dist="' . $station->getDistanceToClinic($model->id) . '" data-line-id="' . $station->underground_line_id . '">' .
								'<span class="metro_link metro_line_' . $station->underground_line_id . '"> ' . $station->name . '</span></li>';
						}
						echo $stations ? implode(', ', $stations) : '';
						echo "</ul>";
					}
					?>

					<span class="link-adress js-ymap-tr"><?php echo $model->getAddress(); ?></span>

					<div data-mobile="0"
						 data-longitude="<?php echo $model->longitude; ?>"
						 data-latitude="<?php echo $model->latitude; ?>"
						 data-adress="<?php echo $model->address; ?>"
						 class="js-ymap js-ymap-data"
						 id="map_<?php echo $model->id; ?>"></div>

					<?php if (!in_array($model->id, $this->advertisements)): ?>
						<p data-ellipsis-height="40" class="clinic_desc"><?php echo $model->shortDescription; ?></p>
					<?php endif; ?>
				</div></div>

				<?php $this->renderPartial('priceList', compact('parentDiagnostics', 'diagnosticPrices', 'diagnosticSpecialPrices', 'onlinePrices', 'model')); ?>

			</div>
		</article>
	</section>
</div>

<aside class="l-aside">
	<?php $this->renderPartial('search', compact('parentDiagnostics', 'childDiagnostics', 'model')); ?>
</aside>

<?php $this->renderPartial('nearest', [ 'clinics' => $nearestClinics ]); ?>

<?php if ($isInBTest): ?>
<script language="javascript">
	/**
	 * Функция закрепления кнопки в топе
	 */
	$(document).ready(function(){
		var scroll = 239;
		var $menu = $(".clinic_card");

		$(window).scroll(function(){
			if ( $(this).scrollTop() > scroll && $menu.hasClass("default") ){
				$menu.removeClass("default").addClass("fixed");
			} else if($(this).scrollTop() <= scroll && $menu.hasClass("fixed")) {
				$menu.removeClass("fixed").addClass("default");
			}
		});
	});
</script>
<?php endif; ?>
