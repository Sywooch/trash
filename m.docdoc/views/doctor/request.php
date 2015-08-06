<?php
/**
 * @var SiteController $this
 * @var CityModel $city
 * @var DoctorModel $doctor
 * @var ClinicModel $clinic
 * @var string $message
 * @var integer $code
 */
$this->pageTitle = "Записаться на прием";
?>

<div data-role="page" id="doctor-form" data-title="Записаться на прием">
    <div data-role="header" data-position="fixed" data-tap-toggle="false" class="fixed">
        <div class="fixed-inn">
            <a href="<?php echo Yii::app()->createUrl("doctor/detail", ['alias' => $doctor->getAlias()]); ?>" data-transition="slide" data-direction="reverse" class="back-link"></a><strong>Записаться на прием</strong>
        </div>
        <div class="fixed-inn w-500 doctor-info-block">
            <div class="left-block">
                <div class="avatar-wrap">
                    <img src="<?php echo $doctor->getImg();?>" alt="<?php echo $doctor->getName();?>">
                </div>
            </div>
            <div class="right-block">
                <h3 class="name"><?php echo $doctor->getName();?></h3><strong class="prof"><?php echo $doctor->getAllSpecialityString();?> </strong>
            </div>
        </div>
    </div>
    <div role="main" class="ui-content doctor-order-form">
        <form action="#"
			  data-request-type="doctor"
			  data-form-type="FullForm"
			  data-clinic-id="<?php echo $clinic ? $clinic->getId() : null; ?>"
			  data-clinic-name="<?php echo $clinic ? $clinic->getName() : null; ?>"
			  data-clinic-metro="<?php echo $doctor->getAllStationsString(); ?>"
			  data-doctor-id="<?php echo $doctor->getId(); ?>"
			  data-doctor-name="<?php echo $doctor->getName(); ?>"
			  data-doctor-reviews="<?php echo count($doctor->getReviews()); ?>"
			  data-doctor-rating="<?php echo $doctor->getRating(); ?>"
			  data-doctor-experience="<?php echo $doctor->getExperienceYear(); ?>"
			  data-doctor-awards="<?php echo $doctor->getDegree(); ?>"
			  data-doctor-price="<?php echo $doctor->getPrice(); ?>"
			  data-doctor-special-price="<?php echo $doctor->getSpecialPrice(); ?>"
			  data-doctor-image="<?php echo $doctor->getImg(); ?>"
			  data-doctor-spec="<?php echo $doctor->getAllSpecialityString(); ?>"
			  data-city-name="<?php echo $city->getName(); ?>">
            <div class="form-item item-1">
                <input type="text" name="name" placeholder="Ваше имя..." class="required" id="requestName"><i></i>
            </div>
            <div class="form-item item-2">
                <input type="tel" name="phone" placeholder="Ваш телефон..." onkeypress="return (event.charCode &gt;= 40 &amp;&amp; event.charCode &lt;= 57) || event.charCode == 32" maxlength="20" id="phoneinput"><i></i>
            </div>
            <div class="send-button-wrap"><a id="submit-doctor-form" href="#order-success" data-request-url="<?php echo Yii::app()->createUrl('doctor/requestSend');?>" data-transition="fade"><i></i> отправить</a>
                <a id="submit-redirect-link" href="" style="display: none"></a>
            </div>
        </form>
        <div class="call-us"><strong>Записаться на прием </strong><span>по номеру <a href='tel:<?php echo $city->getPhoneSkypeFormat();?>'><?php echo $city->getPhone();?></a></span><i class="iphone-bg"></i>
        </div><?php echo $this->renderPartial('//blocks/_main_site');  ?>
    </div>
</div>
<div data-role="page" id="order-success" data-title="Ваша заявка о записи на прием к врачу отправлена.
	Наши консультанты свяжутся с вами в течение 15 минут ежедневно с 9:00 до 21:00 и запишут Вас на прием.">
    <?php echo $this->renderPartial('//blocks/_right_panel');  ?>
    <div data-role="header" data-position="fixed" data-tap-toggle="false" class="fixed">
        <div class="fixed-inn">
			<a href="<?php echo Yii::app()->createUrl("doctor/detail", ['alias' => $doctor->getAlias()]); ?>"
			   data-transition="fade" class="close-btn"></a><strong>Ваша заявка принята</strong>
            <a href="#rightPanel" class="panel-btn"></a>
        </div>
    </div>
    <div role="main" class="ui-content success-block">
        <div class="order-success-content">
			<strong class="success-title">
				Ваша заявка о записи на прием к врачу отправлена.
				Наши консультанты свяжутся с вами в течение 15 минут ежедневно с 9:00 до 21:00 и запишут Вас на прием.
			</strong>
            <div class="success-text">
                <p class="gray">Спасибо за вашу заявку. Наш менеджер свяжется с вами в ближайшее время.</p>
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
<div data-role="page" id="order-error" data-title="Ошибка!">
    <div data-role="header" data-position="fixed" data-tap-toggle="false" class="fixed">
        <div class="fixed-inn">
            <a href="#doctor-form" data-transition="flip" class="back-link"></a><strong>Записаться на прием</strong>
        </div>
    </div>
    <div role="main" class="ui-content success-block">
        <div class="order-success-content"><strong class="success-title order-title">Ошибка!</strong>
            <div class="success-text">
                <p class="gray">Внимание! Ваша анкета записи не была отправлена. Попробуйте снова.Перед отправкой проверьте, все ли поля с информацией заполнены правильно.</p>
                <p class="green">P.S: команда DocDoc благодарит вас за использование услуг нашего портала!</p>
            </div><i class="success-check"></i>
        </div>
        <div class="order-success-bottom-block">
            <div class="link-to-favorites"><a href="#doctor-form" data-transition="flip">Попробовать снова</a>
            </div><?php echo $this->renderPartial('//blocks/_main_site');  ?>
        </div>
    </div>
</div>