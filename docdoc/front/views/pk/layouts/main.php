<!DOCTYPE html>
<html>
<head>
	<title>Docdoc.ru - партнерский кабинет клиники V1.0</title>

	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

	<link rel="stylesheet" type="text/css" href="/css/lk/new/main.css" />
	<link rel="stylesheet" type="text/css" href="/js/jquery-ui/themes/smoothness/jquery-ui.min.css" />

	<script src="/js/jquery/jquery.min.js"></script>
	<script src="/js/jquery-ui/jquery-ui.min.js"></script>
	<script src="/js/lk/plugins/highcharts.js"></script>
	<script src="/js/lk/main.js"></script>
</head>
<body>

	<?php echo $this->renderPartial('/elements/header'); ?>

	<section class="content info">
		<aside class="aside">
			<?php echo $this->renderPartial('/../elements/asideMenu', [ 'menu' => $this->_menu ]); ?>
			<?php echo $this->renderPartial('/elements/stat'); ?>
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