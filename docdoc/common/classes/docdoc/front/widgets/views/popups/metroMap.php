<?php

use dfs\docdoc\extensions\TextUtils;
use dfs\docdoc\helpers\ActiveRecordHelper;

/**
 * @var bool $isMainPage
 * @var dfs\docdoc\models\StationModel[] $stationList
 * @var dfs\docdoc\models\DistrictModel[] $districtList
 * @var array $areaData
 */

$stationsUnique = [];
foreach ($stationList as $station) {
	if (!isset($stationsUnique[$station->name])) {
		$stationsUnique[$station->name] = $station;
	}
}
?>

<div class="js-tabs">

	<ul class="js-tabs-controls">
		<li class="js-tabs-control s-active">Карта метро</li>
		<li class="js-tabs-control" data-stat="tabListStations">Список станций</li>
		<li class="js-tabs-control" data-stat="tabListRegions">Список районов</li>
	</ul>

	<div class="js-tabs-tab">
		<form id="extended_search_form_act" class="sf_form zf zf-inited" onsubmit="return false">

			<div class="">
				<div class="">

					<div class="popup_geo_controls">
						<div class="popup_geo_title h2">Выберите станции метро</div>
					</div>

					<input class="ex_location_map_trigger_metro s-hidden" type="checkbox" name="location_trigger" value="location_metro" checked="checked" rel="moscow" />

					<div id="metro" class="metro_section_map ex_location_type" style="display: block;">

						<div class="metro_top_controls">
							<ul class="als_metro_circle_triggers">
								<li class="metro_top_controls_item">
									<input class="ui-autocomplete-input metro_filter" placeholder="Поиск по названию" />
								</li>
								<li class="metro_top_controls_item">
									<span class="als_metro_select_inside i-metroctrl_circleinner">
										<span class="pseudo">Выделить станции внутри кольца</span>
									</span>
								</li>
								<li class="metro_top_controls_item">
									<span class="als_metro_select_circle i-metroctrl_circle">
										<span class="pseudo">Выделить кольцевые станции</span>
									</span>
								</li>
								<li class="metro_top_controls_item">
									<span class="als_metro_deselect i-metroctrl_remove" style="display: none;">
										<span class="pseudo">удалить выбранные станции</span>
									</span>
								</li>
							</ul>
						</div>

						<div class="metrobox ex_location_type opened" style="display: block;">
							<div class="als_metro">
								<div style="width: 900px; height: 961px;"></div>
							</div>
						</div>

					</div>

				</div>
			</div>

		</form>
	</div>

	<div class="js-tabs-tab">
		<noindex>
		<ul class="stations_list columns_4 metro_list_stations">
			<?php foreach (TextUtils::formatItemsByAlphabet($stationsUnique, 4) as $column): ?>
				<li class="column">
					<?php foreach ($column as $letter => $group): ?>
						<ul class="stations_list_group">
							<?php foreach ($group as $station): ?>
								<li class="stations_list_item js-stationselect js-geoselect" data-station-id="<?php echo $station->id; ?>">
									<?php echo $station->name; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endforeach; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		</noindex>
	</div>

	<div class="js-tabs-tab">
		<noindex>
		<ul class="regions_list columns_4 metro_list_geo">
			<?php foreach (ActiveRecordHelper::groupItemsByField($districtList, 'id_area', 4, 10) as $column): ?>
				<li class="column">
					<?php foreach ($column as $areaId => $group): ?>
						<?php
							$data = isset($areaData[$areaId]) ? $areaData[$areaId] : null;
							$stationIds = $data ? $data['stationIds'] : [];
						?>
						<ul class="stations_list_group">
							<li>
								<?php if ($data): ?>
									<span class="regions_list_group_title js-regionselect js-regionselect-whole js-geoselect"
										  data-station-id-array="<?php echo implode(',', $data['areaStationIds']) ?>">
										<?php echo $data['area']->name; ?>
									</span>
								<?php endif; ?>
								<ul class="regions_sublist">
									<?php foreach ($group as $district): ?>
										<li class="stations_list_item js-regionselect js-geoselect"
											data-station-id-array="<?php echo empty($stationIds[$district->id]) ? '' : implode(',', $stationIds[$district->id]); ?>">
											<?php echo $district->name; ?>
										</li>
									<?php endforeach; ?>
								</ul>
							</li>
						</ul>
					<?php endforeach; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		</noindex>
	</div>

</div>

<div id="metroControls" class="metro_section_controls">

	<div class="metro_list_selected">
		<span class="metro_selected_title l-b">Выбраны станции:</span>
		<div class="ex_location_list metro_selected" style="display: none; height: auto;"></div>
	</div>

	<div class="metro_list_selected_actions">
		<div class="ui-btn ui-teal input_metro_submit"<?php echo $isMainPage ? '' : ' data-related-form="search_form"'; ?>>
			Найти врача
		</div>
		<span class="pseudo als_metro_deselect mtm" style="display: none;">
			удалить выбранные станции
		</span>
	</div>

</div>
