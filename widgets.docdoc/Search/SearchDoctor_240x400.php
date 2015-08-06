<div class="dd-widget dd-widget-search-doctor-240-480">
	<form method="get" action="<?=$this->getRedirectFormUrl()?>">
		<?=$this->getFormBaseWidgetParamsForRedirect()?>
		
		<div class="dd-title">Поиск врача</div>
		<div class="dd-label">Выберите направление:</div>
		<div class="dd-select">
			<?php echo $this->getSectorListField(); ?>
		</div>
		<div class="dd-label">Выберите <?php echo $this->isDistrict() ? "район" : "метро"; ?></div>
		<div class="dd-select">
			<?php echo $this->isDistrict() ? $this->getDistrictListField() : $this->getStationListField(); ?>
		</div>
		<input class="dd-submit" value="Найти" type="submit">
	</form>
</div>
