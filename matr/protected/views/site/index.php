

<div class="section_w680  dashed_border_bottom">
	<div class="section_w300 dashed_border_right" style="text-align: center; padding-top: 50px;">


		<a class="enter lk-link" href="#"></a>

		<?php if (Yii::app()->user->isGuest) { ?>
			<a href="/registration"><img src="/css/images/reg_button.png"/></a>
			<?php
				echo CHtml::ajaxLink(
					'<img src="/css/images/enter_button.png"/>',
					$this->createUrl("user/login"),
					array(
						"success" => 'function(data) {
		                   $("body").append(data);
		                   $(".login-window .close, .login-overlay").on("click", function(){
		                        $("#login-form-container").remove();
		                   });
		                }',
					),
					array(
						"class" => "enter lk-link",
						"id"    => uniqid(),
						"live"  => false,
					)
				);
				?>
		<?php } else { ?>
			<a href="/logout/" class="lk-link">Выйти</a>
			<a href="/lk/" class="lk-link">Личный кабинет</a>
		<?php } ?>

		<div class="cleaner"></div>
	</div>

	<div id="site_title">
	</div>

	<div class="cleaner"></div>
</div>

<div class="section_w680 dashed_border_bottom">
	<div class="section_w300 dashed_border_right">

		<h2><span>01</span>Как заработать</h2>

		<div class="section_w70">
			<img class="image_wrapper" src="/css/images/money.jpg"/>
		</div>
		<div class="section_w220">

			<p>Интернет магазин по продаже русской традиционной матрешки ручной работы с автоматической
				системой денежных вознаграждений! </p>

			<p>Наш магазин щедро вознаграждает своих клиентов за рекомендации!</p>

			<div class="button_01 button_orange"><a href="/how">Узнать подробнее</a></div>
		</div>

		<div class="cleaner"></div>
	</div>

	<div class="section_w300">

		<h2><span>02</span>Возникли вопросы?</h2>

		<div class="section_w70">
			<img class="image_wrapper" src="/css/images/questions.jpg"/>
		</div>
		<div class="section_w220">

			<p>Как стать участником? Как выплачивыется вознаграждение? Как долго ждать заказ?</p>

			<p>На эти и многие другие вопросы вы найдете ответы в этом разделе.</p>

			<div class="button_01 button_blue"><a href="/faq">Узнать подробнее</a></div>
		</div>

		<div class="cleaner"></div>
	</div>
	<div class="cleaner"></div>
</div>

<div class="section_w680 dashed_border_bottom">
	<div class="section_w300 dashed_border_right">

		<h2><span>03</span>Акции</h2>

		<div class="section_w70">
			<img class="image_wrapper" src="/css/images/actions.jpg"/>
		</div>
		<div class="section_w220">

			<p>Будьте в курсе наших новостей и акций</p>

			<div class="button_01 button_green"><a href="/news">Узнать подробнее</a></div>
		</div>

		<div class="cleaner"></div>
	</div>

	<div class="section_w300">

		<h2><span>04</span>С чего все началось</h2>

		<div class="section_w70">
			<img class="image_wrapper" src="/css/images/matr.jpg"/>
		</div>
		<div class="section_w220">

			<p>Русская деревянная расписная кукла появилась в России в 90-х годах XIX века...</p>

			<div class="button_01 button_blue"><a href="/history">Узнать подробнее</a></div>
		</div>

		<div class="cleaner"></div>
	</div>
	<div class="cleaner"></div>
</div>


<div class="cleaner"></div>

<div style="padding-top: 30px;">
	<?php echo Text::model()->findByPk(1)->text; ?>
</div>