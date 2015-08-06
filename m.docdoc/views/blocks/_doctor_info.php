<?php
/**
 * @var DoctorModel $doctor
 * @var bool        $view_map
 */
?>
<div class="left-block">
	<div class="avatar-wrap">
		<img src="<?php echo $doctor->getImg(); ?>" alt="<?php echo $doctor->getName(); ?>">
	</div>
	<div class="rating-wrap"><strong><?php echo $doctor->getRating(); ?></strong>
		<?php echo $this->renderPartial('//blocks/_rating', ['rating' => $doctor->getRating()]); ?>
	</div>
	<?php if ($doctor->getOpinionCount()) { ?>
		<span
			class="qty-reviews"><?php echo Yii::t(
				'',
				'{n} отзыв|{n} отзыва|{n} отзывов',
				[$doctor->getOpinionCount()]
			); ?></span>
	<?php } ?>
</div>
<div class="right-block">
	<h3 class="name"><?php echo $doctor->getName(); ?></h3><strong
		class="prof"><?php echo $doctor->getAllSpecialityString(); ?> </strong>

	<div class="skills-wrap"><?php if ($doctor->getExperienceYear() > 0) { ?><span
			class="left">Стаж: <b><?php echo Yii::t('', '{n} год|{n} года|{n} лет', [$doctor->getExperienceYear()]); ?>
			</b></span><?php } ?>
		<span class="right">
			Прием:
			<?php if ($doctor->getSpecialPrice()) { ?>
				<b class="originalPrice"><?= $doctor->getPrice() ?><span>i</span></b>
				<b class="specialPrice"><?=$doctor->getSpecialPrice()?><span>i</span> только на DocDoc!</b>
			<?php } else { ?>
				<b><?= $doctor->getPrice() ?><span>p</span></b>
			<?php } ?>
		</span>
	</div>
	<?php if (isset($view_map)) { ?>
		<div class="address-wrap">
			<i></i>
			<a href="#map-page" data-transition="fade" class="address-link">
				<?php echo $doctor->getLocation(); ?>
			</a>
		</div>
		<a href="#map-page" data-transition="fade" class="link-to-map"></a>
	<?php } else { ?>
		<div class="address-wrap"><i></i> <?php echo $doctor->getLocation(); ?></div>
	<?php } ?>
</div>