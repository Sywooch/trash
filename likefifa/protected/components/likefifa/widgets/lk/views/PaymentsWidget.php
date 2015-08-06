<?php
/**
 * @var LfMaster $model
 */
?>

<div class="lk-balance-container">
	<div class="right-block">
		<div class="prof-rating_info">
			<div class="stars png" style="float:left;"><span style="width:<?php echo $model->getRatingPercent(); ?>%" class="png"></span></div>
			Рейтинг: <span><?php echo $model->getRating(); ?></span>
			<span class="show-rating-popup">?</span>
			<div class="popup-note popup-rating">
				<div class="popup-close"></div>
				<div class="popup-note_cont">
					<p><strong>Уважаемые мастера красоты!</strong></p>

					<p>Для того чтобы Ваша анкета появилась на сайте, Вам необходимо заполнить
						<span class="required">обязательные поля</span>:
						ФИО, мобильный телефон, специализация, метро и прайс.</p>
					<p>После этого Ваша анкета опубликуется на сайте, и Вам автоматический присвоится рейтинг 3 сердечка
						(сердечки, не надпись).</p>
					<p>Далее Ваш рейтинг будет формироваться в зависимости от следующих условий:</p>
					<p>
						<strong class="pink"><span class="plus">+</span> 0,2</strong> — заполненно поле e-mail
						<br/><strong class="pink"><span class="plus">+</span> 0,3</strong> — добавлена Ваша аватарка
						<br/><strong class="pink"><span class="plus">+</span> 0,2</strong> — указаны Ваше образование
							или курсы повышения квалификации
						<br/><strong class="pink"><span class="plus">+</span> 0,1</strong> — улица, дом
						<br/><strong class="pink"><span class="plus">+</span> 0,2</strong> — график работы
						<br/><strong class="pink"><span class="plus">+</span> 0,2</strong> — 2 фотографии Ваших работ
						<br/><strong class="pink"><span class="plus">+</span> 0,3</strong> — 5 фотографий Ваших работ
						<br/><strong class="pink"><span class="plus">+</span> 0,5</strong> — более 5 фотографий  Ваших
							работ
						<br/><strong class="pink"><span class="plus">+</span> 0,1</strong> — 1 отзыв
						<br/><strong class="pink"><span class="plus">+</span> 0,2</strong> — 2-3 отзыва
						<br/><strong class="pink"><span class="plus">+</span> 0,5</strong> — 4 и более отзывов
					</p>
				</div>
				<div class="popup-arr"></div>
			</div>
		</div>
		<div class="clear"></div>
		<?php if ($model->is_blocked) { ?>
			<div class="blocked-lk">
				Внимание! Ваша анкета временно заблокирована. (?)
			</div>
		<?php } ?>
	</div>

	<p class="balance">
		Ваш баланс:
		<strong>
			<span class='lk-balance-value'><?php echo $model->getBalance(); ?></span> рублей
		</strong>
		/
		<span class="show_pay">пополнить баланс</span>
		<a href="<?php echo Yii::app()->createUrl("balance"); ?>" class="question" target="_blank">?</a>
		<a href="<?php echo Yii::app()->createUrl("balance"); ?>" class="how" target="_blank">(как пополнить баланс)</a>
	</p>

	<p class="pay">
		Введите сумму, которую вы хотите перечислить:
		&nbsp;<input type='text' size='5' class='pay_sum'/> рублей
		<button class="pay_redirect">Перейти к оплате</button>
	</p>

	<div class="clear"></div>
</div>