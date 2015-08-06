<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width"/>
	<title>Форма записи</title>
	<link type="text/css" href="/static/booking/base.css" rel="stylesheet">
	<link type="text/css" href="/static/booking/baseTheme.css" rel="stylesheet">
	<link type="text/css" href="/static/booking/widgetTheme.css" rel="stylesheet">
	<?php if ((int)Yii::app()->request->getQuery("doctor")) { ?>
		<link type="text/css" href="/js/datetimepicker/jquery.datetimepicker.css" rel="stylesheet">
	<?php } else { ?>
		<link rel="stylesheet" href="/static/booking/request.css" />
	<?php } ?>
</head>
<body>
	<div class="block"><?=$content?></div>

	<script type="text/javascript" src="/js/jquery/jquery.min.js"></script>
	<?php if ((int)Yii::app()->request->getQuery("doctor")) { ?>
		<script type="text/javascript" src="/js/datetimepicker/jquery.datetimepicker.js"></script>
		<script type="text/javascript" src="/js/plugin/jquery.maskedinput.min.js"></script>
		<script type="text/javascript" src="/static/booking/requestForm.js"></script>
		<script type="text/javascript">
			BookingSlider();
		</script>
	<?php } else { ?>
		<script type="text/javascript" src="/js/plugin/jquery.nicescroll.min.js"></script>
		<script type="text/javascript" src="/static/booking/request.js"></script>
	<?php } ?>
</body>
</html>
