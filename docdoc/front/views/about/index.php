<?php
/**
 * @var \dfs\docdoc\components\DocDocStat $docDocStat
 */
?>

<main class="l-main l-wrapper section_about_wrap" role="main">

	<?php echo $this->renderPartial('leftmenu', [ 'menu' => $this->_leftmenu, 'active' => 'about' ]); ?>

	<section class="page_about" style="float:right;">

		<article class="aboutus_item">

			<h3>О нас</h3>

			<p>DocDoc.ru – это сервис по поиску врачей. Мы стремимся помочь людям оперативно найти хорошего доктора и
				записаться к нему на прием. Для этого мы создали базу врачей, собираем отзывы у пациентов после приема и
				публикуем их на сайте. Мы предоставляем максимально подробную информацию о специалисте (опыт,
				квалификация, специализация, расписание), которая формирует его рейтинг. Оставить заявку на прием можно
				прямо в анкете врача или по телефону нашего сервиса.</p>

			<p><strong>Основная цель сервиса</strong> – упростить процесс записи пациента на прием к выбранному
				специалисту и сделать работу врачей и клиник более прозрачной для потребителей.</p>

			<div class="aboutus_item_map">
				<div class="aboutus_item_dotted">
					
				<h3>DocDoc в России</h3>
				<div class="l-ib">
					<ul>
						<li><span>Москва</span></li>
						<li><span>Санкт-Петербург</span></li>
						<li><span>Екатеринбург</span></li>
						<li><span>Новосибирск</span></li>
					</ul>
				</div><div class="l-ib">
					<ul>
						<li><span>Пермь</span></li>
						<li><span>Самара</span></li>
						<li><span>Казань</span></li>
						<li><span>Нижний Новгород</span></li>
					</ul>
				</div>
				</div>
			</div>
			<div class="aboutus_item_dotted aboutus_item_advantages">
				<h3>Преимущества</h3>
				<div class="aboutus_item_advantages-item">
					<div class="aboutus_item_advantages-head">Врач рядом с вами</div>
					Обширная база специалистов позволяет найти врача в любом районе города.
				</div>
				<div class="aboutus_item_advantages-item aboutus_item_advantages-rub">
					<div class="aboutus_item_advantages-head">Лучшая цена</div>
					Записываясь к врачу через DocDoc.ru, Вы получаете скидки на первую консультацию врача.					
				</div>
				<div class="aboutus_item_advantages-item aboutus_item_advantages-star">
					<div class="aboutus_item_advantages-head">Подлинные отзывы</div>
					Отзывы о врачах публикуются только после проверки модераторами на достоверность					
				</div>
			</div>
			<div class="aboutus_item_dotted ">
				<h3>DocDoc сегодня</h3>
				<div class="aboutus_item_today">
					<div class="l-ib">
						<div class="aboutus_item_today-item">
							<div class="aboutus_item_today-big">
								<?php
								echo number_format(round($docDocStat->getRequestsCount(false), -3), null, null, ' ');
								?> +
							</div>
							пациентов записались к врачу
						</div>
					</div><div class="l-ib">
						<div class="aboutus_item_today-item aboutus_item_today-opinions">
							<div class="aboutus_item_today-big">
								<?php
								echo number_format(round($docDocStat->getReviewsCount(), -2), null, null, ' ');
								?> +
							</div>
							проверенных отзывов
						</div>
					</div><div class="l-ib">
						<div class="aboutus_item_today-item aboutus_item_today-doctors">
							<div class="aboutus_item_today-big">
								<?php
								echo number_format(round($docDocStat->getDoctorsCount(), -2), null, null, ' ');
								?> +
							</div>
							врачей на портале
						</div>
					</div><div class="l-ib">
						<div class="aboutus_item_today-item aboutus_item_today-clinics">
							<div class="aboutus_item_today-big">
								<?php
								echo number_format(round($docDocStat->getPartnersCount(), -1), null, null, ' ');
								?> +
							</div>
							клиник-партнеров
						</div>
					</div>
				</div>
			</div>
			<div class="aboutus_item_dotted aboutus_item_awards">
				<h3>Награды и достижения</h3>
				<div class="aboutus_item_awards">
					<div class="aboutus_item_awards-item l-ib">
						<div class="aboutus_item_awards-text">
							Наш проект стал финалистом конкурса «Лучший стартап 2012/2013» по версии журнала Forbes.
						</div>
					</div><div class="aboutus_item_awards-item aboutus_item_awards-rbc  l-ib">
						<div class="aboutus_item_awards-text">
							DocDoc.ru занимает 53% рынка<br> записи к врачу через Интернет по данным исследования РБК.
						</div>
					</div><div class="aboutus_item_awards-item aboutus_item_awards-komersant l-ib">
						<div class="aboutus_item_awards-text">
							Мы вошли в список лучших команд стартапов Рунета по версии делового журнала «Секрет Фирмы».
						</div>
					</div>
				</div>
			</div>

		</article>

	</section>

</main>
