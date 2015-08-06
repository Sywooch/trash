<?php

use dfs\models\DistrictModel;

/**
 * @var SiteController $this
 * @var string $message
 * @var integer $code
 * @var array          $metroList
 * @var array          $districtList
 * @var StatModel      $model
 */
$this->pageTitle = "DocDoc - поиск врачей в г. " . Yii::app()->city->getModel()->getName();
?>

<div data-role="page" id="index-page" data-title="<?php echo $this->pageTitle;?>">
	<?php echo $this->renderPartial('//blocks/_right_panel');  ?>
	<div data-role="header">
		<div class="fixed-inn">
			<a href="#rightPanel" class="panel-btn"></a>
		</div>
    </div>
    <div class="vertical-center">
        <div class="index-img-wrapper"><i></i></div>
        <div role="main" class="ui-content">
            <div class="index-main-block-wrapper">
                <div class="index-main-block">
					<form
						class="search-block-doctors"
						method="post"
						action=""
						data-href="<?php echo Yii::app()->createUrl("site/index");?>"
						data-location-type="<?php echo Yii::app()->city->getModel()->hasMetro() ? "metro" : "district"; ?>"
						>
						<h3 class="title">Найдем хорошего Врача<br>в вашем районе</h3>
						<div class="imb-content">
							<div class="top-index-link f-s">
								<select name="specialist" id="select-specialist">
									<option>Выбрать специалиста</option>
									<?php foreach($specialityList as $specLetter => $specItems){?>
										<?php
										/**
										 * @var SpecialityModel[] $specItems
										 */
										foreach ($specItems as $spec) { if ($spec->isSimple()) {
										?>
											<option
												data-alias="<?php echo $spec->getAlias();?>"
												<?php echo ($this->getActiveSpecId() == $spec->getId()) ? " selected" : ""; ?>
												><?php echo $spec->getName();?></option>
										<?php } } ?>
									<?php }?>
								</select>
							</div>
							<?php if (Yii::app()->city->getModel()->hasMetro()) { ?>
							<div class="top-index-link f-m">
								<select name="location" id="select-location">
									<option>Ближайшее метро</option>
								<?php foreach($metroList as $metroLetter => $metroItems){?>
									<?php
									/**
									 * @var MetroModel[] $metroItems
									 */
									foreach($metroItems as $metro){?>
										<option
											data-alias="<?php echo $metro->getAlias();?>"
											data-id="<?php echo $metro->getId();?>"
											<?php echo ($this->getActiveMetroId() == $metro->getId()) ? " selected" : ""; ?>
											><?php echo $metro->getName();?></option>
									<?php }?>

								<?php }?>
								</select>
							</div>
							<?php } else { ?>
								<div class="top-index-link f-m">
									<select name="location" id="select-location">
										<option>Ближайший район</option>
										<?php foreach ($districtList as $districtLetter => $districtItems) { ?>
											<?php
											/**
											 * @var DistrictModel[] $districtItems
											 */
											?>
											<?php foreach ($districtItems as $district) { ?>
												<option data-alias="<?php echo $district->getAlias(); ?>"
													data-id="<?php echo $district->getId(); ?>"><?php echo $district->getName(
														); ?></option>
											<?php } ?>
										<?php } ?>
									</select>
								</div>
							<?php } ?>
						</div>
						<div class="center">
							<div class="find-doctor">
								<a id="find-doctor-search" data-transition="slide"><i></i>Найти</a>
							</div>
						</div>
					</form>
				</div>
            </div>
			<div class="call-us">
				<div class="title">Вам удобнее по телефону?</div>
				<div class="tel"><a href='tel:<?php echo $city->getPhoneSkypeFormat();?>'><?php echo $city->getPhone();?></a></div>
				Посоветуем врача и<br>запишем на прием
			</div>
        </div>
        <div data-role="footer">
			<div class="footer-man"></div>
			<ul class="items-listing">
				<li class="item-2">
					<strong><?php echo Yii::t('', '{n} врач|{n} врача|{n} врачей', [$model->getDoctors()]); ?></strong>
					<span>Врачи почти пятидесяти специальностей по всей Москве</span>
					<i></i>
				</li>
				<li class="item-3">
					<strong>
						<?php echo Yii::t('', '{n} отзыв|{n} отзыва|{n} отзывов', [$model->getReviews()]); ?>
					</strong>
					<span>Проверенные отзывы от реальных пациентов, система рейтингов</span>
					<i></i>
				</li>
				<li class="item-1">
					<strong>
						<?php echo Yii::t('', '{n} запись|{n} записи|{n} записей', [$model->getRequests()]); ?>
					</strong>
					<span>Нам доверяют сотни тысяч пациентов, выбирайте врача и записывайтесь на прием</span>
					<i></i>
				</li>

			</ul>
			<div class="v-mobile">
				Мобильная версия сервиса DocDoc.ru<br>
				<?php echo $this->renderPartial('//blocks/_main_site');  ?>
			</div>
        </div>
    </div>
	<div class="mask-overlay"></div>
</div>
