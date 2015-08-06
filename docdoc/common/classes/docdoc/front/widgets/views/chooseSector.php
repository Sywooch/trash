<?php
/**
 * @var array $services
 */

?>

<div class="request-popup-close" title="Закрыть"></div>

<div class="title">Запись на приём в «<?=$clinic->name?>»</div>

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
		<div class="big">3.Запись</div>
		запишитесь к врачу<br>
		в удобное для Вас время
	</li>
</ul>

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
									<a href="?clinic=<?=$clinic->id?>&speciality=<?=$service['id']?>" class="js-service"><?=$service['name']?></a>
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