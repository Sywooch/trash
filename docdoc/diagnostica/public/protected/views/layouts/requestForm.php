<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Pragma" content="no-cache"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link href="/st/i/common/favicon.ico" rel="icon" type="image/x-icon"/>
	<link rel="apple-touch-icon-precomposed" href="/st/i/common/touch-icon-iphone-precomposed.png" />
	<link rel="apple-touch-icon-precomposed" sizes="76x76" href="/st/i/common/touch-icon-ipad-precomposed.png" />
	<link rel="apple-touch-icon-precomposed" sizes="120x120" href="/st/i/common/touch-icon-iphone-retina-precomposed.png" />
	<link rel="apple-touch-icon-precomposed" sizes="152x152" href="/st/i/common/touch-icon-ipad-retina-precomposed.png" />

	<!--[if lt IE 9]>
	<script src="/st/js/plugin/html5shiv.js"></script>
	<![endif]-->

	<link rel="stylesheet" href="<?php echo Yii::app()->homeUrl; ?>st/css/normalize.css"/>
	<link rel="stylesheet" href="<?php echo Yii::app()->homeUrl; ?>st/css/diagnostics.css?up"/>
	<link rel="stylesheet" href="<?php echo Yii::app()->homeUrl; ?>st/css/icons.css"/>
	<link rel="stylesheet" href="<?php echo Yii::app()->homeUrl; ?>st/css/metro.css"/>
	<link rel="stylesheet" href="<?php echo Yii::app()->homeUrl; ?>st/js/datetimepicker/jquery.datetimepicker.css"/>
	<?php if ($this->isMobile): ?>
		<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->homeUrl ?>st/css/mobile.css">
	<?php endif; ?>

	<!--[if lte IE 9]>
	<link rel="stylesheet" href="<?php echo Yii::app()->homeUrl; ?>st/css/ielove/ie9lte.css"/>
	<![endif]-->
	<!--[if lte IE 8]>
	<link rel="stylesheet" href="<?php echo Yii::app()->homeUrl; ?>st/css/ielove/ie8lte.css"/>

	<script type="text/javascript" src="<?php echo Yii::app()->homeUrl; ?>st/js/ie8lte.js"></script>
	<![endif]-->
	<script src="<?php echo Yii::app()->homeUrl; ?>st/js/priority.js"></script>
	<script src="<?php echo Yii::app()->homeUrl; ?>st/js/plugin/jquery-1.9.1.min.js"></script>
	<script src="<?php echo Yii::app()->homeUrl; ?>st/js/plugin/modernizr.2.7.0.js"></script>
	<script src="<?php echo Yii::app()->homeUrl; ?>st/js/datetimepicker/jquery.datetimepicker.js"></script>
	<script src="/st/js/plugins.js"></script>
	<script src="/st/js/plugin/json2.js"></script>
	<script src="/st/js/maps.js"></script>
	<script src="/st/js/plugin/jquery-ui-1.10.3.min.js"></script>
	<link rel="stylesheet" href="/st/css/ui/ui-lightness/jquery-ui-1.10.3.custom.min.css">
	<script src="/st/js/metro.js"></script>
	<script src="/st/js/extended_search.js"></script>
	<script src="/st/js/plugin/dotdotdot.min.js"></script>
	<script src="/st/js/ddpopup.js"></script>
	<script src="/static/js/diagnostic/requestWidget.js"></script>
	<script src="/st/js/main.js"></script>
	<script src="/st/js/plugin/jquery.maskedinput.min.js"></script>
	<script src="/st/js/plugin/validate.js"></script>
	<script>
		var requestType = "widget";
	</script>
</head>

<body <?php echo $this->isMobile ? 'class="l-site-mobile"' : ''; ?>>
<div class="request-form-inline">
<?php
	echo $content;
?>
</div>

<script>
	Modernizr.load([{
		test: Modernizr.input.placeholder,
		nope: [
			'<?php echo Yii::app()->homeUrl;?>st/css/polyfills/polyfill_placeholder.css',
			'<?php echo Yii::app()->homeUrl;?>st/js/polyfills/polyfill_placeholder.js',
			'<?php echo Yii::app()->homeUrl;?>st/js/polyfills/polyfill_placeholder_onchange.js'
		]
	}]);
</script>

<?php if ($this->isMobile): ?>
	<script type="text/javascript" src="<?php echo Yii::app()->homeUrl ?>st/js/mobile.js"></script>

<?php endif; ?>
</body>
</html>
