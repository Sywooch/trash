<div class="dd-widget-search-vertical <?php echo $this->getContainerName(); ?>">
	<form method="get" action="<?= $this->getRedirectFormUrl() ?>">
		<?= $this->getFormBaseWidgetParamsForRedirect() ?>
		<div class="dd-title">Поиск врача</div>
		<div class="dd-label">Выберите направление:</div>
		<div class="dd-select">
			<?php echo $this->getSectorListField(); ?>
		</div>
		<div class="dd-label">Выберите <?php echo $this->isDistrict() ? "район" : "метро"; ?></div>
		<div class="dd-select">
			<?php echo $this->isDistrict() ? $this->getDistrictListField() : $this->getStationListField(); ?>
		</div>
		<div class="dd-submit-container">
			<input class="dd-submit" value="Найти" type="submit">
		</div>
	</form>
</div>
