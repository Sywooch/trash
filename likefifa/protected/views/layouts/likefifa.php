<?php
use likefifa\models\RegionModel;

/**
 * @var FrontendController $this
 * @var string $content
 */
$isMain = ($this instanceof SiteController && $this->action->id === 'index');
$isGallery = ($this instanceof SearchController && $this->action->id === 'gallery');
$isLk =
	($this instanceof LkController || $this instanceof SalonlkController);
$isMap = ($this instanceof SearchController && $this->action->id === 'map');
$isLanding = ($this instanceof LandingController);
$isOther = !$isMain && !$isGallery && !$isLk && !$isMap && !$isLanding;

$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->clientScript;
$cs->registerCoreScript('jquery')
	->registerScriptFile($baseUrl . '/js/map.js')->registerScriptFile(
		$baseUrl . '/js/jquery.jsonSuggest.js',
		CClientScript::POS_END
	)
	->registerCssFile($baseUrl . '/css/global.css')
	->registerCssFile($baseUrl . '/css/common.css')
	->registerCssFile($baseUrl . '/css/template.css')
	->registerCssFile($baseUrl . '/css/prettyPhoto.css')
	->registerCssFile($baseUrl . '/css/jquery.fancybox.css')
	->registerCssFile($baseUrl . '/css/tip-yellow.css')
	->registerLinkTag('icon', 'image/x-icon', Yii::app()->homeUrl . 'favicon.ico')
	->registerLinkTag('shortcut icon', 'image/x-icon', Yii::app()->homeUrl . 'favicon.ico')
	->registerMetaTag(CHtml::encode($this->metaKeywords), 'keywords')
	->registerMetaTag(CHtml::encode($this->metaDescription), 'description')
	->registerMetaTag('4d69fa5c44d0d762', 'yandex-verification');

if($isLk) {
	$cs->registerCssFile($baseUrl . '/css/profile.css');
}
if($isLanding) {
	$cs->registerCssFile($baseUrl . '/css/landing.css');
}
if($isMap) {
	$cs->registerCssFile($baseUrl . '/css/map.css')
		->registerScriptFile(
			Yii::app()->assetManager->publish(
				Yii::getPathOfAlias('application.vendors.pklauzinski.jscroll') . '/jquery.jscroll.min.js',
				false,
				-1,
				YII_DEBUG
			),
			CClientScript::POS_HEAD
		);
}

if($this->lkRefresh) {
	$cs->registerMetaTag(300, null, 'refresh');
}
if (Yii::app()->mobileDetect->isMobile()) {
	$classVersionDevice = 'mobile-version';
} else {
	$classVersionDevice = 'desctop-version';
}
?>
<!DOCTYPE html>
<!--[if lt IE 7]><html class="lt-ie9 lt-ie8 lt-ie7 ie-6" xmlns:og="http://ogp.me/ns#" id="nojs"><![endif]-->
<!--[if IE 7]><html class="lt-ie9 lt-ie8 ie-7" xmlns:og="http://ogp.me/ns#" id="nojs"> <![endif]-->
<!--[if IE 8]><html class="lt-ie9 ie-8" xmlns:og="http://ogp.me/ns#" id="nojs"><![endif]-->
<!--[if gt IE 8]><!--><html xmlns:og="http://ogp.me/ns#" id="nojs"><!--<![endif]-->
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

	<title><?php echo CHtml::encode($this->pageTitle ? $this->pageTitle.(strpos($this->pageTitle, 'LikeFifa') === false ? ' - LikeFifa' : '') : 'LikeFifa'); ?></title>
	<script>document.documentElement.id = "js"</script>

	<script type="text/javascript" src="<?php echo Yii::app()->homeUrl; ?>js/ga.js?<?php echo RELEASE_MEDIA; ?>"></script>

	<script type="text/javascript">
		(function(){function i(a){return"undefined"!=typeof a}function e(){l||(l=!0,m())}function n(a,b){if("function"==typeof b)try{b()}catch(f){}setTimeout(function(){c.location.href=a},r)}function g(a,b){setTimeout(function(){m(a,b)},0)}function m(j,o){var f=c.createElement(k);f.type="text/java"+k;var d;d="?rnd="+(100*((new Date).getTime()%1E7)+p.round(99*p.random()));d+="&lnk="+encodeURIComponent(c.location.href);d+=c.referrer?"&r="+encodeURIComponent(c.referrer):"";d+="&t="+(new Date).getTime();if(i(a[b][h]))for(var e in a[b][h])a[b][h].hasOwnProperty(e)&&
		(d+="&"+encodeURIComponent(e)+"="+encodeURIComponent(a[b][h][e]));if(i(j))for(var g in j)j.hasOwnProperty(g)&&(d+="&"+encodeURIComponent(g)+"="+encodeURIComponent(j[g]));f.src=s+d;if("function"==typeof o)f.onload=o;else if("function"==typeof a[b].onload)f.onload=a[b].onload;"undefined"!=typeof f&&c.getElementsByTagName(k)[0].parentNode.appendChild(f)}function t(){c.addEventListener?c.addEventListener("DOMContentLoaded",e,!1):c.attachEvent?(q.doScroll&&a==a.top&&function(){try{q.doScroll("left")}catch(a){setTimeout(arguments.callee,
		0);return}e()}(),c.attachEvent("onreadystatechange",function(){"complete"===c.readyState&&e()})):a.onload=e}var s="//c.target.adlabs.ru/tr/161/",c=document,q=c.documentElement,p=Math,a=window,k="script",h="params",l=!1,b="__TRGT_TRCKR__",r=100;i(a[b])||(a[b]={});i(a[b][h])||(a[b][h]={});a[b].track=function(a,b){g(a,b);return!0};a[b].trackOutbound=function(a,b,c){g(b);n(a,c);return!1};a[b].trackOutboundSync=function(a,b,c){g(b,function(){n(a,c)});return!1};(!i(a[b].manual)||!a[b].manual)&&t()})();
	</script>

	<script type="text/javascript" src="//vk.com/js/api/openapi.js?75"></script>
	<script type="text/javascript">
		VK.init({apiId: <?php echo Yii::app()->params["vk"]["apiId"]; ?>, onlyWidgets: true});
		$(function() {
			if($("#vk_like").length > 0) {
				VK.Widgets.Like("vk_like", {type: "button", height: 20});
			}
		});

		_ga.trackVkontakte();
	</script>
</head>
<body class="<?php echo $classVersionDevice;?>">
	<!-- Universal Analytics -->
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', '<?php echo Yii::app()->params["gaAccount"]; ?>', 'auto');
		ga('send', 'pageview');

	</script>
	<!-- /Universal Analytics -->

	<div id="fb-root"></div>
	<script>
		// Facebook async loading.
		(function() {
			var e = document.createElement('script'); e.async = false;
			e.src = document.location.protocol +
				'//connect.facebook.net/ru_RU/all.js';
			document.getElementById('fb-root').appendChild(e);
		}());
		window.fbAsyncInit = function() {
			FB.init({status: true, cookie: true, xfbml: true});
			_ga.trackFacebook();
		};
	</script>

	<div id="wrap">
		<div id="header" class="<?php if($isMain) {?>header-main<?php } ?><?php if($isGallery) {?>header-gallery<?php } ?><?php if($isOther || $isLk) {?>header<?php } ?><?php if($isLanding) {?>header-landing<?php } ?> png">
			<div class="city png<?php if (Yii::app()->activeRegion->isMO()) { ?> header-mo<?php } ?>"></div>
			<a href="<?php echo Yii::app()->homeUrl; ?>" id="logo" class="png"></a>

			<?php if(!$isMain): ?>
				<a href="#" class="change-region" title="сменить регион">
					<?php echo Yii::app()->activeRegion->getModel()->name; ?> <span class="change-region-arr"></span>
				</a>
				<div class="change-region-container">
					<?php foreach (RegionModel::model()->active()->orderByName()->findAll() as $region) { ?>
						<?php if ($region->id == Yii::app()->activeRegion->getModel()->id) { ?>
							<span><?php echo $region->name; ?></span>
						<?php } else { ?>
							<a href="<?php echo $region->getIndexUrl(); ?>"><?php echo $region->name; ?></a>
						<?php } ?>
					<?php } ?>
				</div>
				<div class="change-region-container-overlay"></div>
			<?php endif; ?>

			<?php if ($isOther || $isLk): ?>
				<div class="content-wrap">
					<div class="header-request_wrap">
						<div class="header-request_head">Бесплатный подбор мастера</div>
						Звоните <strong>+7 (495) 215-06-75</strong> <br>
						или <span class="header-appointment_link btn-appointment" data-gatype="click-click_on_header">оставьте заявку</span>
					</div>
					<div class="header-informer_count__wrap">
						<div class="header-informer_count__item">
							<div class="header-informer_count__num-wrap in-bl">
								<?php foreach(str_split($this->getClientCount()) as $c): ?>
									<span class="in-bl"><?php echo $c; ?></span>
								<?php endforeach; ?>
							</div>
							<div class="in-bl">Клиентов за неделю</div>
						</div>
						<div class="header-informer_count__item">
							<div class="header-informer_count__num-wrap in-bl">
								<?php foreach(str_split($this->mastersCount) as $c): ?>
									<span class="in-bl"><?php echo $c ?></span>
								<?php endforeach; ?>
							</div>
							<div class="in-bl">Мастеров на сайте</div>
						</div>
					</div>
					<div class="header-txt-border-wrap"><div class="header-txt-border-gradient"><div class="header-txt-border">Все мастера Москвы – выбери своего!</div></div></div>
				</div>
				<script type="text/javascript">
					var appointmentSuggest;
					$(function() {
						appointmentSuggest = new SearchSuggest()
					});
				</script>
			<?php else:?>
				<?php if (!$isGallery && !$isMap): ?>
					<div class="content-wrap">
						<div class="header-count"><span class="header-count_num"><?php echo $this->mastersCount; ?></span> <?php echo su::caseForNumber($this->mastersCount, array('мастер', 'мастера', 'мастеров')); ?>&nbsp;&nbsp;&nbsp;&nbsp;<span class="header-count_num"><?php echo $this->salonsCount; ?></span> <?php echo su::caseForNumber($this->salonsCount, array('салон', 'салона', 'салонов')); ?>&nbsp;&nbsp;&nbsp;&nbsp;<span class="header-count_num"><?php echo $this->worksCount; ?></span> <?php echo su::caseForNumber($this->worksCount, array('фотография работ', 'фотографии работ', 'фотографий работ')); ?></div>
					</div>
				<?php endif?>
			<?php endif?>

			<?php if ($isGallery): ?>
				<div class="content-wrap">
					<div class="header-gallery_txt"><?php echo $this->getHeadText(); ?> – выбери своего!</div>
					<div class="header-count" style="display:none;">
						<span class="header-count_num"><?php echo $this->mastersCount; ?></span> <?php echo su::caseForNumber(
							$this->mastersCount,
							array('мастер', 'мастера', 'мастеров')
						); ?>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<span class="header-count_num"><?php echo $this->salonsCount; ?></span> <?php echo su::caseForNumber(
							$this->mastersCount,
							array('салон', 'салона', 'салонов')
						); ?>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<span class="header-count_num"><?php echo $this->worksCount; ?></span> <?php echo su::caseForNumber(
							$this->mastersCount,
							array('работа', 'работы', 'работ')
						); ?>
					</div>

					<noindex>
						<form action="<?php echo $this->forDefault()->createRedirectUrl('gallery'); ?>" method="GET">
						<div class="header-gallery_fltr">
							<div class="header-gallery_fltr_h">Вы ищете:</div>
							<div class="header-gallery_fltr_inp spec-selector">
								<div class="header-gallery_fltr_subh">вид услуг:</div>
								<div class="form-inp">
									<input type="hidden" id="inp-select-popup-service-type" name="specialization" value="<?php echo $this->specialization ? $this->specialization->id : null; ?>" />
									<div class="form-select-over" data-select-popup-id="select-popup-service-type"></div>
									<div class="form-select" id="cur-select-popup-service-type"><?php echo $this->specialization ? $this->specialization->name : 'Выберите из списка'; ?></div><div class="form-select-arr png"></div>
									<div class="form-select-popup" id="select-popup-service-type">
										<div class="form-select-popup-long">
											<span class="item<?php echo $this->specialization ? '' : ' act'; ?>" data-value="">Выберите из списка</span>
											<?php foreach (LfSpecialization::model()->ordered()->findAll() as $spec): ?>
												<span class="item<?php echo $this->specialization && ($spec->id == $this->specialization->id) ? ' act' : ''; ?>" data-value="<?php echo $spec->id; ?>"><?php echo $spec->name; ?></span>
											<?php endforeach; ?>
										</div>
									</div>
								</div>
							</div>
							<div class="header-gallery_fltr_inp service-selector">
								<div class="header-gallery_fltr_subh">подвид услуг:</div>
								<div class="form-inp">
									<input type="hidden" id="inp-select-popup-service-subtype" name="service" value="<?php echo $this->service ? $this->service->id : null; ?>" />
									<div class="form-select-over" data-select-popup-id="select-popup-service-subtype"></div>
									<div class="form-select form-select_pink" id="cur-select-popup-service-subtype"><?php echo $this->service ? $this->service->name : 'Выберите из списка'; ?></div><div class="form-select-arr png"></div>
									<div class="form-select-popup" id="select-popup-service-subtype">
										<div class="form-select-popup-long">
											<span class="item form-select_pink<?php echo $this->service ? '' : ' act'; ?>" data-value="">Выберите из списка</span>
											<?php foreach (LfService::model()->filtered()->findAll() as $serv): ?>
												<span class="item<?php echo $this->service && ($serv->id == $this->service->id) ? ' act' : ''; ?>" data-spec-id="<?php echo $serv->specialization_id; ?>" data-value="<?php echo $serv->id; ?>"><?php echo $serv->name; ?></span>
											<?php endforeach; ?>
										</div>
									</div>
								</div>
							</div>
							<div class="header-gallery_fltr_inp" style="width:auto; margin:17px 0 0 0;">
								<div class="button button-pink" id="gallery-search-sbmt"><img src="<?php echo Yii::app()->homeUrl; ?>i/icon-search.png" class="png" style="margin-top:3px;" /></div>
							</div>
						</div>
						</form>
					</noindex>
					<div class="header-gallery-border-bottom">
						<div class="header-txt-border-wrap"><div class="header-txt-border-gradient"><div class="header-txt-border">Фотогалерея работ</div></div></div>
					</div>
				</div>
			<?php endif?>

			<?php if ($isMap): ?>
				<div class="header-count">
					<div class="header-txt"><?php echo $this->getHeadText(); ?><br/> – выбери своего!</div>
				</div>
				<form action="<?php echo $this->createRedirectUrl('map'); ?>" method="GET">
					<div class="header-fltr">
						<div class="header-fltr_h">Вы ищете:</div>
						<div class="header-fltr_inp spec-selector">
							<div class="header-fltr_subh">вид услуг:</div>
							<div class="form-inp">
								<input type="hidden" id="inp-select-popup-service-type" name="specialization" value="<?php echo $this->specialization ? $this->specialization->id : null; ?>" />
								<div class="form-select-over" data-select-popup-id="select-popup-service-type"></div>
								<div class="form-select" id="cur-select-popup-service-type"><?php echo $this->specialization ? $this->specialization->name : 'Выберите из списка'; ?></div><div class="form-select-arr png"></div>
								<div class="form-select-popup" id="select-popup-service-type">
									<div class="form-select-popup-long">
										<span class="item<?php echo $this->specialization ? '' : ' act'; ?>" data-value="">Выберите из списка</span>
										<?php foreach (LfSpecialization::model()->ordered()->findAll() as $spec): ?>
											<span class="item<?php echo $this->specialization && ($spec->id == $this->specialization->id) ? ' act' : ''; ?>" data-value="<?php echo $spec->id; ?>"><?php echo $spec->name; ?></span>
										<?php endforeach; ?>
									</div>
								</div>
							</div>
						</div>
						<div class="header-fltr_inp service-selector">
							<div class="header-fltr_subh">подвид услуг:</div>
							<div class="form-inp">
								<input type="hidden" id="inp-select-popup-service-subtype" name="service" value="<?php echo $this->service ? $this->service->id : null; ?>" />
								<div class="form-select-over" data-select-popup-id="select-popup-service-subtype"></div>
								<div class="form-select form-select_pink" id="cur-select-popup-service-subtype"><?php echo $this->service ? $this->service->name : 'Выберите из списка'; ?></div><div class="form-select-arr png"></div>
								<div class="form-select-popup" id="select-popup-service-subtype">
									<div class="form-select-popup-long">
										<span class="item form-select_pink<?php echo $this->service ? '' : ' act'; ?>" data-value="">Выберите из списка</span>
										<?php foreach (LfService::model()->filtered()->findAll() as $serv): ?>
											<span class="item<?php echo $this->service && ($serv->id == $this->service->id) ? ' act' : ''; ?>" data-spec-id="<?php echo $serv->specialization_id; ?>" data-value="<?php echo $serv->id; ?>"><?php echo $serv->name; ?></span>
										<?php endforeach; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="header-fltr_check content-wrap">
						<span class="form-inp_check" data-check-id="hasDeparture"><i id="i-check_hasDeparture" class="png"></i><input type="checkbox" id="inp-check_hasDeparture" name="hasDeparture" <?php echo $this->hasDeparture ? 'checked="checked"' : ''; ?> />Возможен выезд</span>
					</div>
					<!--<div class="select-metro-btn">+ Метро или Район</div>-->
					<div class="button button-pink map-btn-search"><span>Найти</span><img src="<?php echo Yii::app()->homeUrl; ?>i/icon-search-filter.png" class="png" /></div>
				</form>
			<?php endif; ?>
		</div>

		<?php echo $content; ?>

	</div>
	<?php if (!$isMap): ?>
	<div id="footer-wrap">
		<div id="footer">
			<table width="100%">
				<colgroup>
					<col width="295" />
					<col width="295" />
					<col width="295" />
				</colgroup>
				<tr>
					<td>
						<div class="footer-head">О сайте</div>
						<div class="footer-pad">
							<ul>
								<li><a href="<?php echo $this->createUrl('site/page', array('page' => 'about')); ?>">О нас</a></li>
								<li><a href="<?php echo $this->createUrl('sitemap/index'); ?>">Карта сайта</a></li>
								<li><a href="<?php echo $this->createUrl('site/page', array('page' => 'faq')); ?>">Часто задаваемые вопросы</a></li>
								<li><a href="<?php echo $this->createUrl('site/page', array('page' => 'rules')); ?>">Правила сотрудничества</a></li>
								<li>
									<a href="<?php echo $this->createUrl('site/page', array('page' => 'mobile')); ?>">
										Мобильное приложение
									</a>
								</li>
								<li>Мы в социальных сетях:</li>
							</ul>
							<div>
								<a href="http://vk.com/likefifa" class="soc" target="_blank"></a>
								<a href="http://www.facebook.com/likefifa.ru" class="soc soc-fb" target="_blank"></a>
								<a href="https://twitter.com/LikeFifa_ru" class="soc soc-twitter" target="_blank"></a>
							</div>
							<div class="footer-head" style="margin-bottom:8px;"></div>
						</div>
						<div style="whites-space:nowrap; margin-right:-100px; padding-left:12px; line-height: 25px;">
							<span class="footer-tel">8-495-215-06-75</span> - помощь мастерам красоты<br/>
							<a href="mailto:fifa@likefifa.ru" style="font-style:normal;">fifa@likefifa.ru</a>
						</div>
								</td>
					<td>
						<div class="footer-head">Статьи</div>
						<div class="footer-pad">
							<ul class="bullet">
								<?php foreach ($this->articles as $article): ?>
									<li><a href="<?php echo $article->getDetailUrl(); ?>"><?php echo $article->name; ?></a></li>
								<?php endforeach; ?>
							</ul>
							<br/>
							<a href="<?php echo $this->createUrl('article/index'); ?>" style="margin-left:10px;"> все статьи</a>
						</div>
					</td>
					<td style="padding-left:0;">
						<?php if ($this->loggedMaster || $this->loggedSalon): ?>
							<div class="footer-head">Вы вошли как:</div>
						<?php else: ?>
							<div class="footer-head" id='footer-registration' style="color: #d1288a; text-shadow: none; font-weight:bold; letter-spacing:1px;">Регистрация</div>
						<?php endif; ?>
						<div class="footer-pad">
							<?php if (!$this->loggedMaster && !$this->loggedSalon): ?>
								<a href="<?php echo $this->createUrl('landing/index'); ?>" style="color: #d1288a;">Регистрация частных мастеров<br/> и салонов</a>
								<div class="footer-head" style="margin-bottom:8px; padding-top:5px;"></div>
							<?php endif; ?>
							<?php if ($this->loggedMaster): ?>
								<div style="font-size:17px; font-style:italic;"><?php echo $this->loggedMaster->getFullName(); ?></div>
								<div class="footer-head" style="margin-bottom:8px; padding-top:2px;"></div>
								<a href="<?php echo $this->loggedMaster->getLkUrl(); ?>">Личный кабинет</a>
								<br/>
								<br/>
								<br/>
								<form action="<?php echo $this->createUrl('site/logout'); ?>" method="POST">
									<input type="submit" value="ВЫЙТИ" id="form-footer_submit" />
									<div class="footer-btn-submit png" id="form-footer_btn">ВЫЙТИ</div>
								</form>
							<?php elseif ($this->loggedSalon): ?>
								<div style="font-size:17px; font-style:italic;"><?php echo $this->loggedSalon->name; ?></div>
								<div class="footer-head" style="margin-bottom:8px; padding-top:2px;"></div>
								<a href="<?php echo $this->loggedSalon->getLkUrl(); ?>">Личный кабинет</a>
								<br/>
								<br/>
								<br/>
								<form action="<?php echo $this->createUrl('site/logout'); ?>" method="POST">
									<input type="submit" value="ВЫЙТИ" id="form-footer_submit" />
									<div class="footer-btn-submit png" id="form-footer_btn">ВЫЙТИ</div>
								</form>
							<?php else: ?>
								<div class="footer-head">Вход в личный кабинет</div>
								<?php $form = $this->beginWidget(
									'CActiveForm',
									array(
										'action'               => $this->createUrl(
											'landing/index',
											array('#' => 'footer-registration')
										),
										'enableAjaxValidation' => false,
										'htmlOptions'          => array(),
									)
								); ?>
									<input type="hidden" name="action" value="login" />
									<div class="footer-form-item">
										<?php echo LfHtml::activeTextField($this->masterLoginForm, 'email', array('placeholder' => 'Введите рабочий e-mail')); ?><span class="label">Ваш e-mail</span>
									</div>
									<div class="footer-form-item">
										<?php echo LfHtml::activePasswordField($this->masterLoginForm, 'password', array('placeholder' => '******')); ?><span class="label">Пароль</span>
									</div>
									<div><a href="<?php echo $this->createUrl('remind/index'); ?>" class="footer-link_small">забыли пароль?</a></div>
									<div class="reg-auth_panel_main in-bl">
										<a class="auth-service odnoklassniki" href="/landing/?service=odnoklassniki" title="Войти через Одноклассники"></a>
										<a class="auth-service vkontakte" href="/landing/?service=vkontakte" title="Войти через Вконтакте"></a>
										<a class="auth-service facebook" href="/landing/?service=facebook" title="Войти через Facebook"></a>
										<span class='text'>Войдите через:</span>
									</div>
									<input type="submit" value="ВОЙТИ" id="form-footer_submit" />
									<div class="footer-btn-submit png" id="form-footer_btn">ВОЙТИ</div>
								<?php $this->endWidget(); ?>
							<?php endif; ?>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<?php endif; ?>

	<div id="overlay"></div>
	<div id="popup"></div>

	<div class="btn-top-page"></div>

	<script type="text/javascript">
		window.homeUrl = <?php echo json_encode(Yii::app()->homeUrl); ?>;
	</script>

	<script type="text/javascript" src="<?php echo Yii::app()->homeUrl; ?>js/jquery-ui-1.10.2.custom.min.js?<?php echo RELEASE_MEDIA; ?>"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->homeUrl; ?>js/jquery.ui.datepicker-ru.js?<?php echo RELEASE_MEDIA; ?>"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->homeUrl; ?>js/jquery.maskedinput-1.3.min.js?<?php echo RELEASE_MEDIA; ?>"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->homeUrl; ?>js/jquery.easing.min.1.3.js?<?php echo RELEASE_MEDIA; ?>"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->homeUrl; ?>js/jquery.prettyPhoto.js?<?php echo RELEASE_MEDIA; ?>"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->homeUrl; ?>js/jquery.mousewheel.js?<?php echo RELEASE_MEDIA; ?>"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->homeUrl; ?>js/jquery.jscrollpane.min.js?<?php echo RELEASE_MEDIA; ?>"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->homeUrl; ?>js/authSocial.js?<?php echo RELEASE_MEDIA; ?>"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->homeUrl; ?>js/jquery.mousewheel-3.0.6.pack.js?<?php echo RELEASE_MEDIA; ?>"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->homeUrl; ?>js/jquery.fancybox.js?<?php echo RELEASE_MEDIA; ?>"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->homeUrl; ?>js/jquery.poshytip.min.js?<?php echo RELEASE_MEDIA; ?>"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->homeUrl; ?>js/social-likes.min.js?<?php echo RELEASE_MEDIA; ?>"></script>
	<script type="text/javascript">
		$(function() {
			window.searchEntity = '<?php echo $this->searchEntity; ?>';
			window.serviceTree = <?php echo json_encode(LfSpecialization::model()->getIdsTree()); ?>;
		});
	</script>
	<!--[if IE 6]>
		<script type="text/javascript" src="<?php echo Yii::app()->homeUrl; ?>js/DD_belatedPNG_0.0.8a-min.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				DD_belatedPNG.fix('.png');
			});
		</script>
	<![endif]-->
	<?php if ($isGallery): ?>
	<script type="text/javascript" src="<?php echo Yii::app()->homeUrl; ?>js/imagesloaded.pkgd.min.js?<?php echo RELEASE_MEDIA; ?>"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->homeUrl; ?>js/isotope.pkgd.min.js?<?php echo RELEASE_MEDIA; ?>"></script>
	<?php endif; ?>
	<script type="text/javascript" src="<?php echo Yii::app()->homeUrl; ?>js/common.js?<?php echo RELEASE_MEDIA; ?>"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->homeUrl; ?>js/global.js?<?php echo RELEASE_MEDIA; ?>"></script>

	<?php if($isLanding) {?>
		<script type="text/javascript" src="<?php echo Yii::app()->homeUrl; ?>js/landing.js?<?php echo RELEASE_MEDIA; ?>"></script>
	<?php } ?>

	<?php if ($this->firstTime): ?>
		<script type="text/javascript">
			__TRGT_TRCKR__ = {};
			__TRGT_TRCKR__.params = {target_id: 115};
		</script>
	<?php endif; ?>

	<?php if($this->showPopup):?>
		<script>
			$(function() {
				$("#overlay").show();
				var url = '/popup/service-auth/';

				$.get(url, function(data) {
					$("#popup").html(data);
					showPopup();
				});
			});
		</script>
	<?php endif;?>

	<?php if ($this->firstTime): ?>
		<script type="text/javascript">
			__TRGT_TRCKR__ = {};
			__TRGT_TRCKR__.params = {target_id: 115};
		</script>
	<?php endif; ?>

	<!-- Yandex.Metrika counter -->
	<script type="text/javascript">
	(function (d, w, c) {
	    (w[c] = w[c] || []).push(function() {
	        try {
	            w.yaCounter18888463 = new Ya.Metrika({id:18888463,
	                    webvisor:true,
	                    clickmap:true,
	                    trackLinks:true,
	                    accurateTrackBounce:true});
	        } catch(e) { }
	    });

	    var n = d.getElementsByTagName("script")[0],
	        s = d.createElement("script"),
	        f = function () { n.parentNode.insertBefore(s, n); };
	    s.type = "text/javascript";
	    s.async = true;
	    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

	    if (w.opera == "[object Opera]") {
	        d.addEventListener("DOMContentLoaded", f, false);
	    } else { f(); }
	})(document, window, "yandex_metrika_callbacks");
	</script>
	<noscript><div><img src="//mc.yandex.ru/watch/18888463" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
	<!-- /Yandex.Metrika counter -->

</body>
</html>