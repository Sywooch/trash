<?php

use dfs\docdoc\models\DoctorOpinionModel;
use dfs\docdoc\extensions\DateTimeUtils;

/**
 * @var DoctorOpinionModel $review
 */
?>

<li class="reviews_item">
	<div class="review_info">
		<span class="review_overall_rating t-fs-n">
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
		<p class="review_text">
			<?php echo $review->text; ?>
		</p>
	</div>
	<div class="review_ratings">
		<div class="js-tooltip-tr"
			 title="Насколько врач подробно ответил на все вопросы по курсу лечения или диагностики.">
			<span class="review_ratings_label"><span>Обслуживание</span></span>
			<div class="rating_stars js-rating-small js-rating-small-clinic" data-score="<?php echo $review->rating_qualification; ?>"></div>
		</div>
		<div class="js-tooltip-tr"
			 title="Насколько врач был внимателен и тактичен по отношению к пациенту.">
			<span class="review_ratings_label"><span>Время ожидания</span></span>
			<div class="rating_stars js-rating-small js-rating-small-clinic" data-score="<?php echo $review->rating_attention; ?>"></div>
		</div>
		<div class="js-tooltip-tr"
			 title="Насколько цена приема соответствует качеству обслуживания и полученным результатам.">
			<span class="review_ratings_label"><span>Цена / качество</span></span>
			<div class="rating_stars js-rating-small js-rating-small-clinic" data-score="<?php echo $review->rating_room; ?>"></div>
		</div>
	</div>
</li>
