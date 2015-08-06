<?php
/**
 * @var BackendController $this
 * @var string            $content
 */

use likefifa\models\AdminModel;

$adminModel = AdminModel::model();
$baseUrl = Yii::app()->getBaseUrl();
$themeUrl = Yii::app()->theme->getBaseUrl();

Yii::app()->clientScript
	->registerCoreScript('jquery')
	->registerCoreScript('jquery.ui')
	->registerScriptFile(CHtml::asset(dirname(__FILE__) . '/assets/admin/form.doctor.js'), CClientScript::POS_END)

	->registerScriptFile(Yii::app()->request->hostInfo . ':' . Yii::app()->elephant->sslPort . '/socket.io/socket.io.js', CClientScript::POS_END)

	->registerCssFile($baseUrl . '/css/themes/apple/style.css')
	->registerCssFile('//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css')

	->registerCssFile($themeUrl . '/css/style.min.css')
	->registerCssFile($themeUrl . '/css/retina.min.css')
	->registerCssFile($themeUrl . '/css/print.css', 'print')

	->registerCssFile($baseUrl . '/css/global.css')
	->registerCssFile($baseUrl . '/css/admin/style.css');

Booster::getBooster()->registerPackage('select2');
Booster::getBooster()->registerPackage('datepicker');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="language" content="ru"/>

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>

	<script type="text/javascript">
		var homeUrl = '<?php echo Yii::app()->homeUrl; ?>';
	</script>

	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo $baseUrl; ?>/css/admin/ie.css"
		  media="screen, projection"/>
	<![endif]-->

	<script type="text/javascript">
		var notificationPort = <?=CJavaScript::encode(Yii::app()->elephant->sslPort)?>;
	</script>
</head>

<body>
<?php if (!Yii::app()->user->isGuest): ?>
<header class="navbar">
	<div class="container">
		<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".sidebar-nav.nav-collapse">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<a id="main-menu-toggle" class="hidden-xs open"><i class="fa fa-bars"></i></a>
		<a class="navbar-brand col-md-2 col-sm-1 col-xs-2" href="/admin" style="padding-left: 47px;width: auto;">
			<span>Likefifa</span>
			<em style="font-size: 12px;margin-right: 10px;">(текущая версия: <?php echo RELEASE_MEDIA ?>)</em>
		</a>

		<!-- start: Header Menu -->
		<div class="nav-no-collapse header-nav">
			<ul class="nav navbar-nav pull-right">
				<?php if (!Yii::app()->user->isGuest): ?>
					<li class="dropdown hidden-xs">
						<a href="<?php echo $this->createUrl(
							'/admin/appointment',
							['LfAppointmentAdminFilter[is_viewed]' => 0]
						) ?>" class="btn" title="Новые заявки" data-toggle="tooltip" data-placement="bottom">
							<i class="fa fa-file"></i>
							<span class="number">
								<?php echo Yii::app()->db->createCommand(
									"select count(id) from lf_appointment where is_viewed = 0"
								)->queryScalar(); ?>
							</span>
						</a>
					</li>

					<li class="dropdown hidden-xs">
						<a href="<?php echo $this->createUrl('/admin/opinion/') ?>" class="btn" title="Новые отзывы"
						   data-toggle="tooltip" data-placement="bottom">
							<i class="fa fa-wechat"></i>
							<span class="number">
								<?php echo Yii::app()->db->createCommand(
									"select count(id) from lf_opinion where allowed = 0"
								)->queryScalar(); ?>
							</span>
						</a>
					</li>

					<?php $adminModel = AdminModel::model()->getModel(); ?>
					<!-- start: User Dropdown -->
					<li class="dropdown">
						<a class="btn account dropdown-toggle" data-toggle="dropdown"
						   href="<?php echo $this->createUrl('/admin/index') ?>">
							<span class="name curren-user"><?php echo $adminModel->name; ?></span>
						</a>
						<ul class="dropdown-menu">
							<li><a href="<?php echo Yii::app()->createUrl("site/logout"); ?>"><i class="fa fa-off"></i>
									Выйти</a></li>
						</ul>
					</li>
				<?php endif; ?>
				<!-- end: User Dropdown -->
			</ul>
		</div>
		<!-- end: Header Menu -->

	</div>
</header>

<div class="container">
	<div class="row">
		<!-- start: Main Menu -->
		<div id="sidebar-left" class="col-lg-2 col-sm-1 minified">
			<?php $this->widget(
				'likefifa\components\system\admin\YbNavbar',
				[
					'collapse' => true,
					'brand'    => false,
					'fixed'    => false,
					'fluid'    => false,
					'items'    => [
						[
							'class'       => 'likefifa\components\system\admin\YbMenu',
							'encodeLabel' => false,
							'htmlOptions' => ['class' => 'main-menu'],
							'type'        => 'list',
							'items'       => $adminModel->getItemsForBoMenu(),
						]
					],
				]
			); ?>
			<a class="full visible-md visible-lg" id="main-menu-min" href="#">
				<i class="fa fa-angle-double-left"></i>
			</a>
		</div>

		<div id="content" class="col-lg-10 col-sm-11 sidebar-minified">
			<?php if (isset($this->breadcrumbs)): ?>
				<?php $this->widget(
					'booster.widgets.TbBreadcrumbs',
					[
						'homeLink' => false,
						'links'    => array_merge(
							['Главная' => Yii::app()->baseUrl . '/admin/'],
							$this->breadcrumbs
						),
					]
				); ?>
			<?php endif ?>

			<div class="col-lg-12">
				<?php echo $content; ?>
			</div>
		</div>
	</div>
</div>

<div id="modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true"></div>
<?php else: ?>
	<!-- Login page -->
	<div class="container">
		<div class="row">
			<div id="content" class="col-sm-12 full">
				<?php echo $content; ?>
			</div>
		</div>
	</div>
	<!-- /Login page -->
<?php endif; ?>


<script type="text/javascript" src="<?php echo Yii::app()->homeUrl . 'js/jquery.jstree.js' ?>"></script>
<script type="text/javascript" src="<?php echo Yii::app()->homeUrl . 'js/jquery.MultiFile.js' ?>"></script>
<script type="text/javascript" src="<?php echo Yii::app()->homeUrl . 'js/jquery.maskedinput-1.3.min.js' ?>"></script>
<script type="text/javascript" src="<?php echo Yii::app()->homeUrl . 'js/admin/admin.js' ?>"></script>
<script type="text/javascript" src="<?php echo $baseUrl; ?>/js/global.js"></script>

<!-- page scripts -->
<!--<script src="<?php /*echo $themeUrl*/ ?>/js/jquery-ui-1.10.3.custom.min.js"></script>-->
<script src="<?php echo $themeUrl ?>/js/jquery.ui.touch-punch.min.js"></script>
<script src="<?php echo $themeUrl ?>/js/jquery.sparkline.min.js"></script>
<!--<script src="<?php /*echo $themeUrl*/ ?>/js/fullcalendar.min.js"></script>-->
<!--[if lte IE 8]>
<script language="javascript" type="text/javascript" src="<?php echo $themeUrl?>/js/excanvas.min.js"></script>
<![endif]-->
<script src="<?php echo $themeUrl ?>/js/jquery.autosize.min.js"></script>
<script src="<?php echo $themeUrl ?>/js/jquery.placeholder.min.js"></script>
<script src="<?php echo $themeUrl ?>/js/moment.min.js"></script>
<script src="<?php echo $themeUrl ?>/js/jquery.dataTables.min.js"></script>
<script src="<?php echo $themeUrl ?>/js/dataTables.bootstrap.min.js"></script>

<!-- theme scripts -->
<script src="<?php echo $themeUrl ?>/js/custom.min.js"></script>
<script src="<?php echo $themeUrl ?>/js/core.min.js"></script>

<script src="<?php echo $themeUrl ?>/js/jquery.gritter.min.js"></script>


<!-- end: JavaScript-->
</body>
</html>