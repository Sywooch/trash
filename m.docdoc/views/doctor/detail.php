<?php
/**
 * @var SiteController $this
 * @var string $message
 * @var integer $code
 * @var DoctorModel $doctor
 */
$this->pageTitle = "Врач {$doctor->getName()}. Запись на прием - DocDoc.ru";
?>

<div data-role="page" id="page-doctor" data-title="<?php echo $this->pageTitle;?>" data-doctor-id="<?php echo $doctor->getId();?>" data-doctor-clinic-id="<?php echo $doctor->getFirstClinicId();?>">
<?php echo $this->renderPartial('//blocks/_right_panel');  ?>

<div data-tap-toggle="false" class="fixed header not-style">
    <div class="fixed-inn">
        <a
			href="<?php echo $refURL ?: Yii::app()->createUrl('site/index'); ?>"
			data-transition="slide"
			data-direction="reverse"
			class="back-link"
		></a><strong>Информация о враче</strong>
        <a href="#rightPanel" class="panel-btn"></a>
    </div>
</div>
	<div class="fixed-inn w-500 doctor-info-block doctor-info-block-detail">
		<?php echo $this->renderPartial('//blocks/_doctor_info', array('doctor' => $doctor, 'view_map' => true));  ?>
	</div>
	<?php
	if (!$doctor->isActive()) {
		$specialities = $doctor->getSpecialities();
		if ($specialities) {
	?>
	<div class="request_unaviable">
		<a href="<?php echo $this->createUrl("search/search", ["speciality" => $specialities[0]->getAlias()]); ?>">
			Уважаемые посетители, в настоящий момент запись к данному врачу ограничена.
			Вы можете выбрать из доступных
			<?php echo mb_strtolower($specialities[0]->getNamePluralGenitive(), "UTF-8"); ?>
			или записаться по телефону
		</a>
	</div>
	<?php } } ?>
<div role="main" class="ui-content doctor-info-content">
    <p class="main-info"><?php echo $doctor->getDescription();?></p>
    <div class="doctor-info-item"><strong class="w-img img-1"><i></i> Специализация</strong>
        <div class="skills-listing">
            <?php echo $doctor->getTextSpec();?>
        </div>
    </div>

    <?php if($doctor->getTextEducation()){?>
    <div class="doctor-info-item"><strong class="w-img img-2"><i></i> Образование</strong>
        <div class="skills-listing">
            <?php echo $doctor->getTextEducation();?>
        </div>
    </div>
    <?php } ?>

    <?php if($doctor->getTextCourse()){?>
    <div class="doctor-info-item"><strong class="w-img img-3"><i></i> Курсы повышения квалификации</strong>
        <div class="skills-listing">
            <ul>
                <li><?php echo $doctor->getTextCourse();?></li>
            </ul>
        </div>
    </div>
    <?php } ?>

    <?php if(count($doctor->getReviews()) > 0){?>
    <div class="doctor-info-item"><strong class="w-img img-4"><i></i> Отзывы пациентов о враче:</strong>
        <?php $countReview = count($doctor->getReviews());?>
        <div class="total-review-block"><strong>Общее мнение пациентов по <?php echo Yii::t('', '{n}му|{n}м|{n}ти', array($countReview)); ?>
                <?php echo Yii::t('', 'отзыву|отзывам|отзывам', array($countReview)); ?> :</strong><span class="total"><?php echo $doctor->getRatingInWord();?></span>
            <div class="item">
                <?php echo $this->renderPartial('//blocks/_rating', array('rating' => $doctor->getAllRatingQlf()));  ?>
                <span>квалификация</span>
            </div>
            <div class="item">
                <?php echo $this->renderPartial('//blocks/_rating', array('rating' => $doctor->getAllRatingAtt()));  ?>
                <span>внимание</span>
            </div>
            <div class="item">
                <?php echo $this->renderPartial('//blocks/_rating', array('rating' => $doctor->getAllRatingRoom()));  ?>
                <span>цена-качество</span>
            </div>
        </div>
    </div>
        <?php

        $countFillReview = 0;
        foreach($doctor->getReviews() as $review){
            if($review->getText()){
                $countFillReview++;
            }
        }

        ?>
        <?php if($countFillReview > 0){?>
    <div class="doctor-info-item list-of-reviews"><strong>Отзывы пациентов:</strong>
        <ul>
            <?php foreach($doctor->getReviews() as $review){?>
                <?php if($review->getText()){?>
            <li>
                <div class="review-text-wrapper">
                    <div class="review-text-block">
                        <p><?php echo $review->getText();?></p>
                    </div><i></i>
                </div>
                <div class="review-author-block"><strong><?php echo $review->getRatingInWord();?></strong>
                    <span><?php echo $review->getClient();?></span><em><?php echo $review->getDate()->format('d/m/Y');?></em>
                </div>
                <div class="items-wrapper">
                    <div class="item">
                        <?php echo $this->renderPartial('//blocks/_rating', array('rating' => $review->getRatingQlf()));  ?>
                        <span>квалификация</span>
                    </div>
                    <div class="item">
                        <?php echo $this->renderPartial('//blocks/_rating', array('rating' => $review->getRatingAtt()));  ?>
                        <span>внимание</span>
                    </div>
                    <div class="item">
                        <?php echo $this->renderPartial('//blocks/_rating', array('rating' => $review->getRatingRoom()));  ?>
                        <span>цена-качество</span>
                    </div>
                </div>
            </li>
            <?php } } ?>
        </ul>
    </div>
        <?php } ?>
    <?php } ?>
    <div class="send-review-block" style="display: none"><a href="#send-review" data-transition="slideup">Оставить отзыв</a><span>нам важно ваше мнение! </span>
    </div>
</div>
<div data-role="footer" class="fixed-footer" data-position="fixed" data-tap-toggle="false">
    <div class="fixed-inn fixed-inn-footer-detail">
        <div class="w-500">
			<?php if (!$doctor->isActive()) { ?>
				<span class="filter-number">подобрать врача по телефону</span>
			<?php } else { ?>
				<div class="to-form-doctor-btn">
					<a
						href="<?php echo Yii::app()->createUrl("doctor/request", ['doctor' => $doctor->getId()]); ?>"
						data-transition="slide">записаться <i></i></a>
				</div>
			<?php } ?>
            <div class="call-doctor-btn"><a href="tel:<?php echo $city->getPhoneSkypeFormat();?>"><i></i></a>
            </div>
        </div>
    </div>
</div>
<div class="mask-overlay"></div>
</div>

<div data-role="page" id="map-page" data-address="<?php echo $doctor->getFullAddress(); ?>" data-title="Информация о враче" class="ui-responsive-panel">
    <?php echo $this->renderPartial('//blocks/_right_panel', ["id" => "rightPanelMap"]);  ?>
    <div data-role="header" data-position="fixed" data-tap-toggle="false" class="fixed">
        <div class="fixed-inn">
            <a href="#page-doctor" data-transition="slide" data-direction="reverse" class="back-link"></a><strong>Информация о враче</strong>
            <a href="#rightPanelMap" class="panel-btn"></a>
        </div>
    </div>
    <div role="main" class="ui-content map-content">
        <div
			id="map_canvas"
			data-longitude="<?php echo $doctor->getFirstClinic() ? $doctor->getFirstClinic()->getLongitude() : ""; ?>"
			data-latitude="<?php echo $doctor->getFirstClinic() ? $doctor->getFirstClinic()->getLatitude() : ""; ?>"
			></div>
		<div id="infobox"></div>
        <a href="#page-doctor" data-transition="fade" class="close-map"></a>
    </div>
    <div data-role="footer" class="fixed-footer" data-position="fixed" data-tap-toggle="false">
        <div class="fixed-inn">
            <div class="w-500">
				<?php if (!$doctor->isActive()) { ?>
					<span class="filter-number">подобрать врача по телефону</span>
				<?php } else { ?>
                <div class="to-form-doctor-btn">
					<a href="<?php echo Yii::app()->createUrl(
						"doctor/request",
						['doctor' => $doctor->getId()]
					); ?>">записаться <i></i></a>
                </div>
				<?php } ?>
                <div class="call-doctor-btn"><a href="tel:<?php echo $city->getPhoneSkypeFormat();?>"><i></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="mask-overlay"></div>
</div>



<div data-role="page" id="send-review" data-title="Оставить отзыв">
    <div data-role="header" data-position="fixed" data-tap-toggle="false" class="fixed">
        <div class="fixed-inn">
            <a href="#page-doctor" data-transition="slidedown" class="back-link"></a><strong>Оставить отзыв</strong>
        </div>
    </div>
    <div role="main" class="ui-content send-review-wrapper">
        <form action="#">
            <div class="send-review-top-block">
                <div class="send-review-title"><strong>Ваш отзыв:</strong>
                    <p>Нам важно ваше мнение, пишите все о чем наболело.</p>
                </div>
                <div class="send-review-form">
                    <div class="form-item item-1">
                        <input type="text" placeholder="Ваше имя..." class="required"><i></i>
                    </div>
                    <div class="form-item item-2">
                        <input type="tel" placeholder="Ваш номер..."><i></i>
                    </div>
                </div>
            </div>
            <div class="review-rating-block">
                <div class="review-rating-item">
                    <div class="range-wrap">
                        <input type="range" min="0" max="5" value="4" data-highlight="true">
                    </div><span>квалификация</span>
                </div>
                <div class="review-rating-item">
                    <div class="range-wrap">
                        <input type="range" min="0" max="5" value="3" data-highlight="true">
                    </div><span>внимание</span>
                </div>
                <div class="review-rating-item">
                    <div class="range-wrap">
                        <input type="range" min="0" max="5" value="2" data-highlight="true">
                    </div><span>цена- качество</span>
                </div>
            </div>
            <div class="send-review-btn-block"><a href="#review-success" data-transition="fade"><i></i> высказаться</a>
            </div>
        </form>
    </div>
</div>
<div data-role="page" id="review-success" data-title="Вы оставили отзыв!">
    <div id="rightPanelReview" data-role="panel" data-position="right" data-display="reveal" class="rightPanel">
        <div class="aside-header"><span class="logo-small"></span>
			<?php $this->renderPartial("/blocks/_city_change"); ?>
        </div>
        <div class="aside-description">
            <p>На нашем портале вы можете выбрать врача и записаться к нему на прием.</p>
            <p>Мы поможем вам найти хорошего специалиста!</p>
        </div>
        <div class="aside-block mb-15"><strong>О проекте</strong>
            <ul>
                <li><a href="#">О сервисе</a><i></i>
                </li>
                <li><a href="#">Все врачи</a><i></i>
                </li>
                <li><a href="#">Все клиники</a><i></i>
                </li>
            </ul>
        </div>
        <div class="aside-block"><strong>Врачам и клиникам</strong>
            <ul>
                <li><a href="#">Личный кабинет</a><i></i>
                </li>
                <li><a href="#">Регистрация</a><i></i>
                </li>
            </ul>
        </div>
        <div class="aside-foo"><?php echo $this->renderPartial('//blocks/_main_site');  ?>
        </div>
    </div>
    <div data-role="header" data-position="fixed" data-tap-toggle="false" class="fixed">
        <div class="fixed-inn">
            <a href="#page-doctor" data-transition="fade" class="close-btn"></a><strong>Вы оставили отзыв!</strong>
            <a href="#rightPanelReview" class="panel-btn"></a>
        </div>
    </div>
    <div role="main" class="ui-content success-block">
        <div class="order-success-content"><strong class="success-title">Вы оставили отзыв!</strong>
            <div class="success-text">
                <p class="gray">Спасибо за ваш отзыв. Нам важно ваше мнение.</p>
                <p class="green">P.S: команда DocDoc благодарит вас за использование услуг нашего портала!</p>
            </div><i class="success-check"></i>
        </div>
        <div class="order-success-bottom-block">
            <div class="link-to-favorites"><a href="#">Добавить  сайт в закладки</a><i></i>
            </div><?php echo $this->renderPartial('//blocks/_main_site');  ?>
        </div>
    </div>
    <div class="mask-overlay"></div>
</div>
<div data-role="page" id="review-error" data-title="Ошибка!">
    <div data-role="header" data-position="fixed" data-tap-toggle="false" class="fixed">
        <div class="fixed-inn">
            <a href="#send-review" data-transition="flip" class="back-link"></a><strong>Оставить отзыв</strong>
        </div>
    </div>
    <div role="main" class="ui-content success-block">
        <div class="order-success-content"><strong class="success-title">Ошибка!</strong>
            <div class="success-text">
                <p class="gray">Внимание! Ваш отзыв не был отправлен. Попробуйте снова.Перед отправкой проверьте, все ли поля с информацией заполнены правильно.</p>
                <p class="green">P.S: команда DocDoc благодарит вас за использование услуг нашего портала!</p>
            </div><i class="success-check"></i>
        </div>
        <div class="order-success-bottom-block">
            <div class="link-to-favorites"><a href="#send-review" data-transition="flip">Попробовать снова</a>
            </div><?php echo $this->renderPartial('//blocks/_main_site');  ?>
        </div>
    </div>
</div>