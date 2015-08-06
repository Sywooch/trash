<?php
use likefifa\models\RegionModel;

/**
 * @var LfSpecialization $specialization
 * @var LfService        $service
 * @var string           $parent
 */
?>

<div class="content-wrap content-pad-bottom">
	<div class="det-line_sep" style="margin-top: 11px;"><h1>Карта сайта</h1></div>
	<ul class="sitemap">
		<li>
			<strong><?php echo su::ucfirst($parent); ?></strong>
			<ul>
				<?php if (Yii::app()->activeRegion->isMoscow()) { ?>
				<li>
					<strong>В округе Москвы:</strong>
					<ul>
						<?php foreach (AreaMoscow::model()->with('districts')->ordered()->findAll() as $area): ?>
							<li>
								&mdash; <a href="<?php echo $this->forMasters()->createAreaUrl(
									$area,
									$specialization
								); ?>"><?php echo $area->name; ?></a>
								<ul>
									<?php foreach ($area->districts as $district): ?>
										<li>&mdash; <a href="<?php echo $this->createSearchUrl($specialization, $service, false, array(), $area, array($district)); ?>"><?php echo $district->name; ?></a></li>
									<?php endforeach; ?>
								</ul>
							</li>
						<?php endforeach; ?>
					</ul>
				</li>
				<li>
					<strong>На станции метро:</strong>
					<ul>
						<?php foreach (UndergroundStation::model()->ordered()->findAll() as $station): ?>
							<li>&mdash; <a href="<?php echo $this->createSearchUrl($specialization, $service, false, array($station)); ?>"><?php echo $station->name; ?></a></li>
						<?php endforeach; ?>
					</ul>
				</li>
				<?php } else { ?>
					<li>
						<strong>В городах:</strong>
						<ul>
							<?php foreach (Yii::app()->activeRegion->getModel()->activeCities as $city) { ?>
								<li>
									&mdash; <a href="<?php echo $this->forMasters()->createCityUrl(
										$city->rewrite_name,
										$specialization
									); ?>"><?php echo $city->name; ?></a>
								</li>
							<?php } ?>
						</ul>
					</li>
				<?php } ?>
			</ul>
		</li>
	</ul>
</div>