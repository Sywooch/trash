<?php $this->pageTitle = 'Как работает LikeFifa?'; ?>
<div class="content-wrap content-pad-bottom">
	<div class="about-page">
		<h1><?php echo $this->pageTitle; ?></h1>
		<p style="text-align:center; margin-bottom:40px;">LikeFifa – это новый масштабный интернет-проект, который объединяет на одной площадке мастеров красоты и их клиентов. </p>
		<div class="about-page_item">
			<img src="/i/about/about-1.jpg" alt="" />
			<div class="about-page_head">Найдите своего мастера!</div>
			<p>Вам больше не придется тратить время на поиск нужного мастера в поисковых системах и социальных сетях. У нас есть более 1500 профессиональных специалистов. Просто введите нужную Вам услугу и выберите из списка мастеров того, что Вам понравится больше.</p>
			<a href="<?php echo $this->forMasters()->createSearchUrl(); ?>">перейти к списку мастеров</a>
		</div>

		<div style="text-align:right" class="about-page_item about-page_item__right">
			<img src="/i/about/about-2.jpg" alt="" />
			<div class="about-page_head">Найдите свой салон красоты!</div>
			<p>Вам нужно найти салон, который бы максимально отвечал Вашим требованиям? У нас есть множество вариантов – Вы наверняка найдете нужный. Местоположение, ценовой диапазон и даже работающие в нем мастера – все это Вы можете посмотреть на страничке каждого салона.</p>
			<a href="<?php echo $this->forSalons()->createSearchUrl(); ?>">перейти к списку салонов</a>
		</div>

		<div class="about-page_item">
			<img src="/i/about/about-3.jpg" alt="" />
			<div class="about-page_head">Удобно для Вас!</div>
			<p>Главное для Вас – это экономия времени? Найдите ближайшего к Вам частного мастера или салон красоты на нашей карте.</p>
			<a href="<?php echo $this->forMasters()->createMapUrl(); ?>">перейти к поиску по карте</a>
		</div>

		<div class="about-page_item about-page_item__right" style="padding-top:42px;">
			<img src="/i/about/about-4.jpg" alt="" />
			<div class="about-page_head">Запишитесь к мастеру онлайн!</div>
			<p>Просто нажмите на кнопку «Записаться» и оставьте свои контактные данные. Мастер сам свяжется с Вами в течение двух часов.</p>
		</div>
		<div class="about-page_item">
			<img src="/i/about/about-5.jpg" alt="" />
			<div class="about-page_head">Оставьте отзыв!</div>
			<p>Уже сходили к мастеру? Оставьте отзыв о его работе! На странице каждого мастера и каждого салона есть удобное поле, в котором Вы можете описать впечатления посещения. А еще Вы можете «лайкнуть» фотографии работ. Лучшие попадут на главную страницу нашего сайта!</p>
		</div>
		<br/>
		<br/>
		<br/>
		<p style="text-align:center; font-style:italic; font-size:17px;"><strong>С <span style="color:#d1288a;">LikeFifa</span> мастеру легко найти своего клиента, а клиенту – своего мастера красоты.</strong></p>
	</div>
</div>