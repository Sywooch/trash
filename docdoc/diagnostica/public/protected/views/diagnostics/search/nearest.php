<?php
/**
 * @var dfs\docdoc\models\ClinicModel[] $clinics
 */
?>
<?php if (!empty($clinics)): ?>
	<script src="https://<?php echo Yii::app()->params['hosts']['static']; ?>/js/jquery.bxslider/jquery.bxslider.min.js"></script>

	<div class="nearest_clinics_wrap">

		<h4>Другие клиники поблизости</h4>

		<ul class="nearest_clinics_slider">
			<?php foreach ($clinics as $clinic): ?>
				<li class="nearest_clinics_item">
					<div class="nearest_clinics_cont">

						<div class="nearest_clinics_photo">
							<a href="/kliniki/<?php echo $clinic->rewrite_name; ?>/">
								<img src="<?php echo $clinic->getLogo(); ?>" class="clinic_img" />
							</a>
						</div>

						<div class="nearest_clinics_info">
							<div class="nearest_clinics_name">
								<a href="/kliniki/<?php echo $clinic->rewrite_name; ?>/"><?php echo $clinic->name; ?></a>
							</div>

							<div class="nearest_clinics_spec_wrap">
								<?php echo $clinic->getTypeOfInstitution(); ?>
							</div>
						</div>

						<div class="nearest_clinics_address">
							<?php if ($clinic->stations): ?>
								<span class="metro_icon"></span>
								<?php echo implode(', ', CHtml::listData($clinic->stations, 'id', 'name'));?>
							<?php else: ?>
								<?php echo $clinic->getAddress();?>
							<?php endif; ?>
						</div>

					</div>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>
<?php endif; ?>