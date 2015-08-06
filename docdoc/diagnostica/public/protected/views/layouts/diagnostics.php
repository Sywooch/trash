<?php
use dfs\docdoc\components\Partner;
use dfs\docdoc\components\Version;

$atMainPage = Yii::app()->getController() instanceof SiteController;
$atDiagnosticsPage = Yii::app()->getController() instanceof DiagnosticsController;
$atSiteMapPage = Yii::app()->getController() instanceof SiteMapController;

/**
 * Альтернативная версия страницы
 */
$isInBTest = Yii::app()->referral->isABTest() === Partner::AB_TEST_B;
$abTestVersion = 6;
$dimensionValue = ($isInBTest ? 'new design' : 'old design') . $abTestVersion;

$version = new Version();
$staticVersion = abs(crc32(__DIR__ . $version->getCurrent() . $isInBTest . 2));

?>
<!DOCTYPE html>
<html <?php echo $atMainPage ? 'class="homepage"' : ''; ?>>
<head>
	<title><?php echo htmlspecialchars($this->pageTitle); ?></title>
	<meta http-equiv="Pragma" content="no-cache"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="description" content="<?php echo htmlspecialchars($this->metaDescription); ?>"/>
	<meta name="keywords" content="<?php echo htmlspecialchars($this->metaKeywords); ?>"/>

	<link href="/st/i/common/favicon.ico" rel="icon" type="image/x-icon"/>
	<link rel="apple-touch-icon-precomposed" href="/st/i/common/touch-icon-iphone-precomposed.png" />
	<link rel="apple-touch-icon-precomposed" sizes="76x76" href="/st/i/common/touch-icon-ipad-precomposed.png" />
	<link rel="apple-touch-icon-precomposed" sizes="120x120" href="/st/i/common/touch-icon-iphone-retina-precomposed.png" />
	<link rel="apple-touch-icon-precomposed" sizes="152x152" href="/st/i/common/touch-icon-ipad-retina-precomposed.png" />

	<!--[if lt IE 9]>
	<script src="/st/js/plugin/html5shiv.js?<?=$staticVersion?>"></script>
	<![endif]-->

	<link rel="stylesheet" href="<?php echo Yii::app()->homeUrl; ?>st/css/normalize.css?<?=$staticVersion?>"/>
	<link rel="stylesheet" href="<?php echo Yii::app()->homeUrl; ?>st/css/diagnostics.css?<?=$staticVersion?>"/>
	<link rel="stylesheet" href="<?php echo Yii::app()->homeUrl; ?>st/css/icons.css?<?=$staticVersion?>"/>
	<link rel="stylesheet" href="<?php echo Yii::app()->homeUrl; ?>st/css/metro.css?<?=$staticVersion?>"/>
	<link rel="stylesheet" href="<?php echo Yii::app()->homeUrl; ?>st/js/datetimepicker/jquery.datetimepicker.css?<?=$staticVersion?>"/>
	<?php if ($this->isMobile): ?>
		<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->homeUrl ?>st/css/mobile.css?<?=$staticVersion?>">
	<?php endif; ?>

	<!--[if lte IE 9]>
	<link rel="stylesheet" href="<?php echo Yii::app()->homeUrl; ?>st/css/ielove/ie9lte.css?<?=$staticVersion?>"/>
	<![endif]-->
	<!--[if lte IE 8]>
	<link rel="stylesheet" href="<?php echo Yii::app()->homeUrl; ?>st/css/ielove/ie8lte.css?<?=$staticVersion?>"/>

	<script type="text/javascript" src="<?php echo Yii::app()->homeUrl; ?>st/js/ie8lte.js?<?=$staticVersion?>"></script>
	<![endif]-->
	<script src="<?php echo Yii::app()->homeUrl; ?>st/js/priority.js?<?=$staticVersion?>"></script>
	<script src="<?php echo Yii::app()->homeUrl; ?>st/js/plugin/jquery-1.9.1.min.js?<?=$staticVersion?>"></script>
	<script src="<?php echo Yii::app()->homeUrl; ?>st/js/plugin/modernizr.2.7.0.js?<?=$staticVersion?>"></script>
	<script src="<?php echo Yii::app()->homeUrl; ?>st/js/datetimepicker/jquery.datetimepicker.js?<?=$staticVersion?>"></script>

	<script type="text/javascript" src="<?php echo Yii::app()->homeUrl; ?>st/js/ga.js?<?=$staticVersion?>"></script>

	<script type="text/javascript">
		if (typeof ga !== 'undefined') {
			ga('set', 'dimension1', '<?=$dimensionValue?>');
		} else {
			$(document).on('gaCreated', function () {
				ga('set', 'dimension1', '<?=$dimensionValue?>' );
			});
		}
	</script>
	<script>
		var requestType = "docDocDiagnostic";
	</script>
</head>

<body <?php echo $this->isMobile ? 'class="l-site-mobile"' : ''; ?>>

<?php if (SHOW_STAT) { ?>
	<!-- Google Tag Manager -->
	<noscript><iframe src="//www.googletagmanager.com/ns.html?id=<?php
		echo Yii::app()->city->getCity()->diagnostic_gtm; ?>"
	                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
			new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
			j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
			'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','<?php
		echo Yii::app()->city->getCity()->diagnostic_gtm; ?>');</script>
	<!-- End Google Tag Manager -->
<?php }?>

<!--[if lte IE 7]>
<div class="chromeframe">Вы используете <strong>устаревший</strong> браузер. Пожалуйста, <a
	href="http://browsehappy.com/">обновите ваш браузер</a> или <a
	href="http://www.google.com/chromeframe/?redirect=true">добавьте в него Google Chrome Frame</a> чтобы улучшить его
	возможности.
</div>
<![endif]-->
<header class="l-header l-wrapper">
	<div class="b-header_wrap <?php echo $this->isLandingPage ? 'without_search' : '';?>">
		<div class="logo">

			<?php if ($this->isLandingPage) {?>

				<span class="logo_link">
					<img class="m-fit" src="<?php echo Yii::app()->homeUrl; ?>st/i/common/logo.png" alt="DocDoc.ru">
				</span>

			<?php } else {?>

				<div id="ChangeCityBlock" class="b-dropdown tooltip city-selector">
					<div class="b-dropdown_item b-dropdown_item__current">
						<span id="CurrentCityName" class="b-dropdown_item__text"><?=Yii::app()->city->getTitle()?></span>
						<span class="b-dropdown_item__icon"></span>
					</div>
					<ul class="b-dropdown_list">
						<?php foreach ($this->cities as $key => $city) {?>
							<li class="b-dropdown_item <?php echo $city->id_city == Yii::app()->city->getCityId() ? 's-current' : '';?>"
							    data-cityid="<?=$city->id_city?>">
								<?=$city->title?>
							</li>
						<?php }?>
					</ul>

					<form class="b-dropdown_form" name="cityselector" method="get" action="/changeCity" >
						<input class="b-dropdown_input" name="cityId" type="hidden" value="<?=Yii::app()->city->getCityId()?>" />
					</form>
				</div>
				<a class="logo_link" href="/">
					<img class="m-fit" src="<?php echo Yii::app()->homeUrl; ?>st/i/common/logo.png"
					     alt="DocDoc.ru" title="На главную страницу">
				</a>

			<?php }?>

			<div class="b-dropdown tooltip site-selector">
				<div class="b-dropdown_item b-dropdown_item__current">
					<span class="b-dropdown_item__text">Диагностические центры</span><span
						class="b-dropdown_item__icon"></span>
				</div>
				<ul class="b-dropdown_list">
					<li class="b-dropdown_item">Диагностические центры</li>
					<a href="http://<?php echo Yii::app()->city->getSubDomain();?><?=Yii::app()->params['hosts']['front']?>"><li class="b-dropdown_item">Поиск врачей</li></a>
				</ul>
			</div>
		</div>
		<div class="header_contact i-doctor_r">
			Сервис по поиску <br> диагностических центров
		</div>
		<div class="header_info i-doctor_r">
			<div class="header_info_item_wrap">
				<div class="header_info_item i-tick l-ib">Удобная запись на приём</div>
				<div class="header_info_item l-ib"><span
						class="header_info_num"><?php echo $this->getCountFormatted('countVisitedWeek'); ?></span> <?php echo RussianTextUtils::caseForNumber($this->countVisitedWeek, array('посетитель', 'посетителя', 'посетителей')); ?>
					за месяц
				</div>
			</div>
			<div class="header_info_item_wrap">
				<div class="header_info_item i-tick l-ib">Крупнейшая база данных</div>
				<div class="header_info_item l-ib"><span
						class="header_info_num"><?php echo $this->getCountFormatted(); ?></span> <?php echo RussianTextUtils::caseForNumber($this->countTotalClinic, array('центр', 'центра', 'центров')); ?>
					в базе
				</div>
			</div>
			<div class="header_info_item_wrap">
				<div class="header_info_item i-tick l-ib">Любые типы диагностики</div>
				<div class="header_info_item l-ib"><span class="header_info_num"><?php echo count($this->diagnostics);?></span><?php echo RussianTextUtils::caseForNumber($this->countTotalClinic, array('тип', 'типа', 'типов')); ?> диагностики</div>
			</div>
		</div>
	</div>
	<?php if (!$this->isLandingPage) {?>
		<?php $this->widget('\dfs\docdoc\diagnostica\widgets\SearchFormWidget', array(
			'diagnostic'        => $this->diagnostic,
			'parentDiagnostic'  => $this->parentDiagnostic,
			'area'              => $this->area,
			'stations'          => $this->stations,
			'districts'         => $this->district,
			'isMobile'          => $this->isMobile,
		));?>
	<?php } else {?>
		<div class="no_search l-wrapper"></div>
	<?php }?>
</header>
<main class="l-main l-wrapper <?php echo $this->isLandingPage ? 'landing' : '';?>" role="main">
	<?php if ($_SERVER['REQUEST_URI'] == Yii::app()->homeUrl): ?>
		<ul class="spec_list columns_5">
			<li class="column">
				<div class="spec_list_head"><a href="/uzi/">УЗИ</a></div>
				<div>&ndash; <a href="/uzi-dlya-beremennih/">при беременности</a></div>
				<div>&ndash; <a href="/uzi/uzi-brushnoi-polosti/">брюшной полости</a></div>
				<div>&ndash; <a href="/uzi/uzi-malogo-taza/">малого таза</a></div>
				<div>&ndash; <a href="/uzi/uzi-pochek/">почек</a></div>
				<div>&ndash; <a href="/ehokardiografiya/">сердца (ЭХОКГ)</a></div>
				<div>&ndash; <a href="/3d-uzi/">3D УЗИ</a></div>
			</li>
			<li class="column">
				<div class="spec_list_head"><a href="/komputernaya-tomografiya/">КТ</a></div>
				<div>&ndash; <a href="/komputernaya-tomografiya/kt-golovi/">головного мозга</a></div>
				<div>&ndash; <a href="/komputernaya-tomografiya/kt-leghih-i-serdca/">легких</a></div>
				<div>&ndash; <a href="/komputernaya-tomografiya/kt-brushnoi-polosti/">брюшной полости</a></div>
				<div>&ndash; <a href="/komputernaya-tomografiya/kt-pozvonochnika/">позвоночника</a></div>
				<div>&ndash; <a href="/komputernaya-tomografiya/kt-grudnoi-kletki/">грудной клетки</a></div>
				<div>&ndash; <a href="/komputernaya-tomografiya/kt-pochek/">почек</a></div>
			</li>
			<li class="column">
				<div class="spec_list_head"><a href="/mrt/">МРТ</a></div>
				<div>&ndash; <a href="/mrt/mrt-golovnogo-mozga/">головного мозга</a></div>
				<div>&ndash; <a href="/mrt/mrt-otdelov-pozvonochnika/">позвоночника</a></div>
				<div>&ndash; <a href="/mrt/mrt-kolennogo-sustava/">коленного сустава</a></div>
				<div>&ndash; <a href="/mrt/mrt-brushnoi-polosti/">брюшной полости</a></div>
				<div>&ndash; <a href="/mrt/mrt-malogo-taza/">малого таза</a></div>
				<div>&ndash; <a href="/mrt/mrt-gipofiza/">гипофиза</a></div>
			</li>
			<li class="column">
				<div class="spec_list_head"><a href="/rentgen/">Рентген</a></div>
				<div>&ndash; <a href="/rentgen/rentgen-legkih/">легких</a></div>
				<div>&ndash; <a href="/rentgen/rentgen-pozvonochnika/">позвоночника</a></div>
				<div>&ndash; <a href="/rentgen/rentgen-grudnoi-kletki/">грудной клетки</a></div>
				<div>&ndash; <a href="/rentgen/rentgen-sustavov/">суставов</a></div>
				<div>&ndash; <a href="/rentgen/mammografia/">маммография</a></div>
				<div>&ndash; <a href="/rentgen/rentgen_tolstoj_kishki_irrigoskopia/">ирригоскопия</a></div>
			</li>
			<li class="column">
				<div><a href="/fluorografiya/">Флюорография</a></div>
				<div><a href="/func-diagnostika/sutochnoe-ekg/">Электрокардиография (ЭКГ)</a></div>
				<div><a href="/endoskopicheskie-issledovaniya/kolonoskopiya/">Колоноскопия</a></div>
				<div><a href="/gastroskopiya/">Гастроскопия</a></div>
				<div><a href="/bronhoskopiya/">Бронхоскопия</a></div>
				<div><a href="/endoskopicheskie-issledovaniya/rektoromanoskopiya/">Ректороманоскопия</a></div>
				<div><a href="/densitometriia/">Денситометрия</a></div>
			</li>
		</ul>
	<?php endif; ?>
	<?php echo $content; ?>
</main>
<footer class="l-footer l-wrapper">
	<div class="footer_group">
		<h4 class="footer_group_title">О проекте</h4>
		<ul class="footer_about_list">
			<li class="footer_about_item"><a href="http://<?php echo Yii::app()->city->getSubDomain() . Yii::app()->params['hosts']['front'] ;?>/about" class="footer_about_link">О сервисе</a></li>
			<li class="footer_about_item"><a href="http://<?php echo Yii::app()->city->getSubDomain(). Yii::app()->params['hosts']['front'] ;?>/doctor" class="footer_about_link">Все врачи</a></li>
			<li class="footer_about_item"><a href="/diagnostici" class="footer_about_link">Вся диагностика</a></li>
		</ul>
		<div class="footer_forbes">
			<a target="_blank"
			   href="http://www.forbes.ru/svoi-biznes-photogallery/startapy/240008-final-konkursa-startapov-forbes-20122013/photo/5"
			   class="footer_about_link_img"><img class=""
			                                      src="/st/i/common/forbes.png"
			                                      alt="Forbes" title="Forbes - Призер 'Стартап 2012'">Призер "Стартап
				2012"</a>
		</div>
		<p class="footer_copyright">Diagnostica.docdoc.ru – поиск диагностических центров в
			<?php foreach ($this->cities as $key => $city) {?>
				<a class="footer_copyright_link" href="http://<?php echo $city->prefix . Yii::app()->params['hosts']['diagnostica'];?>"><?php echo $city->title_prepositional;?></a>
				<?php echo $key < (count($this->cities) - 1) ? 'и' : '';?>
			<?php }?></p>

		<p class="footer_copyright">
			Copyright <?php echo date('Y'); ?>  &copy;
			<a href="<?php echo $this->createUrl('/sitemap'); ?>" class="footer_copyright_link">Карта сайта</a>
			| <a href="//<?=Yii::app()->params['hosts']['front']?>/affiliate" class="footer_copyright_link">Партнерская программа</a>
		</p>
	</div>
	<!--
			 -->
	<div class="footer_group">
		<h4 class="footer_group_title">Врачам и клиникам</h4>
		<ul class="footer_lk">
			<li class="footer_lk_item i-lk_enter"><a href="https://<?=Yii::app()->params['hosts']['front']?>/lk/auth" class="footer_lk_link">Личный
					кабинет</a></li>
			<li class="footer_lk_item i-lk_reg"><a
					href="https://<?=Yii::app()->params['hosts']['front']?>/register"
					class="footer_lk_link">Регистрация</a></li>
		</ul>
		<p class="footer_lk_info">Регистрация диагностических центров на портале <span class="t-uc">БЕСПЛАТНА</span>.
		</p>
	</div>
</footer>

<div class="popups">
	<?php
	$this->renderPartial('//diagnostics/popup/diaSpec', array('diagnostics' => $this->diagnostics), false);
	if (!$this->isMobile) $this->renderPartial('//diagnostics/popup/map', true);
	?>
	<div class="js-popup popup request request-form-container" data-popup-id="js-popup-request-clinic" data-noscroll="1">
	<?php
		$this->widget('\dfs\docdoc\diagnostica\widgets\RequestFormWidget', array(
			'diagnostic'        => $this->diagnostic,
			'parentDiagnostic'  => $this->parentDiagnostic,
		));
	?>
	</div>
</div>
<div class="popup_bg"></div>
<script src="/st/js/plugins.js?<?=$staticVersion?>"></script>
<script src="/st/js/plugin/json2.js?<?=$staticVersion?>"></script>
<script src="/st/js/maps.js?<?=$staticVersion?>"></script>
<script src="/st/js/plugin/jquery-ui-1.10.3.min.js?<?=$staticVersion?>"></script>
<link rel="stylesheet" href="/st/css/ui/ui-lightness/jquery-ui-1.10.3.custom.min.css?<?=$staticVersion?>">
<script src="/st/js/metro.js?<?=$staticVersion?>"></script>
<script src="/st/js/extended_search.js?<?=$staticVersion?>"></script>
<script src="/st/js/plugin/dotdotdot.min.js?<?=$staticVersion?>"></script>
<script src="/st/js/ddpopup.js?<?=$staticVersion?>"></script>
<script src="/static/js/diagnostic/requestWidget.js?<?=$staticVersion?>"></script>
<script src="/st/js/main.js?<?=$staticVersion?>"></script>
<script src="/st/js/plugin/jquery.maskedinput.min.js?<?=$staticVersion?>"></script>
<script src="/st/js/plugin/validate.js?<?=$staticVersion?>"></script>

<script>
	Modernizr.load([{
		test: Modernizr.input.placeholder,
		nope: [
			'<?php echo Yii::app()->homeUrl;?>st/css/polyfills/polyfill_placeholder.css?<?=$staticVersion?>',
			'<?php echo Yii::app()->homeUrl;?>st/js/polyfills/polyfill_placeholder.js?<?=$staticVersion?>',
			'<?php echo Yii::app()->homeUrl;?>st/js/polyfills/polyfill_placeholder_onchange.js?<?=$staticVersion?>'
		]
	}]);
</script>

<?php if ($this->isMobile): ?>
	<script type="text/javascript" src="<?php echo Yii::app()->homeUrl ?>st/js/mobile.js?<?=$staticVersion?>"></script>

<?php endif; ?>

<?php if (SHOW_STAT) { ?>
	<!-- Yandex.Metrika counter -->
	<script type="text/javascript">
		(function (d, w, c) {
			(w[c] = w[c] || []).push(function () {
				try {
					w.yaCounter<?=Yii::app()->city->getDiagnosticYandexMetrikaProfileId()?> = new Ya.Metrika({id: '<?=Yii::app()->city->getDiagnosticYandexMetrikaProfileId()?>', enableAll: true, webvisor: true});
				} catch (e) {
				}
			});

			var n = d.getElementsByTagName("script")[0],
				s = d.createElement("script"),
				f = function () {
					n.parentNode.insertBefore(s, n);
				};
			s.type = "text/javascript";
			s.async = true;
			s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

			if (w.opera == "[object Opera]") {
				d.addEventListener("DOMContentLoaded", f);
			} else {
				f();
			}
		})(document, window, "yandex_metrika_callbacks");
	</script>
	<noscript>
		<div><img src="//mc.yandex.ru/watch/<?=Yii::app()->city->getDiagnosticYandexMetrikaProfileId()?>" style="position:absolute; left:-9999px;" alt=""/></div>
	</noscript>
	<!-- /Yandex.Metrika counter -->
<?php } ?>

</body>
</html>
