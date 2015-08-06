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
			<li><a href="/?registration=1" class="lk-link">Регистрация</a></li>
		<?php } else { ?>
			<li><a href="/logout" class="lk-link">Выйти</a></li>
			<li><a href="/lk" class="lk-link">Личный кабинет</a></li>
		<?php } ?>
		<li><a href="/contacts/">Контакты</a></li>
		<li><a href="/offers/">Ваши предложения</a></li>
		<li><a href="/faq/">Вопрос-ответ</a></li>
		<li><a href="/shop/">Магазин</a></li>
		<li><a href="/docs/">Документация</a></li>
		<li><a href="/news/">Новости</a></li>
		<li><a href="/program/">Программа скидок</a></li>
	</ul>
	<a href="/" id="logo"><img src="/images/small-logo.png" /></a>
</div>

<?php echo $content; ?>


<div id="footer">
	<div class="icons">
		<div class="cover">
			<div class="find">Ищете оригинальный подарок?</div>
			<div class="find2">Вы найдете его в нашем <a href="/shop/">интернет-магазине</a></div>
			<a href="/shop/" class="shop-image"><img src="/images/present.png" /></a>
		</div>
	</div>
	<div class="bottom-nav">
		<div class="left">
			<div class="social-networks">
				<!--
				<a href="#" class="social vk" rel="nofollow" target="_blank"></a>
				<a href="#" class="social ok" rel="nofollow" target="_blank"></a>
				<a href="#" class="social fb" rel="nofollow" target="_blank"></a>
				<a href="#" class="social tw" rel="nofollow" target="_blank"></a>
				<a href="#" class="social yt" rel="nofollow" target="_blank"></a>
				-->
			</div>
		</div>
		<div class="right">
			<ul class="links">
				<li><a href="/program/">Программа скидок</a></li>
				<li><a href="/news/">Новости</a></li>
				<li><a href="/docs/">Документация</a></li>
				<li><a href="/shop/">Магазин</a></li>
				<li><a href="/faq/">Вопрос-ответ</a></li>
				<li><a href="/offers/">Ваши предложения</a></li>
				<li><a href="/contacts/">Контакты</a></li>
			</ul>
		</div>
	</div>
</div>

<!-- Yandex.Metrika informer -->
<a href="https://metrika.yandex.ru/stat/?id=25705373&amp;from=informer"
   target="_blank" rel="nofollow"><img src="//bs.yandex.ru/informer/25705373/3_1_FFFFFFFF_EFEFEFFF_0_pageviews"
									   style="width:1px; height:1px; border:0;" alt="Яндекс.Метрика" title="Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)" onclick="try{Ya.Metrika.informer({i:this,id:25705373,lang:'ru'});return false}catch(e){}"/></a>
<!-- /Yandex.Metrika informer -->

<!-- Yandex.Metrika counter -->
<script type="text/javascript">
	(function (d, w, c) {
		(w[c] = w[c] || []).push(function() {
			try {
				w.yaCounter25705373 = new Ya.Metrika({id:25705373,
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
<noscript><div><img src="//mc.yandex.ru/watch/25705373" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

<!-- Start Alexa Certify Javascript -->
<script type="text/javascript">
	_atrk_opts = { atrk_acct:"7sVQj1a8y100ya", domain:"kv500.com",dynamic: true};
	(function() { var as = document.createElement('script'); as.type = 'text/javascript'; as.async = true; as.src = "https://d31qbv1cthcecs.cloudfront.net/atrk.js"; var s = document.getElementsByTagName('script')[0];s.parentNode.insertBefore(as, s); })();
</script>
<noscript><img src="https://d5nxst8fruw4z.cloudfront.net/atrk.gif?account=7sVQj1a8y100ya" style="display:none" height="1" width="1" alt="" /></noscript>
<!-- End Alexa Certify Javascript -->

</body>
</html>