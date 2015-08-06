<?php
/**
 * @var \dfs\docdoc\models\ClinicModel $clinic;
 * @var string $stationUrl
 * @var bool $showClinicName
 * @var string $extraClass
 */

$j = 0;
$clinicAddress = $clinic->getAddress();
$countStations = count($clinic->stations);
?>

<div class="doctor_address_item">

	<?php if (!empty($showClinicName)): ?>
		<div class="doctor_address_clinic t-fs-n i-address-doctor <?php echo empty($extraClass) ? '' : $extraClass; ?>">
			<?php echo $clinic->name; ?>
		</div>
	<?php endif; ?>

	<div class="t-fs-s<?php echo empty($showClinicName) ? ' i-address-doctor ' . (empty($extraClass) ? '' : $extraClass) : ''; ?>">

		<?php if (mb_strlen($clinicAddress) > 5): ?>
			<span class="clinic-address"><?php echo $clinicAddress; ?></span>
		<?php endif; ?>

		<?php foreach ($clinic->stations as $station): ?>
			<div class="metro_item">
				<a href="<?php echo $stationUrl . $station->rewrite_name; ?>"
				   class="metro_link metro_line_<?php echo $station->underground_line_id; ?>">
					<?php echo $station->name; ?>
					<span class="metro_link_dist t-fs-xs">(<?php echo $station->getDistanceToClinic($clinic->id); ?>)</span>
				</a><?php echo ++$j < $countStations ? ', ' : ''; ?>
			</div>
		<?php endforeach; ?>

	</div>

</div>
