<?php if (!$this->hiddenFromSearch): ?>

	<?php if (Yii::app()->city->isMoscow()) {?>
		<a href="/map.php?<?php echo $this->parentDiagnostic ? 'diagnostic=' . $this->parentDiagnostic->id : ''; ?><?php echo $this->diagnostic ? '&subDiagnostica=' . $this->diagnostic->id : ''; ?>"
		   class="b-map_right__link">
			<img src="<?php echo Yii::app()->homeUrl; ?>st/i/common/map-right.png" alt="">
		</a>
	<?php }?>

	<script type="text/javascript">
		var diagnostics = eval(<?php echo json_encode($this->diagnosticTree); ?>);
	</script>

	<noindex>
		<div class="b-filter_right__wrap">
			<div class="b-filter_right__item">
				<div class="b-filter_right__label">вид диагностики</div>
				<div class="b-select_wrap b-select_spec" id="diagnostic-type">
					<div class="b-select_arr"></div>
					<div class="b-select_current"><?php
						if (isset($diagnostic)) {
							if ($diagnostic && count($parentDiagnostics) == 0) {
								echo $diagnostic->name;
							} else {
								echo $this->parentDiagnostic->name;
							}
						} else {
							echo 'выберите из списка';
						}
						?>
					</div>
					<div class="b-select_list">
						<?php foreach ($parentDiagnostics as $item): ?>
							<div
								class="js-specselect b-select_list__item <?php echo
								isset($diagnostic) && $diagnostic->id == $item->id ? 'b-select_list__act' : ''; ?>"
								data-spec-id="<?php echo $item->id; ?>"
								data-related-form="search_form"><?php echo $item->name; ?></div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<div class="b-filter_right__item">
				<div class="b-filter_right__label">область диагностики</div>
				<div class="b-select_wrap b-select_spec__sub" id="diagnostic-subtype">
					<div class="b-select_arr"></div>
					<div class="b-select_current">
						<?php
						if ($this->diagnostic) {
							echo $this->diagnostic->name;
						} else {
							echo count($childDiagnostics) == 0 ? 'нет вариантов' : 'выберите из списка';
						}
						?></div>
					<div class="b-select_list">
						<?php foreach ($childDiagnostics as $item): ?>
							<div
								class="js-specselect b-select_list__item <?php echo
								isset($diagnostic) && $diagnostic->id == $item->id ? 'b-select_list__act' : ''; ?>"
								data-spec-id="<?php echo $item->id; ?>"
								data-related-form="search_form"><?php echo $item->name; ?></div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<?php if (Yii::app()->city->isMoscow()) { ?>
				<div class="b-filter_right__item">
					<?php if ($this->stations): ?>
						<div class="b-filter_right__label">возле метро:</div>
					<?php endif; ?>
					<?php foreach ($this->stations as $s): ?>
						<div
							class="metro_link metro_line_<?php echo $s->underground_line_id; ?>"><?php echo $s->name; ?></div>
					<?php endforeach; ?>
				</div>
				<div class="b-filter_right__metro-link l-ib js-popup-tr" data-select-related="select-geo"
					 data-stat="btnPopupGeo" data-popup-width="920" data-popup-id="js-popup-geo"><span
						class="i-metro_filter"></span>выбрать метро
				</div>
			<?php } else { ?>
				<div class="b-filter_right__item">
					<?php if (Yii::app()->city->hasMetro()) { ?>
						<div class="b-filter_right__label">возле метро</div>
						<input class="search_input search_input_geo js-autocomplete-trigger" type="text" placeholder="выбрать метро"
							   value="<?php echo $this->stations ? $this->stations[0]->name : ''; ?>" data-autocomplete-id="autocomplete-geo1">
					<?php } else { ?>
						<div class="b-filter_right__label">в районе</div>
						<input class="search_input search_input_geo js-autocomplete-trigger" type="text" placeholder="выбрать район"
							   value="<?php echo $this->district ? $this->district[0]->name : ''; ?>" data-autocomplete-id="autocomplete-geo1">
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	</noindex>
<?php else: ?>
	<h3 class="related_list_head">Все типы диагностики</h3>
	<ul class="related_list related_list_right">
		<!--<li class="related_item"><a href="<?php echo $this->createUrl('diagnostics' . '/diagnostici') ?>" class="related_link"><?php echo $this->parentDiagnostic ? 'Все типы диагностики' : '<strong>Все типы диагностики</strong>'; ?></a></li>-->
		<?php foreach ($this->diagnostics as $d) { ?>
			<?php if ($d->parent_id == 0) { ?>
				<?php
				$urlParams = array();
				$urlParams['rewriteName'] = $d->rewrite_name;
				?>
				<li class="related_item"><a
						href="<?php echo $this->createUrl(Yii::app()->homeUrl . $d->rewrite_name) ?>"
						class="related_link"><?php echo ($this->parentDiagnostic && ($d->id === $this->diagnostic->id)) ? '<strong>' . $d->getParentName() . '</strong>' : $d->getParentName(); ?></a>
				</li>
			<?php } ?>
		<?php } ?>
	</ul>
<?php endif; ?>
