<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">

</head>
<body>
<a href="#" onclick="loadWidget(this);" data='<?=$config?>' id="testWidget">Загрузка виджета из аттрибута data</a>

<div id="widget"></div>

<script type="text/javascript">
	(function (w, d) {
		var proto = d.location.protocol === "https:" ? "https:" : "http:";
		w.DdFeedAsyncInit = function () {};

		(function (d) {
			var js, id = 'dd-feed', ref = d.getElementsByTagName('script')[0];
			if (d.getElementById(id)) {
				return;
			}
			js = d.createElement('script');
			js.id = id;
			js.async = true;
			js.src = proto + '//<?=$host;?>/widget/js';
			ref.parentNode.insertBefore(js, ref);
		}(d));
	}(window, window.document));

	function loadWidget(el)
	{
		var data = el.getAttribute("data");
		var config = eval("("+data+")");
		DdWidget(config);
	}

</script>

</body>


</html>