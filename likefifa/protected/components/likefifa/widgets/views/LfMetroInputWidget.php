<?php
use likefifa\components\likefifa\widgets\LfMetroInputWidget;

/**
 * @var LfMetroInputWidget $this
 */
?>

	<input type="hidden" name="stations" id="stations" value="<?php echo $this->stationIdList; ?>"/>

<?php if (Yii::app()->mobileDetect->isMobile()): ?>
	<div class="form-inp">
		<div class="form-select form-placeholder">
			<?php echo !empty($this->stationList) ? $this->stationList : 'Введите название станции'; ?>
		</div>
		<input type="text" value="" name="metro-suggest" id="metro-suggest" class="suggest-input"/>
	</div>
<?php else: ?>
	<div class="form-inp" id="select-metro">
		<div class="form-select">
			<?php echo !empty($this->stationList) ? $this->stationList : 'Любое метро'; ?>
		</div>
		<div class="form-select-arr form-select-icon png"></div>
		<div id="selected-metro_popup" <?php if (!$this->stationList): ?>class="metro-no-value"<?php endif; ?>>
			<i class="arr"></i>

			<div><?php echo $this->stationList; ?></div>
		</div>
	</div>
<?php endif; ?>