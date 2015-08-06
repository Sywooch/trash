<?php
/**
 * @var dfs\docdoc\objects\Phone $phoneForPage
 */
?>

<form class="req_form req_form_clinic" method="post" action="/routing.php?r=request/save" novalidate="novalidate">

	<h3 class="request_title mvn">Запишитесь на прием в эту клинику</h3>

	<?php if ($phoneForPage): ?>
		<p class="t-teal mvn strong request_phone">
			по тел.:
			<span class="js-request-tel"><?php echo $phoneForPage->prettyFormat(); ?></span>
		</p>
		<p class="ui-linethrough">
			<span class="ui-bg_grey t-grey phs">или online</span>
		</p>
	<?php endif; ?>

	<p class="req_form_row">
		<label class="label req_form_label">Клиника</label><span class="t-grey req_form_title js-request-popup-clinic"></span>
	</p>

	<p class="req_form_row">
		<label class="label">
			<span class="req_form_label i-required">Ваше имя:
			</span><input class="dd_input req_form_input" type="text" autofocus="true" placeholder="" name="requestName" />
		</label>
	</p>

	<p class="req_form_row">
		<label class="label">
			<span class="req_form_label i-required">Ваш телефон:
			</span><input class="dd_input req_form_input js-mask-phone" type="text" placeholder="" name="requestPhone" />
		</label>
	</p>

	<p class="req_form_row">
		<span class="req_form_label">Пациент:</span>
		<label class="label_radio strong">
			<input type="radio" class="input_radio" name="requestAgeSelector" value="adult" checked="" />
			Взрослый
		</label>
		<label class="label_radio strong">
			<input type="radio" class="input_radio" name="requestAgeSelector" value="child" />
			Ребенок
		</label>
	</p>

	<p class="req_form_row">
		<span class="req_form_label s-hidden">Комментарий:</span>
		<label for="reqComment" class="label_textarea js-slidedown-tr ps">Добавить комментарий</label>
		<textarea id="reqComment" class="dd_input textarea req_textarea js-slidedown-ct s-closed" name="requestComments"></textarea>
	</p>

	<p class="req_form_row">
		<span class="req_form_label s-hidden">Записаться:</span>
		<input class="req_submit ui-btn ui-btn_green" data-stat="" type="submit" value="Отправить" />
	</p>

	<input type="hidden" name="clinic" value="" />
	<input type="hidden" name="requestBtnType" value="requestPopupBtn" />

</form>
