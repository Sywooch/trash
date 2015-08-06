<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=1024"/>

	<title>Docdoc.ru</title>

	<link href="/img/common/favicon.ico" rel="icon" type="image/x-icon">

	<link rel="stylesheet" href="/css/normalize.css">
	<link rel="stylesheet" href="/css/print.css?up">
</head>
<body>
	<div class="l-print_height">
		<div class="l-wrap_print">
			<div class="b-header_wrap">
				<img src="/img/print/logo.png" width="398" height="146" class="b-header_logo" />
				<img src="/img/print/i-doctor.png" width="184" height="146" class="b-header_doctor" />
				<div class="b-header_line"></div>
			</div>
			<?=$content?>
		</div>
	</div>
	<div class="b-footer_print">
		<div class="b-footer_print__line"></div>
		<div class="b-footer_print__txt">
			Спасибо за то, что воспользовались<br>
			нашим сервисом! <span>Будьте здоровы!</span>
		</div>
		<div class="b-footer_print__line"></div>
	</div>
	<script>
		window.print();
	</script>
</body>
</html>
