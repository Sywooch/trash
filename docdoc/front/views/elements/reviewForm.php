<?php
/**
 * @var \dfs\docdoc\models\DoctorModel $doctor
 * @var \dfs\docdoc\models\ClinicModel $clinic
 */
?>

<form class="review_form" method="post" action="/service/createReview.php">

	<h4 class="review_form_title">
		Нам важно ваше мнение! Оставьте отзыв о приеме врача
	</h4>

	<ul class="review_form_ratings">
		<li class="review_form_rating">
			<span class="review_form_label">Обслуживание</span>
			<div class="rating_stars js-rating-small" data-score="" data-editable="true" data-related="rating_qualification"></div>
		</li>
		<li class="review_form_rating">
			<span class="review_form_label">Время ожидания</span>
			<div class="rating_stars js-rating-small" data-score="" data-editable="true" data-related="rating_attention"></div>
		</li>
		<li class="review_form_rating">
			<span class="review_form_label">Цена / качество</span>
			<div class="rating_stars js-rating-small" data-score="" data-editable="true" data-related="rating_room"></div>
		</li>
	</ul>

	<div class="review_form_textarea_ct prm">
		<label class="placeholder_label_textarea">
			<textarea class="textarea dd_input" placeholder="Напишите здесь текст отзыва" name="reviewComment"></textarea>
		</label>
	</div>

	<div class="review_form_inputs_ct">
		<label class="placeholder_label">
			<input class="review_input dd_input mbs" type="text" placeholder="Ваше имя" name="reviewName"/>
		</label>
		<label class="placeholder_label">
			<input class="review_input dd_input mvs js-mask-phone" type="text" placeholder="Ваш телефон" name="reviewPhone"/>
		</label>
		<span class="disclaimer">
			Мы просим указать ваш телефон для контроля достоверности отзывов
			<sup class="helper js-tooltip-tr"
				 title="Во избежание появления рекламных и антирекламных отзывов, все отзывы, оставленные через сайт, проверяются сотрудниками нашего портала.">
				(?)
			</sup>
		</span>
	</div>

	<div class="mtsl t-center">
		<input class="ui-btn ui-btn_teal review_form_btn" type="submit" value="Высказаться"/>
	</div>

	<input type="hidden" name="doctorId" id="doctor-id" value="<?php echo empty($doctor) ? '' : $doctor->id; ?>"/>
	<input type="hidden" name="clinicId" id="clinic-id" value="<?php echo empty($clinic) ? '' : $clinic->id; ?>"/>

</form>
