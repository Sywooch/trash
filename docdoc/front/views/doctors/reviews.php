<?php

use dfs\docdoc\models\DoctorModel;
use dfs\docdoc\models\DoctorOpinionModel;
use dfs\docdoc\extensions\DateTimeUtils;

/**
 * @var DoctorModel $doctor
 * @var array $avgRatings
 * @var DoctorOpinionModel[] $reviews
 * @var int $countReviews
 */

$countMore = $countReviews - count($reviews);
?>

<section class="doctor_reviews" id="reviews">

	<?php if ($countReviews): ?>

		<div class="reviews_header ui-grad-yellow">
			<h3 class="reviews_header_title mtn mbm">
				<span>Общее мнение пациентов</span>
				/ по <?php echo $countReviews . ' ' . RussianTextUtils::caseForNumber($countReviews, ['отзыву', 'отзывам', 'отзывам']); ?>
			</h3>

			<ul class="reviews_total_rating">
				<li class="total_rating_text total_rating_item">
					<?php echo DoctorOpinionModel::getRatingInWord($avgRatings['RatTotal']); ?>
				</li>
				<li class="total_rating_item">
					<div class="l-ib js-tooltip-tr"
						 title="Насколько врач подробно ответил на все вопросы по курсу лечения или диагностики.">
						<span class="review_ratings_label">Квалификация</span>
						<div class="rating_stars js-rating" data-score="<?php echo $avgRatings['RatQualification']; ?>"></div>
					</div>
				</li>
				<li class="total_rating_item">
					<div class="l-ib js-tooltip-tr"
						 title="Насколько врач был внимателен и тактичен по отношению к пациенту.">
						<span class="review_ratings_label">Внимание</span>
						<div class="rating_stars js-rating" data-score="<?php echo $avgRatings['RatAttention']; ?>"></div>
					</div>
				</li>
				<li class="total_rating_item">
					<div class="l-ib js-tooltip-tr"
						 title="Насколько цена приема соответствует качеству обслуживания и полученным результатам.">
						<span class="review_ratings_label">Цена-качество</span>
						<div class="rating_stars js-rating" data-score="<?php echo $avgRatings['RatRoom']; ?>"></div>
					</div>
				</li>
			</ul>
		</div>

		<div class="reviews_collection js-howto-ct" data-howto-id="reviews_collection">
			<h4 class="reviews_title mhxm">Как мы собираем отзывы</h4>

			<ul class="steps_list">
				<li class="steps_item i-step-request">Мы записываем Вас к врачу</li>
				<li class="steps_item i-step-doctor">Вы приходите на прием</li>
				<li class="steps_item i-step-call">Мы звоним и спрашиваем Ваше мнение о враче</li>
				<li class="steps_item i-step-review">И публикуем его в анкете врача</li>
			</ul>

			<p class="mbn">
				Отзывы собираются нами в ходе телефонного и электронного опроса пациентов,
				записавшихся на прием к врачу через портал DocDoc.
			</p>

			<p class="ui-border_b mvn pvm">
				Во избежание появления на портале рекламных или антирекламных отзывов мы публикуем
				только отзывы, полученные от людей, которые записывались к врачу через наш сервис.
			</p>
		</div>

		<p class="mvs t-right">
			<span class="reviews_how ps js-howto-tr" data-howto-id-target="reviews_collection" data-alt-text="свернуть">
				как мы собираем отзывы
			</span>
		</p>

		<h4 class="reviews_title mhxm">Отзывы пациентов о враче</h4>

		<ul class="reviews mhxm">
			<?php foreach ($reviews as $review): ?>

				<li class="reviews_item">
					<div class="review_info">
						<span class="review_overall_rating">
							<?php echo DoctorOpinionModel::getRatingInWord($review->getTotalRating()); ?>
						</span>
						<span class="review_author">
							<?php echo $review->name; ?>
						</span>
						<span class="review_date">
							<?php echo DateTimeUtils::timeToDate(strtotime($review->created)); ?>
						</span>
					</div>
					<div class="review_content">
						<ul class="review_ratings">
							<li class="review_ratings_item">
								<div class="l-ib js-tooltip-tr"
									 title="Насколько врач подробно ответил на все вопросы по курсу лечения или диагностики.">
									<span class="review_ratings_label">Квалификация</span>
									<div class="rating_stars js-rating-small" data-score="<?php echo $review->rating_qualification; ?>"></div>
								</div>
							</li>
							<li class="review_ratings_item">
								<div class="l-ib js-tooltip-tr"
									 title="Насколько врач был внимателен и тактичен по отношению к пациенту.">
									<span class="review_ratings_label">Внимание</span>
									<div class="rating_stars js-rating-small" data-score="<?php echo $review->rating_attention; ?>"></div>
								</div>
							</li>
							<li class="review_ratings_item">
								<div class="l-ib js-tooltip-tr"
									 title="Насколько цена приема соответствует качеству обслуживания и полученным результатам.">
									<span class="review_ratings_label">Цена-качество</span>
									<div class="rating_stars js-rating-small" data-score="<?php echo $review->rating_room; ?>"></div>
								</div>
							</li>
						</ul>
						<p class="review_text">
							<?php echo $review->text; ?>
						</p>
					</div>
				</li>

			<?php endforeach; ?>

			<?php if ($countMore > 0): ?>
				<li class="reviews_item mtl t-center">
					<a href="/opinion/more/<?php echo $doctor->id; ?>" class="ps reviews_show_all js-showall">
						и еще <?php echo $countMore . ' ' . RussianTextUtils::caseForNumber($countMore, ['отзыв', 'отзыва', 'отзывов']); ?>
					</a>
				</li>
			<?php endif; ?>
		</ul>

	<?php endif; ?>

	<?php echo $this->renderPartial('/elements/socialLinks'); ?>

	<?php echo $this->renderPartial('/doctors/reviewForm', ['doctor' => $doctor]); ?>

</section>
