<!DOCTYPE html>
<html>
<head>
	<title>Docdoc.ru - личный кабинет клиники V1.0</title>

	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

	<link href="/images/lk/favicon.png" rel="icon" type="image/png">

	<link rel="stylesheet" type="text/css" href="/css/lk/new/main.css" />
	<link rel="stylesheet" type="text/css" href="/js/jquery-ui/themes/smoothness/jquery-ui.min.css" />
	<link rel="stylesheet" type="text/css" href="/css/lk/fullcalendar.css" />
	<link rel="stylesheet" type="text/css" href="/css/lk/schedule.css" />

	<script src="/js/jquery/jquery.min.js"></script>
	<script src="/js/jquery-ui/jquery-ui.min.js"></script>
	<script src="/js/plugin/jquery.mtz.monthpicker.js"></script>
	<script src="/js/plugin/modernizr.js"></script>
	<script src="/js/lk/plugins/highcharts.js"></script>
	<script src="/js/lk/main.js"></script>
	<script src="/js/lk/moment.min.js"></script>
	<script src="/js/lk/fullcalendar.min.js"></script>
</head>
<body>

	<?php echo $this->renderPartial('/elements/header'); ?>

	<section class="content info">
		<aside class="aside">
			<?php echo $this->renderPartial('/../elements/asideMenu', [ 'menu' => $this->getMenu() ]); ?>
			<?php echo $this->renderPartial('/elements/help'); ?>
		</aside>

		<div class="page">
			<?php
				$user = Yii::app()->user;
			?>
			<?php if ($user->hasFlash('success')): ?>
				<div class="reg_form__success">
					<p><?php echo $user->getFlash('success'); ?></p>
				</div>
			<?php endif; ?>

			<?php if ($user->hasFlash('error')): ?>
				<div class="reg_form__errors">
					<p><?php echo $user->getFlash('error'); ?></p>
				</div>
			<?php endif; ?>

			<?php echo $content; ?>
		</div>
	</section>

	<?php echo $this->renderPartial('/elements/footer'); ?>

</body>
</html>