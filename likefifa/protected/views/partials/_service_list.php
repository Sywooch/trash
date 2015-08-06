<?php
use likefifa\models\RegionModel;

/**
 * @var LfService $service
 */
?>

<?php if (Yii::app()->activeRegion->canShowServices()) { ?>
	<div class="filter-head">подвид услуг:</div>
	<div class="form-inp service-selector">
		<input
			type="hidden"
			id="inp-select-popup-service-subtype"
			name="service"
			value="<?php echo $service ? $service->id : 0; ?>"
			/>

		<div class="form-select-over" data-select-popup-id="select-popup-service-subtype"></div>
		<div
			class="form-select form-select_pink"
			id="cur-select-popup-service-subtype"
			><?php echo $service ? $service->name : "Выберите из списка"; ?></div>
		<div class="form-select-arr png"></div>
		<div class="form-select-popup" id="select-popup-service-subtype">
			<div class="form-select-popup-long">
				<span
					class="item form-select_pink<?php if (!$service) { ?> act<?php } ?>"
					data-value=""
					>Выберите из списка</span>
				<?php foreach (LfService::model()->filtered()->findAll() as $s) { ?>
					<span
						class="item"
						data-spec-id="<?php echo $s->specialization_id; ?>"
						data-value="<?php echo $s->id; ?>"
						><?php echo $s->name; ?></span>
				<?php } ?>
			</div>
		</div>
	</div>
<?php } ?>