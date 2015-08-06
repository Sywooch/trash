<?php

use dfs\docdoc\extensions\TextUtils;

/**
 * @var dfs\docdoc\models\ClinicModel[] $clinics
 */
?>
<?php if (!empty($clinics)): ?>
	<div class="nearest_doctors_wrap">

		<h2 class="t-fs-l">Другие клиники поблизости</h2>

		<ul class="nearest_doctors_slider" data-itemwidth="288">
			<?php foreach ($clinics as $clinic): ?>
				<?php
					$rating = $clinic->rating_show ? TextUtils::ratingFormat($clinic->rating_show) : null;
					$countReviews = $clinic->getCountReviews();
					$uniqueStations = $clinic->getUniqueStations();
				?>
				<li class="nearest_doctors_item">
					<div class="nearest_doctors_cont">

						<div class="nearest_doctors_photo">
							<a href="/clinic/<?php echo $clinic->rewrite_name; ?>">
								<img src="<?php echo $clinic->getLogo(); ?>" class="doctor_img" />
							</a>
						</div>

						<div class="nearest_doctors_info">
							<div class="nearest_doctors_name">
								<a href="/clinic/<?php echo $clinic->rewrite_name; ?>">
									<?php echo TextUtils::cutString($clinic->name, ['maxCharacters' => 55]); ?>
								</a>
							</div>

							<div class="nearest_doctors_spec_wrap">
								<?php echo $clinic->getTypeOfInstitution();?>
							</div>

							<div class="reviews_count<?php echo $countReviews > 0 ? '' : ' reviews_counter_no'; ?>">
								<a href="/clinic/<?php echo $clinic->rewrite_name;?>#reviews" class="reviews_counter"><?php echo $countReviews ?: 'нет'; ?></a>
								<a href="/clinic/<?php echo $clinic->rewrite_name;?>#reviews" class="reviews_counter_text">
									<?php echo RussianTextUtils::caseForNumber($countReviews, ['отзыв', 'отзыва', 'отзывов']); ?>
								</a>
							</div>

							<div class="doctor_rating js-tooltip-tr" title="Рейтинг сформирован на основе отзывов пациентов о врачах клиники на сайте docdoc.ru">
								<p class="doctor_rating_numbers">
									<?php if ($rating): ?>
										<span class="doctor_rating_main"><?php echo $rating['main']; ?></span><span class="doctor_rating_sub">.<?php echo $rating['sub']; ?></span>
									<?php else: ?>
										<span class="doctor_rating_no">нет</span>
									<?php endif; ?>
								</p>
								<span class="doctor_rating_disclaimer">рейтинг</span>
							</div>
						</div>

						<div class="nearest_doctors_address">
							<?php if ($uniqueStations): ?>
								<span class="metro_icon"></span>
								<?php echo implode(', ', array_keys($uniqueStations));?>
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