<?php
/**
 * @var string $diagnosticName
 */
?>
<div class="search <?php echo ($_SERVER['REQUEST_URI'] == Yii::app()->homeUrl || (!Yii::app()->city->isMoscow() && !$this->isMobile)) ? 'mainpage' : 'm-simple' ?> l-wrapper">
	<form action="/search/redirect/" method="post" class="search_form">
		<?php if ($_SERVER['REQUEST_URI'] == Yii::app()->homeUrl): ?>
			<?php if ($this->isMobile): ?>
				<span class="jsm-select" data-select-related="select-spec">
					Исследование
					<select data-selected="" class="search_input search_input_spec" data-select="select-spec" name="diagnostic">
						<option value="" data-select-placeholder="любой специальности">любой специальности</option>
					</select>
				</span>
				в
				<span class="search_input_imit search_input_imit_geo jsm-select" data-select-related="select-geo">
					<select data-selected="" class="search_input search_input_geo" data-select="select-geo" multiple>
						<option selected value="" data-select-placeholder="любом районе">любом районе</option>
					</select>
				</span>
				<input type="submit" value="Найти клинику" class="search_btn_find ui-btn ui-btn_teal">

			<?php else: ?>
				<span class="strong l-ib">Ищу Диагностику&nbsp;</span><!--
				--><label><input class="search_input search_input_spec" type="text" placeholder="исследование"></label><!--
				--><i class="search_list_spec js-popup-tr" data-popup-id="js-popup-speclist" data-popup-width="920"></i><!--
				--><span class="strong l-ib">&nbsp;&nbsp;в&nbsp;&nbsp;</span><!--
				--><label><input class="search_input search_input_geo js-autocomplete-trigger" type="text" placeholder="любом районе" data-autocomplete-id="autocomplete-geo"></label><!--
				--><?php if (Yii::app()->city->isMoscow()) {?>
					<i class="search_list_metro js-popup-tr s-dynamic" data-popup-id="js-popup-geo" data-popup-width="920" data-stat="btnPopupGeo"></i>
				<?php }?>
				<input type="submit" value="Найти клинику" class="search_btn_find ui-btn ui-btn_teal">
			<?php endif; ?>
		<?php else: ?>
			<?php if ($this->isMobile): ?>
				Исследование
				<span class="search_input_imit search_input_imit_spec jsm-select" data-select-related="select-spec">
					<span><?php echo $diagnosticName;?></span>
					<select data-selected="<?php echo $diagnostic; ?>" class="search_input search_input_spec" data-select="select-spec" name="diagnostic">
						<option value="" data-select-placeholder="любой специальности">любой специальности</option>
					</select>
				</span>
				в <?php if ($this->geoType == 'station') {?>
					<?php echo !empty($this->stations) ? 'районе метро' : '';?>
				<?php } elseif ($this->geoType == 'district') {?>
					<?php echo !empty($this->districts) ? 'районе' : '';?>
				<?php }?>
				<span class="search_input_imit search_input_imit_geo jsm-select" data-select-related="select-geo">
					<?php if ($this->geoType == 'station') {?>
						<span><?php echo !empty($this->stations) ? $this->stations[0]->name . (count($this->stations) > 1 ? ' [и ещё ' . (count($this->stations) - 1) . ']' : '') : 'любом районе'; ?></span>
					<?php } elseif($this->geoType == 'district') {?>
						<span><?php echo !empty($this->districts) ? $this->districts[0]->name : 'любом районе'; ?></span>
					<?php }?>
					<select class="search_input search_input_geo" data-select="select-geo" multiple>
						<option value="" data-select-placeholder="любой район">любой район</option>
					</select>
				</span>

				<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>

				<input type="text" value="" placeholder="любом районе" class="search_input search_input_geo ui-autocomplete-input">
			<?php else: ?>
				<?php if (Yii::app()->city->isMoscow()) {?>
					Исследование
					<span data-select-related="select-spec" data-popup-width="920" data-popup-id="js-popup-speclist" class="search_input_imit search_input_imit_spec js-popup-tr jsm-select l-ib">
						<?php echo $diagnosticName;?>
					</span>
					в <?php echo !empty($this->stations) ? 'районе метро' : '';?>
					<span data-select-related="select-geo" data-stat="btnPopupGeo" data-popup-width="920" data-popup-id="js-popup-geo" class="search_input_imit search_input_imit_geo js-popup-tr jsm-select">
						<span><?php echo !empty($this->stations) ? $this->stations[0]->name . (count($this->stations) > 1 ? ' [и ещё ' . (count($this->stations) - 1) . ']' : '') : 'любом районе'; ?></span>
					</span>

					<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
					<input type="text" value="" placeholder="любом районе" class="search_input search_input_geo ui-autocomplete-input">
				<?php } else {?>
					<span class="strong l-ib">Ищу Диагностику&nbsp;</span><!--
					--><label><input class="search_input search_input_spec" type="text"
									 value="<?php echo $diagnosticName !== "все диагностики" ? $diagnosticName : ""; ?>"
									 placeholder="<?php echo $diagnosticName; ?>"></label><!--
					--><i class="search_list_spec js-popup-tr" data-popup-id="js-popup-speclist" data-popup-width="920"></i><!--
					--><span class="strong l-ib">&nbsp;&nbsp;в&nbsp;&nbsp;</span><!--
					--><label>
					<?php if ($this->geoType == 'station') {?>
						<input class="search_input search_input_geo js-autocomplete-trigger" type="text" data-autocomplete-id="autocomplete-geo" placeholder="любом районе" value="<?php echo !empty($this->stations) ? $this->stations[0]->name : '';?>">
					<?php } elseif($this->geoType == 'district') {?>
						<input class="search_input search_input_geo js-autocomplete-trigger" type="text" data-autocomplete-id="autocomplete-geo" placeholder="любом районе" value="<?php echo !empty($this->districts) ? $this->districts[0]->name : '';?>">
					<?php }?>
					</label>
					<input type="submit" value="Найти клинику" class="search_btn_find ui-btn ui-btn_teal">
				<?php }?>
			<?php endif; ?>
		<?php endif; ?>
		<input type="hidden" class="js-choose-input-spec" id="diagnostic" name="diagnostic" value="<?php echo $diagnostic; ?>"/>
		<input type="hidden" class="js-choose-input-geo" id="geoValue" name="geoValue" value="<?php echo $geoValue; ?>"/>
		<input type="hidden" class="js-choose-input-geo" id="geoType" name="geoType" value="<?php echo $this->geoType; ?>"/>
		<input type="hidden" id="areaMoscow" name="areaMoscow" value="<?php echo $area; ?>"/>
		<input type="hidden" id="districtMoscow" name="districtMoscow" value="<?php echo $districts; ?>"/>
		<?php if (!is_null($geoDataJson)) {?>
			<input type="hidden" id="geoDataJson" value='<?php echo $geoDataJson;?>'/>
		<?php }?>
	</form>
</div>