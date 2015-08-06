<div class="dd-widget-rs <?php echo $this->getContainerName(); ?>">
	<form method="get" action="<?= $this->getRedirectFormUrl() ?>">

		<?= $this->getFormBaseWidgetParamsForRedirect() ?>

		<div class="dd-title">Поиск <?php echo $this->searchType === "doctor" ? "врача" : "клиники"; ?></div>
		<div class="dd-forms-container">
			<div class="dd-select">
				<?php echo $this->getSectorListField(); ?>
			</div>
			<div class="dd-select">
				<?php echo $this->isDistrict() ? $this->getDistrictListField() : $this->getStationListField(); ?>
			</div>
			<input class="dd-submit" value="Найти" type="submit">

			<div class="dd-docdoc">
				Форма подбора предоставлена сервисом по поиску врачей <a href="<?php echo $this->getDocDocUrl(); ?>">DocDoc.ru</a>
			</div>
		</div>
	</form>

</div>