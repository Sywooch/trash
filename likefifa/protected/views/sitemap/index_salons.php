<?php
use likefifa\models\RegionModel;
?>

<div class="content-wrap content-pad-bottom">
	<div class="det-line_sep" style="margin-top: 11px;"><h1>Карта сайта</h1></div>
	<ul class="sitemap">
		<li>
			&mdash; <a href="<?php echo $this->createUrl('site/index') ?>"><strong>Главная</strong></a>
			<ul>
				<li>&mdash; <a href="<?php echo $this->createUrl('/salons') ?>"><strong>Все салоны</strong></a>
					<ul>
						<li>&mdash; <strong class="black">Все услуги</strong>
							<?php foreach (LfSpecialization::model()->findAll() as $spec): ?>
								<div class="met-dia">
									<ul<?php
									echo !Yii::app()->activeRegion->isMoscow() ? ' class="margin-bottom-none"' : ""; ?>>
										<li>
										&mdash; <a href="<?php echo $this->createUrl('sitemap/salons', array('specialization' => $spec->getRewriteName())); ?>"><strong><?php echo su::ucfirst($spec->name); ?></strong></a>
										<?php if (Yii::app()->activeRegion->isMoscow() && $spec->services) { ?>
											<ul>
												<?php foreach ($spec->services as $service): ?>
													<li>&mdash; <a href="<?php echo $this->createUrl('sitemap/salons', array('specialization' => $spec->getRewriteName(), 'service' => $service->getRewriteName())); ?>"><?php echo $service->name; ?></a></li>
												<?php endforeach; ?>
											</ul>
										<?php } ?>
										</li>
									</ul>
								</div>
							<?php endforeach; ?>
						</li>
						<?php if (Yii::app()->activeRegion->isMoscow()) { ?>
						<li>&mdash; <a href="<?php echo $this->createUrl(
								'sitemap/salons',
								array('location' => "location")
							); ?>"><strong>По расположению</strong></a></li>
						<?php } ?>
						<li>&mdash; <a href="<?php echo $this->createUrl('article/index'); ?>"><strong>Все статьи</strong></a></li>
					</ul>
				</li>
			</ul>
		</li>
	</ul>
</div>