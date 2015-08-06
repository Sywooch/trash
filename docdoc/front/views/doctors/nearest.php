<?php

use dfs\docdoc\extensions\TextUtils;

/**
 * @var dfs\docdoc\models\DoctorModel[] $doctors
 */
?>
<?php if (!empty($doctors)): ?>
	<div class="nearest_doctors_wrap">

		<h4>Другие врачи поблизости</h4>

		<ul class="nearest_doctors_slider">
			<?php foreach ($doctors as $doctor): ?>
				<?php
					$rating = TextUtils::ratingFormat($doctor->getDoctorRating());
					$clinic = $doctor->getDefaultClinic();
					$uniqueStations = $clinic ? $clinic->getUniqueStations() : [];
					$countReviews = $doctor->countReviews();
				?>
				<li class="nearest_doctors_item">
					<div class="nearest_doctors_cont">

						<div class="nearest_doctors_photo">
							<a href="/doctor/<?php echo $doctor->rewrite_name; ?>">
								<img src="<?php echo $doctor->getImg(); ?>" class="doctor_img" />
							</a>
						</div>

						<div class="nearest_doctors_info">
							<div class="nearest_doctors_name">
								<a href="/doctor/<?php echo $doctor->rewrite_name; ?>"><?php echo $doctor->name; ?></a>
							</div>

							<div class="nearest_doctors_spec_wrap">
								<div class="nearest_doctors_spec"><?php echo implode(', ', CHtml::listData($doctor->visibleSectors, 'id', 'name')); ?></div>
								<?php
									if ($doctor->getExperience() > 0) {
										echo 'Стаж: ', $doctor->getExperience(), ' ';
										echo RussianTextUtils::caseForNumber($doctor->getExperience(), ['год', 'года', 'лет']);
									}
								?>
							</div>

							<div class="reviews_count<?php echo $countReviews > 0 ? '' : ' reviews_counter_no'; ?>">
								<a href="/doctor/<?php echo $doctor->rewrite_name;?>#reviews" class="reviews_counter"><?php echo $countReviews ?: 'нет'; ?></a>
								<a href="/doctor/<?php echo $doctor->rewrite_name;?>#reviews" class="reviews_counter_text">
									<?php echo RussianTextUtils::caseForNumber($countReviews, ['отзыв', 'отзыва', 'отзывов']); ?>
								</a>
							</div>

							<?php if ($rating): ?>
								<div class="doctor_rating js-tooltip-tr" title="Рейтинг врача сформирован на основании следующих показателей: образование, опыт работы, научная степень.">
									<p class="doctor_rating_numbers">
										<span class="doctor_rating_main"><?php echo $rating['main']; ?></span><span class="doctor_rating_sub">.<?php echo $rating['sub']; ?></span>
									</p>
									<span class="doctor_rating_disclaimer">рейтинг</span>
								</div>
							<?php endif; ?>

							<div class="nearest_doctors_price"><span>цена приёма: </span><span class="nearest_doctors_cost"><?php echo $doctor->price; ?>р.</span></div>
						</div>

						<?php if ($clinic): ?>
							<div class="nearest_doctors_address">
								<?php if ($uniqueStations): ?>
									<span class="metro_icon"></span>
									<?php echo implode(', ', array_keys($uniqueStations));?>
								<?php else: ?>
									<?php echo $clinic->getAddress();?>
								<?php endif; ?>
							</div>
						<?php endif; ?>

					</div>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>
<?php endif; ?>