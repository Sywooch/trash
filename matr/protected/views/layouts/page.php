<!DOCTYPE HTML>
<html>
<head>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<title>Русская матрешка</title>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,800,300,700&subset=latin,cyrillic-ext'
		  rel='stylesheet' type='text/css'>

	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
	<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css"/>

	<link href="<?php echo Yii::app()->baseUrl; ?>/js/select2-3.4.6/select2.css" rel="stylesheet"/>
	<script src="<?php echo Yii::app()->baseUrl; ?>/js/select2-3.4.6/select2.js"></script>

	<script src="<?php echo Yii::app()->baseUrl; ?>/js/jquery.maskedinput.js"></script>

	<script src="<?php echo Yii::app()->baseUrl; ?>/js/common.js"></script>
	<link rel="stylesheet" href="<?php echo Yii::app()->baseUrl; ?>/css/style.css"/>
	<meta name="w1-verification" content="163388444121"/>
</head>
<body>

<div id="templatmeo_container_outter">
	<div id="templatmeo_container_inner_01">
		<div id="templatmeo_container_inner_02">
			<div id="templatemo_menu">
				<ul>
					<li><a href="/" class="first">Главная</a></li>
					<li><a href="/how">Как это работает?</a></li>
					<li><a href="/news">Новости</a></li>
					<li><a href="/faq">Вопрос-ответ</a></li>
					<li><a href="/history">История</a></li>
					<li><a href="/docs">Документы</a></li>
					<?php if (Yii::app()->user->isGuest) { ?>
						<li><a href="/registration">Регистрация</a></li>
					<?php } else { ?>
						<li><a href="/lk">Личный кабинет</a></li>
					<?php } ?>
				</ul>
			</div>
			<!-- end of menu -->

			<div id="templatemo_content">
				<div id="content_top"></div>
				<div id="content_top_left"></div>
				<div id="ccontainer">
					<?php echo $content; ?>
				</div>

				<div id="content_bottom"></div>
			</div>

			<div id="templatemo_footer">

				<div class="footer_section left_section">
					<h4>Время работы</h4>

					<?php echo Text::model()->findByPk(2)->text; ?>
				</div>

				<div class="footer_section right_section">
					<h4>Контакты</h4>

					<?php echo Text::model()->findByPk(3)->text; ?>
				</div>

				<div class="margin_bottom_10"></div>
				Copyright © 2015-3015 Русская матрешка
			</div>

			<div class="cleaner"></div>
		</div>
		<div class="cleaner">&nbsp;</div>
	</div>
</div>


</body>
</html>