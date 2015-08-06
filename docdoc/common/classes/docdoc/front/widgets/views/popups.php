<?php
/**
 * @var CWidget $this
 * @var bool $isMainPage
 * @var bool $isMobile
 * @var dfs\docdoc\objects\Phone $phoneForPage
 * @var dfs\docdoc\models\SectorModel $specialityList
 * @var dfs\docdoc\models\StationModel $stationList
 * @var dfs\docdoc\models\DistrictModel $districtList
 * @var array $areaData
 */
?>

<div class="popups">

	<?php if (Yii::app()->city->isMoscow()): ?>

		<div class="js-popup popup" data-popup-id="js-popup-speclist">
			<?php echo $this->render('/popups/doctorsTable', [
				'specialityList' => $specialityList,
				'isMainPage' => $isMainPage,
			]); ?>
		</div>

		<?php if (!$isMobile): ?>
			<div class="js-popup popup" data-popup-id="js-popup-geo">
				<?php echo $this->render('/popups/metroMap', [
					'stationList' => $stationList,
					'districtList' => $districtList,
					'areaData' => $areaData,
					'isMainPage' => $isMainPage,
				]); ?>
			</div>
		<?php endif; ?>

	<?php endif; ?>

	<div class="js-popup popup request" data-popup-id="js-popup-request">
		<?php echo $this->render('/popups/requestDoctor', ['phoneForPage' => $phoneForPage]); ?>
	</div>

	<div class="js-popup popup request" data-popup-id="js-popup-request-clinic">
		<?php echo $this->render('/popups/requestClinic', ['phoneForPage' => $phoneForPage]); ?>
	</div>

	<div class="js-popup popup popup-address" data-popup-id="js-popup-address">
		<div class="popup-address-right">
			<h4 class="t-fs-l">Врач принимает по адресам:</h4>
			<div class="address-list-scroll">
				<div class="popup-address-items popup-address-items-more"></div>
			</div>
		</div>
		<div class="popup-address-map" id="map_address_zoom"></div>
	</div>

</div>
