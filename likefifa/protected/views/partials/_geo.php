<?php
use likefifa\models\RegionModel;
use likefifa\components\helpers\ListHelper;

/**
 * @var SearchController     $this
 * @var UndergroundStation[] $stations
 * @var DistrictMoscow[]     $districts
 */
?>

<?php if (Yii::app()->activeRegion->canShowGeo()) { ?>
	<div class="filter-head head-metro">возле метро:</div>

	<?php $this->widget(
		'\likefifa\components\likefifa\widgets\LfMetroInputWidget',
		array(
			'stationIdList' => ListHelper::buildIdList($stations),
			'stationList'   => ListHelper::buildNameList($stations),
		)
	); ?>

	<div class="filter-head head-distr">в районе:</div>
	<div class="form-inp" id="select-area">

		<div id="selected-areas_popup" <?php if (!$districts): ?>class="areas-no-value"<?php endif; ?>>
			<i class="arr"></i>

			<div>
				<?php if ($districts): ?>
					<?php echo implode(', ', ListHelper::buildPropList('name', $districts)); ?>
				<?php endif; ?>
			</div>
		</div>

		<div class="form-select">
			<?php if ($districts): ?>
				<?php echo implode(', ', ListHelper::buildPropList('name', $districts)); ?>
			<?php else: ?>
				Выберите район
			<?php endif; ?>
		</div>

		<div class="form-select-arr form-select-icon png"></div>
	</div>
<?php } ?>