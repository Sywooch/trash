<?php
/**
 * @var string $partnersEmail
 */
?>

<main class="l-main l-wrapper section_about_wrap" role="main">

	<?php echo $this->renderPartial('leftmenu', ['menu' => $this->_leftmenu, 'active' => 'faq']); ?>

	<section class="page_about" style="float:right;">

		<article>
			<div class="page_faq_wrap">
				<h3>О DocDoc.ru</h3>

				<div class="page_faq_items">
					<div class="page_faq_item">
						<div class="page_faq_head">Что такое DocDoc.ru?</div>
						<div class="page_faq_text" style="display:block;">DocDoc.ru – это сервис по поиску врачей и
							записи к ним на прием. Мы помогаем нашим посетителям выбрать хорошего специалиста рядом с
							домом и по доступной цене.
						</div>
					</div>
					<div class="page_faq_item">
						<div class="page_faq_head">DocDoc.ru – это клиника?</div>
						<div class="page_faq_text">Нет, DocDoc.ru – это сервис, где можно найти и записаться в
							подходящую клинику. Портал DocDoc.ru не оказывает медицинские услуги.
						</div>
					</div>
					<div class="page_faq_item">
						<div class="page_faq_head">Могу ли я записаться к любому врачу России?</div>
						<div class="page_faq_text">Пока нет. Сейчас DocDoc.ru работает только в 8 городах России, а
							именно Москве, Санкт-Петербурге, Екатеринбурге, Нижнем Новгороде, Новосибирске, Перми,
							Самаре и Казани. Мы активно развиваемся и собираемся со временем открыть филиалы поиска
							врача по всей России.
						</div>
					</div>
					<div class="page_faq_item">
						<div class="page_faq_head">DocDoc.ru бесплатен для пациентов?</div>
						<div class="page_faq_text">Да, сервис поиска врача абсолютно бесплатен для пациентов. Оплата
							услуг происходит в клинике, куда обратился пациент.
						</div>
					</div>
					<div class="page_faq_item">
						<div class="page_faq_head">Сколько стоит прием врача через DocDoc.ru?</div>
						<div class="page_faq_text">Стоимость приема врача зависит от клиники, в которой он работает. Со
							своей стороны мы гарантируем, что не завышаем стоимость услуг и цены соответствуют
							официальному прайсу клиники.
						</div>
					</div>
					<div class="page_faq_item">
						<div class="page_faq_head">Что такое Diagnostica.DocDoc.ru?</div>
						<div class="page_faq_text">Diagnostica.DocDoc.ru – это сервис по поиску диагностических центров.
							Как и на DocDoc.ru здесь можно найти нужное исследование, просмотреть клиники, их
							месторасположение, стоимость того или иного обследования, а также записаться по телефону.
						</div>
					</div>
				</div>
				<div class="aboutus_item_dotted">
					<h3>О записи к врачу</h3>

					<div class="page_faq_item">
						<div class="page_faq_head">Как выбрать врача на ДокДок.ру?</div>
						<div class="page_faq_text">Выберите специальность врача и удобную станцию метро. Из
							представленных специалистов Вы сможете выбрать врача, подходящего Вашим критериям. Можно
							отсортировать список по стажу, стоимости приема. Отдельно отмечены специалисты, которые
							выезжают на дом и принимают детей. Хорошего врача Вам так же посоветуют в нашем колл-центре
							по телефону.
						</div>
					</div>
					<div class="page_faq_item">
						<div class="page_faq_head">Как записаться на прием?</div>
						<div class="page_faq_text">Записаться на прием можно по телефону или оставить заявку в анкете
							врача.
						</div>
					</div>
					<div class="page_faq_item">
						<div class="page_faq_head">Как оплачивается прием врача?</div>
						<div class="page_faq_text">Прием врача оплачивается после консультации в клинике. DocDoc.ru не
							взимает плату за запись на прием.
						</div>
					</div>
					<div class="page_faq_item">
						<div class="page_faq_head">Как я могу оставить отзыв?</div>
						<div class="page_faq_text">Все отзывы на нашем сайте проходят строгую проверку на подлинность.
							После приема наши сотрудники сами связываются с пациентами, чтобы собрать отзыв. Отзывы,
							оставленные на сайте, требуют подтверждения модератором перед публикацией.
						</div>
					</div>
				</div>
				<div class="aboutus_item_dotted">
					<h3>Сотрудничество</h3>

					<div class="page_faq_item">
						<div class="page_faq_head">Как стать партнером DocDoc.ru?</div>
						<div class="page_faq_text">Все вопросы по партнерской программе нашего сайта можно задать
							Владимиру Никишкову по адресу: <a
								href="mailto:<?php echo $partnersEmail; ?>"><?php echo $partnersEmail; ?></a></div>
					</div>
					<div class="page_faq_item">
						<div class="page_faq_head">Как войти в Личный Кабинет?</div>
						<div class="page_faq_text">Логин и пароль к Личному Кабинету получают клиники партнеры от отдела
							аккаунтинга после подписания договора.
						</div>
					</div>
					<div class="page_faq_item">
						<div class="page_faq_head">Как разместить анкеты врачей?</div>
						<div class="page_faq_text">Анкеты врачей размещаются через отдел аккаунтинга в течение 3 рабочих
							дней после предоставления информации.
						</div>
					</div>
					<div class="page_faq_item">
						<div class="page_faq_head">Как DocDoc.ru зарабатывает деньги?</div>
						<div class="page_faq_text">DocDoc.ru бесплатен для пациентов. Для клиник работает система оплаты
							за результат. Стоимость записи на прием рассчитывается индивидуально для каждого
							медицинского центра.
						</div>
					</div>
					<div class="page_faq_item">
						<div class="page_faq_head">Как зарегистрироваться как клиника?</div>
						<div class="page_faq_text">Чтобы зарегистрироваться как клиника, заполните данные о клинике,
							телефон и e-mail в разделе «Регистрация». После этого менеджеры свяжутся с Вами и объяснять
							дальнейшие шаги.
						</div>
					</div>
					<div class="page_faq_item">
						<div class="page_faq_head">Как зарегистрироваться как частный врач?</div>
						<div class="page_faq_text">Чтобы зарегистрироваться как частный врач, заполните данные о Вас,
							телефон и e-mail в разделе «Регистрация». После этого менеджеры свяжутся с Вами и объяснять
							дальнейшие шаги.
						</div>
					</div>
				</div>
			</div>

		</article>

	</section>

</main>
