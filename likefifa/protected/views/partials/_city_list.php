<?php

use likefifa\models\CityModel;
use likefifa\components\Seo;

/**
 * @var CityModel $city
 */
?>

<?php if (Yii::app()->activeRegion->canShowCities()) { ?>
	<div class="filter-head head-city">Город:</div>
	<div class="form-inp city-selector">
		<input type="hidden" id="inp-select-popup-city" name="city" value="<?php echo $city ? $city->id : 0 ?>"/>

		<div class="form-select-over" data-select-popup-id="select-popup-city"></div>
		<div class="form-select form-select" id="cur-select-popup-city"><?php echo Seo::$location->name; ?></div>
		<div class="form-select-arr png"></div>
		<div class="form-select-popup" id="select-popup-city">
			<div class="form-select-popup-long">
				<?php foreach (Yii::app()->activeRegion->getModel()->activeCities as $cityModel) { ?>
					<span class="item" data-value="<?php echo $cityModel->id; ?>"><?php echo $cityModel->name; ?></span>
				<?php } ?>
			</div>
		</div>
	</div>
<?php } ?>