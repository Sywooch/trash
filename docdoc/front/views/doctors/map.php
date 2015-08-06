<?php
use dfs\docdoc\objects\Phone;
/**
 * @var \dfs\docdoc\models\DoctorModel $doctor
 */

$cardParams = Yii::app()->getParams()['doctorCard'];
$isMobile = Yii::app()->mobileDetect->isAdaptedMobile();
$clinics = $doctor->getActiveClinics();
?>

<section class="doctor_map">

	<div class="t-fs-l mbs">Принимает по адресу:</div>

	<div class="mvn js-ymap-ct s-open js-popup-tr" data-popup-id="js-popup-address">

		<div id="map_<?=$doctor->id?>" class="js-ymap js-ymap-data s-open" data-mobile="<?php echo (int)$isMobile;?>">
			<?php foreach ($clinics as $index => $clinic): ?>
				<div class="js-map-data"
					 data-address="<?=CHtml::encode($clinic->getAddress())?>"
					 data-latitude="<?=$clinic->latitude?>"
					 data-longitude="<?=$clinic->longitude?>"
					 data-number="<?=$index+1?>"
					 data-draggable="0"></div>
			<?php endforeach; ?>
			<div class="js-ymap-zoom">увеличить</div>
		</div>

		<div class="doctor_address_wrap <?php echo count($clinics) > 1 ? 'doctor_address_number_wrap' : ''?>">
			<?php foreach ($clinics as $index => $clinic): ?>
				<div class="doctor_address_item doctor_address_dotted <?php echo count($clinics) > 1 ? 'address_selector' : ''?>"
					 data-latitude="<?=$clinic->latitude?>"
					 data-longitude="<?=$clinic->longitude?>">

					<div class="doctor_address_clinic t-fs-n">
						<?php if (count($clinics) > 1): ?>
							<div class="doctor_address_number <?php echo !$cardParams['showClinicName'] ? 'without_clinic' : '';?>">
								<?=$index + 1?>.
							</div>
						<?php endif; ?>
						<?php if ($cardParams['showClinicName']): ?>
							<?=$clinic->name?>
						<?php endif; ?>
						<div class="t-fs-s mtxs"><?=$clinic->getAddress()?></div>
					</div>

					<div class="t-fs-s">
						<?php foreach ($clinic->stations as $key => $station): ?>
							<div class="metro_item">
								<span class="metro metro_link metro_line_<?=$station->underground_line_id?>">
									<?=$station->name?>
									<span class="metro_link_dist t-fs-xs">(<?=$station->getDistanceToClinic($clinic->id)?>)</span>
								</span>
								<?php if ($key < count($clinic->stations) - 1): ?>
									<span class="metro_list_delimiter">,</span>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>

					<?php if ($cardParams['showClinicPhone']): ?>
						<div class="doctor_address_phone">
							<?php if ($isMobile): ?>
								<a class="request_tel_call" href="tel:+<?=Phone::strToNumber($clinic->asterisk_phone)?>">
									<?=(new Phone($clinic->asterisk_phone))->prettyFormat('+7 ')?>
								</a>
							<?php else: ?>
								<?=(new Phone($clinic->asterisk_phone))->prettyFormat('+7 ')?>
							<?php endif; ?>
						</div>
					<?php endif; ?>

				</div>
			<?php endforeach; ?>
		</div>

	</div>

</section>
