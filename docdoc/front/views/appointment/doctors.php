<?php

use dfs\docdoc\extensions\TextUtils;

/**
 * @var dfs\docdoc\models\DoctorModel[] $doctors
 */
?>

<div class="any"><a class="btn grey js-doctor" href="#" data-id="">Любой специалист</a></div>
<!-- end .any-->
<div class="scroll">
	<div class="hold">
		<?php foreach ($doctors as $doctor) {?>
			<figure>
				<div class="name"><a class="js-doctor" href="#" data-id="<?=$doctor->id?>"><?=$doctor->name?></a></div>
				<!-- end .name-->
				<div class="clearfix">
					<div class="photo">
						<a class="js-doctor" href="#" data-id="<?=$doctor->id?>">
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
				<div class="btn-hold"> <a class="btn grey js-doctor" href="#" data-id="<?=$doctor->id?>">выбрать</a> </div>
				<!-- end .btn-hold-->
			</figure>
			<!-- end figure-->
		<?php }?>
	</div>
	<!-- end .hold-->
</div>
<!-- end .scroll-->