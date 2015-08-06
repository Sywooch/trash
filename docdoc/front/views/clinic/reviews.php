<?php

use dfs\docdoc\models\ClinicModel;
use dfs\docdoc\models\DoctorOpinionModel;

/**
 * @var ClinicModel $clinic
 * @var array $avgRatings
 * @var DoctorOpinionModel[] $reviews
 * @var int $countReviews
 */

$countMore = $countReviews - count($reviews);
$countMoreLimit = $countMore > 10 ? 10 : $countMore;
?>

<section class="clinic_card_cont clinic_reviews js-reviews-rating-big" id="reviews">

	<?php if ($countReviews): ?>

		<h2 class="t-fs-l mbs">Отзывы о клинике "<?php echo $clinic->name; ?>"</h2>

		<div class="reviews_header ui-grad-yellow-light">
			<ul class="reviews_total_rating">
				<li class="total_rating_text total_rating_item t-center">
					<?php echo DoctorOpinionModel::getRatingInWord($avgRatings['RatTotal']); ?>
					<div class="total_rating_text_small">На основе <?php echo $countReviews . ' ' . RussianTextUtils::caseForNumber($countReviews, ['отзыва', 'отзывов', 'отзывов']); ?></div>
				</li>
				<li class="total_rating_item">
					<div class="l-ib js-tooltip-tr"
						 title="Насколько врач подробно ответил на все вопросы по курсу лечения или диагностики.">
						<span class="review_ratings_label">Квалификация</span>
						<div class="rating_stars js-rating" data-score="<?php echo $avgRatings['RatQualification']; ?>"></div>
					</div>
				</li>
				<li class="total_rating_item total_rating_item_3">
					<div class="l-ib js-tooltip-tr"
						 title="Насколько врач был внимателен и тактичен по отношению к пациенту.">
						<span class="review_ratings_label">Внимание</span>
						<div class="rating_stars js-rating" data-score="<?php echo $avgRatings['RatAttention']; ?>"></div>
					</div>
				</li>
				<li class="total_rating_item total_rating_item_4">
					<div class="l-ib js-tooltip-tr"
						 title="Насколько цена приема соответствует качеству обслуживания и полученным результатам.">
						<span class="review_ratings_label">Цена-качество</span>
						<div class="rating_stars js-rating" data-score="<?php echo $avgRatings['RatRoom']; ?>"></div>
					</div>
				</li>
			</ul>
		</div>

		<noindex>
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
		</noindex>

		<ul class="reviews">
			<?php
				foreach ($reviews as $review) {
					echo $this->renderPartial('/clinic/review', ['review' => $review]);
				}
			?>
			<noindex class="js-more-reviews"></noindex>
		</ul>

		<?php if ($countMore > 0): ?>
			<div class="clinic_reviews_more_link t-center">
				<a href="#" class="ps reviews_show_all js-show-more" data-clinic-id="<?php echo $clinic->id; ?>" data-count-more="<?php echo $countMore; ?>">
					и еще <?php echo $countMoreLimit . ' ' . RussianTextUtils::caseForNumber($countMoreLimit, ['отзыв', 'отзыва', 'отзывов']); ?>
				</a>
			</div>
		<?php endif; ?>

	<?php endif; ?>

	<?php // echo $this->renderPartial('/elements/reviewForm', ['clinic' => $clinic]); ?>

</section>
