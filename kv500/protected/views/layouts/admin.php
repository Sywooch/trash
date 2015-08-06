<!DOCTYPE HTML>
<html>
<head>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<title>Администрирование</title>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,800,300,700&subset=latin,cyrillic-ext'
		  rel='stylesheet' type='text/css'>

	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
	<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css"/>

	<link href="<?php echo Yii::app()->baseUrl; ?>/js/select2-3.4.6/select2.css" rel="stylesheet"/>
	<script src="<?php echo Yii::app()->baseUrl; ?>/js/select2-3.4.6/select2.js"></script>
	<script src="<?php echo Yii::app()->baseUrl; ?>/js/jquery.maskedinput.js"></script>

	<script src="<?php echo Yii::app()->baseUrl; ?>/js/tinymce/tinymce.min.js"></script>

	<script src="<?php echo Yii::app()->baseUrl; ?>/js/common.js"></script>
	<link rel="stylesheet" href="<?php echo Yii::app()->baseUrl; ?>/css/style.css"/>
</head>
<body>

<div id="admin-container">

	<?php if (!Yii::app()->user->isGuest) { ?>
		<div class="admin-title">Панель управления <a href="/logout">(Выйти)</a></div>

		<div id="mainmenu">
			<?php $this->widget(
				'zii.widgets.CMenu',
				array(
					'items' => array(
						array(
							'label'   => 'Пользователи',
							'url'     => array('/admin/user'),
							'visible' => !Yii::app()->user->isGuest
						),
						array(
							'label'   => 'Платежи',
							'url'     => array('/admin/payment'),
							'visible' => !Yii::app()->user->isGuest
						),
						array(
							'label'   => 'Операции',
							'url'     => array('/admin/operation'),
							'visible' => !Yii::app()->user->isGuest
						),
						array(
							'label'   => 'Заявки на снятие денег' . PaymentMoney::model()->getCountNotRead(),
							'url'     => array('/admin/paymentMoney'),
							'visible' => !Yii::app()->user->isGuest
						),
						array(
							'label'   => 'Новости',
							'url'     => array('/admin/news'),
							'visible' => !Yii::app()->user->isGuest
						),
						array(
							'label'   => 'Вопрос-ответ',
							'url'     => array('/admin/faq'),
							'visible' => !Yii::app()->user->isGuest
						),
						array(
							'label'   => 'Предложения' . Offer::model()->getCountNotRead(),
							'url'     => array('/admin/offer'),
							'visible' => !Yii::app()->user->isGuest
						),
						array(
							'label'   => 'Обратная связь' . Contacts::model()->getCountNotRead(),
							'url'     => array('/admin/contacts'),
							'visible' => !Yii::app()->user->isGuest
						),
						array(
							'label'   => 'Текстовые страницы',
							'url'     => array('/admin/text'),
							'visible' => !Yii::app()->user->isGuest
						),
					),
				)
			); ?>
			<div class="clear"></div>
		</div>
	<?php } ?>

	<?php if (isset($this->breadcrumbs)): ?>
		<?php $this->widget(
			'zii.widgets.CBreadcrumbs',
			array(
				'homeLink' => false,
				'links'    => $this->breadcrumbs,
			)
		); ?>
	<?php endif ?>


	<?php echo $content; ?>


</div>


</body>
</html>