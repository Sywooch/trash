<?php
/**
 * @var array $services
 */
?>

<div class="title">Запись на приём в «<?=$clinic->name?>»</div>
<!-- end .title-->
<ul class="steps">
	<li class="js-service-tab active">
		<div class="big">1.Услуга</div>
		Выберите, на что вы<br>
		хотели бы записаться
	</li>
	<li class="js-doctor-tab">
		<div class="big">2.Специалист</div>
		Выберите врача, к которому<br>
		хотите записаться
	</li>
	<li class="js-contacts-form-tab">
		<div class="big">3.Контакты</div>
		Укажите имя и<br>
		ваш телефон
	</li>
</ul>
<!-- end .steps-->

<div class="services js-services js-block">
	<div class="scroll">
		<div class="types">
		<?php foreach($services as $col) {?>
			<div class="fl">
				<div class="hold">
					<?php foreach ($col as $letter => $group) {?>
						<div class="letter"><?=$letter?></div>
						<ul>
							<?php foreach ($group as $service) {?>
								<li>
									<span class="js-service" data-id="<?=$service['id']?>" data-type="<?=$service['type']?>"><?=$service['name']?></span>
								</li>
							<?php }?>
						</ul>
					<?php }?>
				</div>
				<!-- end .hold-->
			</div>
			<!-- end .fl-->
		<?php }?>
		</div>
	</div>
</div>
<!-- end .types-->

<div class="doctors js-doctors js-block"></div>
<!-- end .doctors-->

<div class="form js-contacts-form js-block">
	<?php $this->renderPartial('contactsForm', ['clinic' => $clinic]);?>
</div>
<!-- end .form-->

<div class="done js-success js-block">
	<div class="inline">
		<div class="cell">Ваша заявка о записи на прием к врачу отправлена.<br>
			<br>
			Наши консультанты свяжутся с вами в течение 15 минут<br>
			ежедневно с 9:00 до 21:00 и запишут Вас на прием.</div>
	</div>
</div>
<!-- end .done-->

<div class="footer">
	<div class="logo"><img src="/img/appointment/dd-logo.png" width="121" height="35" alt="DocDoc.ru"></div>
	<!-- end .logo-->
	<a class="btn light js-back hidden" href="#">Назад</a>
	<a class="btn js-book hidden" href="#">Записаться</a>
	<a class="btn js-next" href="#">Далее</a>
</div>
<!-- end .footer-->
