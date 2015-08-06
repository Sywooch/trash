<?php

/**
 * @var dfs\docdoc\front\controllers\FrontController $this
 */

$city = Yii::app()->city;
$referral = Yii::app()->referral;
$referralId = $referral->getId();

$partnerJsFile = '/js/partner/track/' . $referral->getLogin() . '.js';

$YA = $city->getYandexMetrikaProfileId();;
$showComagic = false;
$showCallTouch = false;

if (!$referralId) {
	if ($city->getCityPrefix() == 'spb') {
		$showCallTouch = true;
	} else {
		$showComagic = true;
	}
}
?>

<?php if ($this->globalTrack): ?>
	<script type="text/javascript">
		var global_track = {};
		global_track.name = '<?php echo $this->globalTrack['Name']; ?>';
		global_track.params = <?php echo $this->globalTrack['Params']; ?>;
	</script>
<?php endif; ?>


<?php if ($YA): ?>
	<!-- Yandex.Metrika counter -->
	<script type="text/javascript">
		(function (d, w, c) {
			(w[c] = w[c] || []).push(function() {
				try {
					w.yaCounter<?php echo $YA; ?> = new Ya.Metrika({id:'<?php echo $YA; ?>',
						webvisor:true,
						clickmap:true,
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
	<noscript>
		<div><img src="//mc.yandex.ru/watch/<?php echo $YA; ?>" style="position:absolute; left:-9999px;" alt="" /></div>
	</noscript>
	<!-- Yandex.Metrika counter -->
<?php endif; ?>


<!--[if lte IE 9]>
<script src="/js/ie9lte.js"></script>
<link rel="stylesheet" href="/css/ielove/ie9lte.css" />
<![endif]-->

<script src="/js/plugins.js"></script>
<script src="/js/plugin/json2.js"></script>
<script src="/js/plugin/jquery.raty.js"></script>
<script src="/js/plugin/jquery.maskedinput.min.js"></script>
<script src="/js/plugin/social-likes.min.js"></script>
<script src="/js/plugin/validate.js"></script>
<script src="/js/plugin/validate_additional_methods.js"></script>
<script src="/js/jquery.bxslider/jquery.bxslider.min.js"></script>
<script src="/js/maps.js"></script>

<?php if ($this->isMobile): ?>
	<script src="/js/stat/ga.js"></script>
	<script src="/js/main.js"></script>
	<script src="/js/mobile.js"></script>
<?php else: ?>
	<script src="/js/plugin/jquery-ui-1.10.3.min.js"></script>
	<link rel="stylesheet" href="/css/ui/ui-lightness/jquery-ui-1.10.3.custom.min.css" />

	<?php if ($city->isMoscow()): ?>
		<script src="/js/metro.js"></script>
		<script src="/js/extended_search.js"></script>
	<?php else: ?>
		<script src="/js/search_geo.js"></script>
	<?php endif; ?>

	<script src="/js/ddpopup.js"></script>
	<script src="/js/stat/ga.js"></script>
	<script src="/js/main.js"></script>
<?php endif; ?>

<link type="text/css" href="/css/metro.css" rel="stylesheet" />
<link type="text/css" href="/static/booking/baseTheme.css" rel="stylesheet" />
<link type="text/css" href="/js/datetimepicker/jquery.datetimepicker.css" rel="stylesheet" />

<script type="text/javascript" src="/js/sociomantic.js"></script>
<script type="text/javascript" src="/js/datetimepicker/jquery.datetimepicker.js"></script>
<script type="text/javascript" src="/js/plugin/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="/static/booking/requestForm.js"></script>

<?php if ($referralId && file_exists(ROOT_PATH . '/front/public' . $partnerJsFile)): ?>
	<script type="text/javascript" src="<?php echo $partnerJsFile; ?>"></script>
<?php endif; ?>


<?php if ($showComagic): ?>
	<script type="text/javascript">
		var Comagic = Comagic || [];
		Comagic.push(["setAccount", "gHdRAQbVelteFkWY_nUIZhxkz3Gn67ep"]);
		Comagic.push(["setHost", "http://server.comagic.ru/comagic"]);
	</script>
	<script type="text/javascript" async="async" src="//app.comagic.ru/static/comagic/comagic.min.js"></script>
<?php endif; ?>

<?php if ($showCallTouch): ?>
	<script type="text/javascript">
		(function(w, d, e) {
			var a = 'all', b = 'tou'; var src = b + 'c' +'h'; src = 'm' + 'o' + 'd.c' + a + src;
			var jsHost = (("https:" == d.location.protocol) ? "https://" : "http://")+ src;
			s = d.createElement(e); p = d.getElementsByTagName(e)[0]; s.async = 1; s.src = jsHost +"."+"r"+"u/d_client.js?param;ref"+escape(d.referrer)+";url"+escape(d.URL)+";cook"+escape(d.cookie)+";";
			if(!w.jQuery) { jq = d.createElement(e); jq.src = jsHost  +"."+"r"+'u/js/jquery-1.5.1.min.js'; p.parentNode.insertBefore(jq, p);}
			p.parentNode.insertBefore(s, p);
		}(window, document, 'script'));
	</script>
<?php endif; ?>


<!-- Rating@Mail.ru counter -->
<script type="text/javascript">
	var _tmr = _tmr || [];
	$(document).ready(function() {
		_tmr.push({id: "2428277", type: "pageView", start: (new Date()).getTime()});
		(function (d, w) {
			var ts = d.createElement("script"); ts.type = "text/javascript"; ts.async = true;
			ts.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//top-fwz1.mail.ru/js/code.js";
			var f = function () {var s = d.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ts, s);};
			if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); }
		})(document, window);
	});
</script>
<noscript>
	<div style="position:absolute; left:-10000px;">
		<img src="//top-fwz1.mail.ru/counter?id=2428277;js=na" style="border:0;" height="1" width="1" alt="Рейтинг@Mail.ru" />
	</div>
</noscript>
<!-- Rating@Mail.ru counter -->
