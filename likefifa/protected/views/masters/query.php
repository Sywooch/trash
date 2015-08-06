 		<div class="content-wrap content-pad-bottom">
			<div class="col-left">
				<div class="breadcrumbs">
					<a href="<?php use likefifa\components\helpers\ListHelper;

					echo $this->createSearchUrl(); ?>">Все мастера</a>
				</div>
				<div class="seo-txt">
					<h1><?php echo su::ucfirst($this->pageHeader ?: $this->pageTitle); ?></h1>
					<?php if ($this->pageSubheader): ?>
						<p><?php echo $this->pageSubheader; ?></p>
					<?php endif; ?>
				</div>
				<div class="search-res_head">
					<span class="txt"><?php echo su::caseForNumber(0, array('Найден', 'Найдено', 'Найдено')); ?> <span><?php echo '0 '.su::caseForNumber(0, array('мастер', 'мастера', 'мастеров')); ?></span></span>
				</div>
				
				<p>Мастера, соответствующие указанным условиям, не найдены.</p>
                
			</div>
			<div class="col-right">
				<div class="pre-map-rht">
					<a href="<?php echo $this->createMapUrl(); ?>" class="pre-map-img"><img src="/i/search-map.jpg" style="display:block;" /></a>
					<a href="<?php echo $this->createMapUrl(); ?>" class="pre-map-lbl">посмотреть на карте</a>
				</div>
				<form action="<?php echo $this->createRedirectUrl('custom'); ?>" method="GET" class="form-wrap filter-rht" id="filter-form">
					<input type="hidden" name="area" id="areaMoscow" value="<?php echo $area ? $area->id : null; ?>" />
					<input type="hidden" name="districts" id="districtMoscow" value="<?php echo ListHelper::buildIdList($districts); ?>" />
					
					<div class="head">Вы ищете:</div>
					<div class="filter-head">вид услуг:</div>
					<div class="form-inp spec-selector">
						<input type="hidden" id="inp-select-popup-service-type" name="specialization" value="<?php echo $specialization ? $specialization->id : null; ?>" />
						<div class="form-select-over" data-select-popup-id="select-popup-service-type"></div>
						<div class="form-select" id="cur-select-popup-service-type"><?php echo $specialization ? $specialization->name : 'Выберите из списка'; ?></div><div class="form-select-arr png"></div>					
						<div class="form-select-popup" id="select-popup-service-type">
							<div class="form-select-popup-long">
								<span class="item<?php echo $specialization ? '' : ' act'; ?>" data-value="">Выберите из списка</span>
								<?php foreach (LfSpecialization::model()->ordered()->findAll() as $spec): ?>
									<span class="item<?php echo $specialization && ($spec->id == $specialization->id) ? ' act' : ''; ?>" data-value="<?php echo $spec->id; ?>"><?php echo $spec->name; ?></span>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
					<div class="filter-head">подвид услуг:</div>
					<div class="form-inp service-selector">
						<input type="hidden" id="inp-select-popup-service-subtype" name="service" value="<?php echo $service ? $service->id : null; ?>" />
						<div class="form-select-over" data-select-popup-id="select-popup-service-subtype"></div>
						<div class="form-select form-select_pink" id="cur-select-popup-service-subtype"><?php echo $service ? $service->name : 'Выберите из списка'; ?></div><div class="form-select-arr png"></div>					
						<div class="form-select-popup" id="select-popup-service-subtype">
							<div class="form-select-popup-long">
								<span class="item form-select_pink<?php echo $service ? '' : ' act'; ?>" data-value="">Выберите из списка</span>
								<?php foreach (LfService::model()->ordered()->findAll() as $serv): ?>
									<span class="item<?php echo $service && ($serv->id == $service->id) ? ' act' : ''; ?>" data-spec-id="<?php echo $serv->specialization_id; ?>" data-value="<?php echo $serv->id; ?>"><?php echo $serv->name; ?></span>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
					<div class="filter-head">возле метро:</div>
					<?php $this->widget('\likefifa\components\likefifa\widgets\LfMetroInputWidget', [
							'stationIdList' => ListHelper::buildIdList($stations),
							'stationList' => ListHelper::buildNameList($stations),
						]); ?>
					<div class="filter-head">в районе:</div>
					<div class="form-inp" id="select-area">
						<input type="hidden" id="inp-select-popup-service-subtype" />
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
					<div class="filter-check">
						<span class="form-inp_check" data-check-id="f_home"><i id="i-check_f_home" class="png"></i><input type="checkbox" id="inp-check_f_home" name="hasDeparture" <?php echo $hasDeparture ? 'checked="checked"' : ''; ?> />Возможен выезд</span>
					</div>
					<div style="text-align:center"><input type="submit" class="style-submit" value="Найти" /><div class="button button-pink" id="filter-right-sbmt"><span>Найти</span><img src="/i/icon-search-filter.png" class="png" /></div></div>
				</form>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>