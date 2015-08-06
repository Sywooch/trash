<?php
/**
 * @var string $content
 */
?>
<html>
<head>
	<meta charset="utf-8">
	<title>Test</title>
	<link href="/public/css/common.css" rel="stylesheet" type="text/css">
	<script src="/public/js/common.js"></script>
</head>
<body>
<div id="header">
	<div class="container">
		<a href="/">Test</a>
	</div>
</div>
<div class="container">
	<div id="left">
		<?= $this->getTopicTree(); ?>
		<div><a href="/topic/add/">[ add topic ]</a></div>
	</div>
	<div id="content"><?= $content; ?></div>
	<div class="clear"></div>
</div>
</body>