<?php

use dfs\docdoc\front\widgets\PopupsWidget;
use dfs\docdoc\front\controllers\FrontController;

/**
 * @var FrontController $this
 * @var string $content
 */

$seo = Yii::app()->seo;
$gtm = \Yii::app()->city->getCity()->gtm;
?><!DOCTYPE html>
<html<?php echo $this->isMainPage ? ' class="homepage"' : ''; ?>>
<head>
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="description" content="<?php echo $seo->getMetaDescription(); ?>" />
	<meta name="keywords" content="<?php echo $seo->getMetaKeywords(); ?>" />

	<link href="/img/common/favicon.ico" rel="icon" type="image/x-icon" />
	<link rel="apple-touch-icon-precomposed" href="/img/common/touch-icon-iphone-precomposed.png" />
	<link rel="apple-touch-icon-precomposed" sizes="76x76" href="/img/common/touch-icon-ipad-precomposed.png" />
	<link rel="apple-touch-icon-precomposed" sizes="120x120" href="/img/common/touch-icon-iphone-retina-precomposed.png" />
	<link rel="apple-touch-icon-precomposed" sizes="152x152" href="/img/common/touch-icon-ipad-retina-precomposed.png" />

	<!--[if lt IE 9]>
	<script src="/js/plugin/html5shiv.js"></script>
	<![endif]-->

	<link rel="stylesheet" href="/css/normalize.css" />
	<link rel="stylesheet" href="/css/docdoc.css?up" />
	<link rel="stylesheet" href="/css/icons.css" />

	<script src="/js/plugin/jquery-1.9.1.min.js"></script>

	<script type="text/javascript">
		var DD = {};

		$(document).on('gaCreated', function (e) {
			if(typeof(ga) == 'function'){
				ga(function (tracker) {
					var clientId = tracker.get('clientId');
					document.cookie = "_ga_cl=" + clientId + '; path=/; expires=Tue, 19 Jan 2038 03:14:07 GMT;';
				});
			}
		});
	</script>

	<!--[if lte IE 9]>
	<link rel="stylesheet" href="/css/ielove/ie9lte.css" />
	<![endif]-->
	<!--[if lte IE 8]>
	<link rel="stylesheet" href="/css/ielove/ie8lte.css" />
	<script type="text/javascript" src="/js/ie8lte.js"></script>
	<![endif]-->

	<?php if ($this->isMobile): ?>
		<link rel="stylesheet" href="/css/mobile.css?up" />
	<?php endif; ?>

	<script src="/js/plugin/modernizr.2.7.0.js"></script>
	<script type="text/javascript" src="/js/plugin/dotdotdot.min.js"></script>

	<title><?php echo $seo->getTitle() ?: 'DocDoc - поиск врачей'; ?></title>
</head>
<body<?php echo $this->isMobile ? ' class="mobile"' : ''; ?>>

	<?php if ($gtm): ?>
		<!-- Google Tag Manager -->
		<noscript>
			<iframe src="//www.googletagmanager.com/ns.html?id=<?php echo $gtm; ?>" height="0" width="0" style="display:none; visibility:hidden"/>
		</noscript>
		<script>
			(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
				new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
				j=d.createElement(s),dl=l!='dataLayer'?'&amp;l='+l:'';j.async=true;j.src=
				'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
			})(window,document,'script','dataLayer','<?php echo $gtm; ?>');
		</script>
		<!-- End Google Tag Manager -->
	<?php endif; ?>

	<!--[if lte IE 7]>
	<p class="chromeframe">
		Вы используете <strong>устаревший</strong> браузер.
		Пожалуйста, <a href="http://browsehappy.com/">обновите ваш браузер</a> или
		<a href="http://www.google.com/chromeframe/?redirect=true">добавьте в него Google Chrome Frame</a>
		чтобы улучшить его возможности.
	</p>
	<![endif]-->

	<?php echo $this->renderPartial('/elements/header'); ?>

	<?php echo $content; ?>

	<?php $this->widget(PopupsWidget::class, [
		'isMainPage' => $this->isMainPage,
		'isMobile' => $this->isMobile,
		'phoneForPage' => $this->phoneForPage,
	]); ?>

	<?php echo $this->renderPartial('/elements/footer'); ?>
	<?php echo $this->renderPartial('/elements/footerScripts'); ?>

</body>
</html>