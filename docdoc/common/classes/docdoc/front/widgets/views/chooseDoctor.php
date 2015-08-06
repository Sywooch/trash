<?php

use dfs\docdoc\extensions\TextUtils;

/**
 * @var dfs\docdoc\models\DoctorModel[] $doctors
 */

?>

<div class="request-popup-close" title="Закрыть"></div>

<div class="title">Запись на приём в «<?=$clinic->name?>»</div>

<ul class="steps">
	<li class="js-service-tab">
		<a href="/request/form?clinic=<?=$clinic->id?>">
		<div class="big">1.Услуга</div>
		Выберите, на что вы<br>
		хотели бы записаться
			</a>
	</li>
	<li class="js-doctor-tab active">
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

<!-- end .any-->
<div class="scroll doctors">
	<div class="hold">
		<?php foreach ($doctors as $doctor) {?>
			<figure>
				<div class="name"><a class="js-doctor" href="?clinic=<?=$clinic->id?>&doctor=<?=$doctor->id?>" data-id="<?=$doctor->id?>"><?=$doctor->name?></a></div>
				<!-- end .name-->
				<div class="clearfix">
					<div class="photo">
						<a class="js-doctor" href="?clinic=<?=$clinic->id?>&doctor=<?=$doctor->id?>" data-id="<?=$doctor->id?>">
							<img src="<?=$doctor->getImg()?>" width="78" height="107" alt="<?=$doctor->name?>">
						</a>
					</div>
					<!-- end .photo-->
					<div class="rating"><strong><?=$doctor->getDoctorRating()?></strong> / 10 </div>
					<!-- end .rating-->
				</div>
				<div class="text">
					<?=implode(', ', $doctor->getSpecialityNames())?>.
					Стаж <?=$doctor->getExperience()?> <?=TextUtils::caseForNumber($doctor->getExperience(), ['год', 'года', 'лет'])?>.<br>
					<?=$doctor->getCategory()?> <?=$doctor->getDegree()?></div>
				<!-- end .text-->
				<div class="btn-hold"> <a class="btn grey js-doctor" href="?clinic=<?=$clinic->id?>&doctor=<?=$doctor->id?>" data-id="<?=$doctor->id?>">выбрать</a> </div>
				<!-- end .btn-hold-->
			</figure>
			<!-- end figure-->
		<?php }?>
	</div>
	<!-- end .hold-->
</div>
<!-- end .scroll-->