<!DOCTYPE html>
<html>
<head>
	<title>Docdoc.ru - личный кабинет клиники V1.0</title>

	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

	<link href="/images/lk/favicon.png" rel="icon" type="image/x-icon">

	<link rel="stylesheet" type="text/css" href="/css/lk/new/main.css" />
	<link rel="stylesheet" type="text/css" href="/js/jquery-ui/themes/smoothness/jquery-ui.min.css" />

	<script src="/js/jquery/jquery.min.js"></script>
	<script src="/js/jquery-ui/jquery-ui.min.js"></script>
	<script src="/js/lk/main.js"></script>
</head>
<body class="lk_simple">

	<?php echo $this->renderPartial('/elements/headerSimple'); ?>

	<div class="l-wrapper page_auth">
		<?php echo $content; ?>
	</div>

	<?php echo $this->renderPartial('/elements/footerSimple'); ?>

</body>
</html>