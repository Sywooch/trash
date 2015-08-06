<?php
/**
 * @var \dfs\docdoc\models\ClinicModel $clinic
 */

$j = 0;
$isMobile = Yii::app()->mobileDetect->isAdaptedMobile();
$countStations = count($clinic->stations);
?>

<section class="doctor_map">

	<div class="mvn js-ymap-ct s-open js-popup-tr" data-popup-id="js-popup-address">

		<div id="map_<?=$clinic->id?>" class="js-ymap js-ymap-data s-open" data-mobile="<?php echo (int)$isMobile;?>">
			<div class="js-map-data"
				 data-address="<?=CHtml::encode($clinic->getAddress())?>"
				 data-latitude="<?=$clinic->latitude?>"
				 data-longitude="<?=$clinic->longitude?>"
				 data-title="<?=CHtml::encode($clinic->name)?> находится по адресу:"
				 data-number="1"
				 data-draggable="0"></div>
			<div class="js-ymap-zoom">увеличить</div>
		</div>

		<div class="doctor_address_wrap">

			<div class="doctor_address_item doctor_address_dotted"
				 data-latitude="<?=$clinic->latitude?>"
				 data-longitude="<?=$clinic->longitude?>">

				<div class="doctor_address_clinic t-fs-n">
					<div class="t-fs-s mtxs"><?=$clinic->getAddress()?></div>
				</div>

				<div class="t-fs-s">
					<?php foreach ($clinic->stations as $station): ?>
						<div class="metro_item">
							<span class="metro metro_link metro_line_<?=$station->underground_line_id?>">
								<?=$station->name?>
								<span class="metro_link_dist t-fs-xs">(<?=$station->getDistanceToClinic($clinic->id)?>)</span>
							</span>
							<?php if (++$j < $countStations): ?>
								<span class="metro_list_delimiter">,</span>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>

			</div>

			<?php if ($clinic->way_on_foot || $clinic->way_on_car): ?>
				<div class="description_item doctor_address_dotted">

					<?php if ($clinic->way_on_foot): ?>
						<div class="t-fs-s">
							<p><b>Как добраться пешком</b></p>
							<p class="clinic_desc"><?php echo $clinic->way_on_foot; ?></p>
						</div>
					<?php endif; ?>

					<?php if ($clinic->way_on_car): ?>
						<div class="t-fs-s">
							<p><b>Как добраться на машине</b></p>
							<p class="clinic_desc"><?php echo $clinic->way_on_car; ?></p>
						</div>
					<?php endif; ?>

				</div>
			<?php endif; ?>

		</div>

	</div>

</section>
