<?php
	use dfs\docdoc\components\Version;

	define('ROOT_PATH', realpath(__DIR__ . "/../.."));
	require ROOT_PATH . '/common/include/common.php';

	header('Content-Type: text/html; charset=utf-8');
	$server = $_SERVER['HTTP_HOST'];
	$sParts = explode(".", $server);
	$serverName = current($sParts);
	$version = new Version;

	$mobileVersionFile = ROOT_PATH . '/../docdoc_m/version.txt';
	$mVersion = file_exists($mobileVersionFile)
		? 'v' . file_get_contents($mobileVersionFile)
		: 'not installed';

	$bookingFolder = ROOT_PATH . '/../docdoc_booking';
	$bVersion = is_dir($bookingFolder)
		? 'installed'
		: 'not installed';
?>
<!doctype html>
<html>
<head lang="ru">
	<title>DocDoc startup page</title>
	<meta charset="utf-8"/>
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
<!--	<link href="style.css" rel="stylesheet" media="all">-->
	<link rel="icon"
		  type="image/png"
		  href="i/logo.png">
	<script src="http://yandex.st/jquery/2.0.3/jquery.min.js"></script>
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
	<style>
		* {font-size: 110%;}
		body {padding: 0 2em;}
		h1{color: #29aaac;}
		.add{
			background-color: #e8f6f6;
			font-size: 75%;
		}
		.ico {
			width: 2ex;
			height: 2ex;
		}
	</style>
</head>
<body>

<h1>
	<img src="i/logo.png" width="32" height="32">
	<strong>DocDoc</strong> startup page</h1>

<div class="row">
	<div class="col-md-4">
		<h2>Sites</h2>
		<ul>
			<li>
				<a href="<?="http://front.{$server}"?>">
					<img src="//<?="front.{$server}"?>/img/common/touch-icon-iphone-retina-precomposed.png" class="ico" >
					Front
				</a> |
				<a href="<?="https://front.{$server}"?>">Secure</a> |
				<a href="<?="http://spb.front.{$server}"?>">spb</a> |
				<a href="<?="https://front.{$server}"?>/lk">ЛК</a> |
				<a href="<?="https://front.{$server}"?>/pk">ПК</a>
				<br/>
				<span class="add"><b>[</b>
					<a href="https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=786437">wiki</a> |
					<a href="https://docdoc.atlassian.net/secure/RapidBoard.jspa?rapidView=1">board</a> |
					<a href="https://bitbucket.org/dfsru/docdoc">git</a>
					<b>]</b>
				</span>

			</li>
			<li>
				<a href="<?="http://back.{$server}"?>">
					BackOffice
				</a> | <a href="<?="https://back.{$server}"?>">Secure</a>
			</li>
			<li>
				<a href="<?="http://diagnostica.{$server}"?>">Diagnostic</a> | <a href="<?="http://spb.diagnostica.{$server}"?>">spb</a>
			</li>
			<li>
				<a href="<?="http://m.front.{$server}"?>">Mobile</a> <sup><?=$mVersion?></sup> |
				<a href="<?="http://m.spb.front.{$server}"?>">spb</a>
				<br/>
				<span class="add"><b>[</b>
					<a href="https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=18448525">wiki</a> |
					<a href="https://docdoc.atlassian.net/secure/RapidBoard.jspa?rapidView=7">board</a> |
					<a href="https://bitbucket.org/dfsru/docdoc_m">git</a>
					<b>]</b>
				</span>
			</li>
			<li>
				Booking: <sup><?=$bVersion?></sup>
				<br/>
				<span class="add"><b>[</b>
					<a href="https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=10453015">wiki</a> |
					<a href="https://docdoc.atlassian.net/secure/RapidBoard.jspa?rapidView=11">board</a> |
					<a href="https://bitbucket.org/dfsru/docdoc_booking">git</a>
					<b>]</b>
				</span>
			</li>
		</ul>
	</div>

	<div class="col-md-4">
		<h2>Tools</h2>

		<ul>
			<li><a href="https://docdoc.atlassian.net/wiki/">
					<img src="https://docdoc.atlassian.net/wiki/s/en_GB/5758/9733a9ad7594abe230aa8a69e0a962467811a6f4.2/_/favicon.ico" class="ico" >
					Confluence
				</a>
				<span class="add"><b>[</b>
					<a href="https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=786437">DD</a> |
					<a href="https://docdoc.atlassian.net/wiki/display/dev/Development">dev</a>
					<b>]</b>
				</span>

			</li>
			<li>
				<a href="https://docdoc.atlassian.net/">
					<img src="https://docdoc.atlassian.net/s/en_USmag1yc/64005/14/_/favicon.ico" class="ico" >
					JIRA
				</a>
			</li>
			<li><a href="https://bitbucket.org/dfsru">Bitbucket</a></li>
			<li><a href="https://www.browserstack.com/">BrowserStack</a></li>
			<li>
				<a href="https://jenkins.docdoc.pro/">Jenkins</a>
				<span class="add"><b>[</b>
					<a href="https://jenkins.docdoc.pro/view/Dev/job/DocDoc_<?=$serverName?>/build?delay=0sec"">build</a>
					<b>]</b>
				</span>

			</li>
			<li><a href="https://rpm.newrelic.com/accounts/720140/applications/4761019">
					<img src="//newrelic.com/favicon.ico" class="ico" />
					New Relic
				</a></li>
			<li><a href="https://logentries.com/app">
					<img src="/i/logentries-logo.png" class="ico" />
					LogEntries
				</a></li>

			<li><a href="https://dash.docdoc.pro">
					DashBoard
				</a></li>

			<li><a href="http://ast.docdoc.pro/queue-stats/">
				Asternic
			</a></li>

			<li><a href="https://tl.docdoc.pro/">TestLink</a></li>
			<li>

				<a href="http://www.webpagetest.org/">
					<img src="//www.webpagetest.org/images/favicon.ico" class="ico" />
					<b>WEB</b>PAGETEST</a></li>
			<li><a style="font-size: 90%; color: #808080;" href="https://docdoc.megaplan.ru/">Megaplan</a></li>

		</ul>
	</div>

	<div class="col-md-4">
		<h2>Info</h2>
		<p>
			Version: <b><?=$version->getCurrent()?></b>
		</p>
		<img src="i/release_logo/<?=$version->getImageUrl()?>" />
	</div>
</div>

<div class="row">
	<div class="col-md-4">
		<h2>Helpful</h2>

		<ul>
			<li><a href="https://docdoc.atlassian.net/wiki/pages/viewpage.action?pageId=2162747">Контакты сотрудников</a>
			</li>
		</ul>
	</div>

</div>

</body>
</html>

