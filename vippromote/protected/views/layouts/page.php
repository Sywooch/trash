<!DOCTYPE HTML>
<html>
<head>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $this->title; ?></title>
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,800,300,700&subset=latin,cyrillic-ext' rel='stylesheet' type='text/css'>

<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css"/>

<link href="<?php echo Yii::app()->baseUrl; ?>/js/select2-3.4.6/select2.css" rel="stylesheet"/>
<script src="<?php echo Yii::app()->baseUrl; ?>/js/select2-3.4.6/select2.js"></script>

<script src="<?php echo Yii::app()->baseUrl; ?>/js/jquery.maskedinput.js"></script>

<script src="<?php echo Yii::app()->baseUrl; ?>/js/common.js"></script>
<link rel="stylesheet" href="<?php echo Yii::app()->baseUrl; ?>/css/style.css"/>
	<meta name="w1-verification" content="163388444121" />
</head>
<body>

<div id="header">
	<ul class="menu">
		<li><a href="/advertisement/">Реклама</a></li>
		<li><a href="/rules/">Правила</a></li>
		<li><a href="/news/">Новости</a></li>
		<li><a href="/contacts/">Контакты</a></li>
		<?php if (Yii::app()->user->isGuest) { ?>
			<li><?php 
				echo CHtml::ajaxLink(
					"Войти",
					$this->createUrl("user/login"),
					array(
						"success" => 'function(data) {
		                   $("body").append(data);
		                   $(".login-window .close, .login-overlay").on("click", function(){
		                        $("#login-form-container").remove();
		                   });
		                }',
					),
					array(
						"class" => "enter lk-link",
						"id"    => uniqid(),
						"live"  => false,
					)
				);
			?>
			</li>
			<li><a href="/registration/" class="lk-link">Регистрация</a></li>
		<?php } else { ?>
			<li><a href="/logout/" class="lk-link">Выйти</a></li>
			<li><a href="/lk/" class="lk-link">Личный кабинет</a></li>
		<?php } ?>

	</ul>
	<a href="/" id="logo"><img src="/images/logo.png" /></a>
</div>

<div id="content-container">
	<?php echo $content; ?>
</div>

<div id="footer">

	<div class="bottom-nav">
		<div class="left">
			Если нужна помощь, звоните: <br>
			Тел.: +7(920)891-96-75 <br>
			Скайп: alexhodackov
		</div>
		<div class="right">
			<ul class="links">
				<li><a href="/">Главная</a></li>
				<li><a href="/advertisement/">Реклама</a></li>
				<li><a href="/rules/">Правила</a></li>
				<li><a href="/news/">Новости</a></li>
				<li><a href="/contacts/">Контакты</a></li>
			</ul>
		</div>
		<div class="attantion-footer">Внимание! Компания не несет отвественности за рекламирумые товары и услуги.</div>
	</div>
</div>

<!-- Yandex.Metrika informer -->
<a href="https://metrika.yandex.ru/stat/?id=26384487&amp;from=informer"
   target="_blank" rel="nofollow"><img src="//bs.yandex.ru/informer/26384487/3_1_FFFFFFFF_EFEFEFFF_0_pageviews"
									   style="width:1px; height:1px; border:0;" alt="Яндекс.Метрика" title="Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)" onclick="try{Ya.Metrika.informer({i:this,id:26384487,lang:'ru'});return false}catch(e){}"/></a>
<!-- /Yandex.Metrika informer -->

<!-- Yandex.Metrika counter -->
<script type="text/javascript">
	(function (d, w, c) {
		(w[c] = w[c] || []).push(function() {
			try {
				w.yaCounter26384487 = new Ya.Metrika({id:26384487,
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
<noscript><div><img src="//mc.yandex.ru/watch/26384487" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	ga('create', 'UA-55238786-1', 'auto');
	ga('send', 'pageview');

</script>

</body>
</html>