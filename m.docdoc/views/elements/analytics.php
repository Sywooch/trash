<?php
	$params = Yii::app()->getParams();
	$mixpanel = Yii::app()->mixpanel;
?>

<?php if (!empty($params['ga-universal-id'])): ?>
	<!-- Google analytics -->
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', '<?php echo $params['ga-universal-id']; ?>', 'auto');
		ga('send', 'pageview');
	</script>
	<script type="text/javascript" src="/js/ga.js"></script>
	<!-- End Google analytics -->
<?php endif; ?>


<?php if (!empty($params['gtm-id'])): ?>
	<!-- Google Tag Manager -->
	<noscript>
		<iframe src="//www.googletagmanager.com/ns.html?id=<?php echo $params['gtm-id']; ?>" height="0" width="0" style="display:none;visibility:hidden"/>
	</noscript>
	<script>
		(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
			new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
			j=d.createElement(s),dl=l!='dataLayer'?'&amp;l='+l:'';j.async=true;j.src=
			'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','<?php echo $params['gtm-id']; ?>');
	</script>
	<!-- End Google Tag Manager -->
<?php endif; ?>


<?php if ($mixpanel): ?>
	<!-- Start Mixpanel -->
	<script type="text/javascript">
		(function(f,b){if(!b.__SV){var a,e,i,g;window.mixpanel=b;b._i=[];b.init=function(a,e,d){function f(b,h){var a=h.split(".");2==a.length&&(b=b[a[0]],h=a[1]);b[h]=function(){b.push([h].concat(Array.prototype.slice.call(arguments,0)))}}var c=b;"undefined"!==typeof d?c=b[d]=[]:d="mixpanel";c.people=c.people||[];c.toString=function(b){var a="mixpanel";"mixpanel"!==d&&(a+="."+d);b||(a+=" (stub)");return a};c.people.toString=function(){return c.toString(1)+".people (stub)"};i="disable track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config people.set people.set_once people.increment people.append people.track_charge people.clear_charges people.delete_user".split(" ");
			for(g=0;g<i.length;g++)f(c,i[g]);b._i.push([a,e,d])};b.__SV=1.2;a=f.createElement("script");a.type="text/javascript";a.async=!0;a.src="//cdn.mxpnl.com/libs/mixpanel-2.2.min.js";e=f.getElementsByTagName("script")[0];e.parentNode.insertBefore(a,e)}})(document,window.mixpanel||[]);
		mixpanel.init("<?php echo $mixpanel->getToken(); ?>");
	</script>
	<!-- End Mixpanel -->

	<script type="text/javascript" src="/js/mixpanel.js"></script>

	<!-- Mixpanel trackers -->
	<script type="text/javascript">
		window.mixpanel_pid = null;
		window.global_track = <?php echo json_encode($mixpanel->getTracks()); ?>;
	</script>
	<!-- End Mixpanel trackers -->
<?php endif; ?>

<?php if (Yii::app()->params["env"] == "production") { ?>
	<!-- Yandex.Metrika counter -->
	<script type="text/javascript">
		(function (d, w, c) {
			(w[c] = w[c] || []).push(function () {
				try {
					w.yaCounter26441067 = new Ya.Metrika({
						id: 26441067,
						webvisor: true,
						clickmap: true,
						trackLinks: true,
						accurateTrackBounce: true
					});
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
				d.addEventListener("DOMContentLoaded", f, false);
			} else {
				f();
			}
		})(document, window, "yandex_metrika_callbacks");
	</script>
	<noscript>
		<div><img src="//mc.yandex.ru/watch/26441067" style="position:absolute; left:-9999px;" alt=""/></div>
	</noscript>
	<!-- /Yandex.Metrika counter -->
<?php } ?>