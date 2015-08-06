<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru" id="nojs">
<head>
	<title>Реклама клиник и медицинских (диагностических) центров - DocDoc</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<script type="text/javascript">document.documentElement.id = "js"</script>
	<link rel="shortcut icon" href="/st/i/common/favicon.ico" type="image/x-icon">
	<link href="/st/css/registration/styles.css" type="text/css" rel="stylesheet" />
	<link href="/st/css/registration/registration.css" type="text/css" rel="stylesheet" />
	<script type="text/javascript" src="/st/js/registration/jquery.min.js"></script>
	<script type="text/javascript" src="/st/js/registration/jquery.maskedinput.js"></script>
	<!--[if lte IE 7]>
		<style type="text/css">
			#header .txt div {display:inline; zoom:1;}
			#content {zoom:1;}
		</style>
	<![endif]-->
	<!--[if lte IE 8]>
		<style type="text/css">
			#header .txt div, .reg-cont, .doc-speach, .form-btn {behavior: url(./js/PIE.htc);}
			#header .txt div {border-radius: 9px; box-shadow: #ccc 0px 1px 3px;}
			.reg-cont {border-radius: 5px; box-shadow: #666 0px 0px 6px;}
			.form-btn {border-radius: 6px;}
			.doc-speach {border-radius: 8px; box-shadow: #666 0px 1px 3px;}
		</style>
	<![endif]-->
</head>
<body>
	<div id="wrap">
		<div class="reg-menu_top">
			<a href="">Для клиник<i class="arr"></i></a>
		</div>
		<div id="content">
			<div id="header">
				<div class="info">
					Все диагностические центры Москвы на одном сайте
					<div class="phone">8 (495) 565-333-0</div>
				</div>
				<a href="/" id="logo"></a>
				<div class="clear"></div>
				<div class="txt"><div>Получайте новых пациентов от <span>Diagnostica.DocDoc.ru</span></div></div>
			</div>
			<div class="reg-cont">
				<div class="reg-cont_r">
					<form action="" method="POST" class="reg-form">
						<div class="reg-form_head">Отправить заявку<br/> на размещение:</div>
						<div class="item">
							<div class="h">Контактное лицо:</div>
							<div class="inp"><input type="text" name="name" /></div>
						</div>
						<div class="item">
							<div class="h">Телефон:</div>
							<div class="inp"><input type="text" name="phone" /></div>
						</div>
						<div class="item">
							<div class="h">E-mail:</div>
							<div class="inp"><input type="text" name="mail" /></div>
						</div>
						<div class="item">
							<div class="h">Название клиники:</div>
							<div class="inp"><input type="text" name="clinic" /></div>
						</div>
						<div class="form-btn">Отправить</div>
						<input type="submit" value="Отправить" class="form-sbmt" />
						<div class="clear"></div>
					</form>
					<script type="text/javascript">
						$(function() {
						
							$("input[name='phone']").mask("+7 (999) 999 99 99",{placeholder:"  "});
							
							var $inpName = $("input[name='name']");
							var $inpMail = $("input[name='mail']");
							var $inpClinic = $("input[name='clinic']");
							var $inpPhone = $("input[name='phone']");
							
							var regexpMail = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
							var regexpPhone = /^\+7 \(\d{3,3}\) \d{3,3} \d{2,2} \d{2,2}$/;
							
							$(".form-btn").click(function() {
								var lenName = $.trim($inpName.val()).length;
								var valMail = regexpMail.test($.trim($inpMail.val()));
								var lenClinic = $.trim($inpClinic.val()).length;
								var valPhone = regexpPhone.test($.trim($inpPhone.val()));
								
								if (lenName > 3 && valMail && lenClinic > 3 && valPhone) {
									$(".reg-form").submit();
								} else {
									if (lenName <= 3)
										$inpName.parents(".item").addClass("error");
									else
										$inpName.parents(".item").removeClass("error");
										
									if (!valMail)
										$inpMail.parents(".item").addClass("error");
									else
										$inpMail.parents(".item").removeClass("error");
										
									if (lenClinic <= 3)
										$inpClinic.parents(".item").addClass("error");
									else
										$inpClinic.parents(".item").removeClass("error");
										
									if (!valPhone)
										$inpPhone.parents(".item").addClass("error");
									else
										$inpPhone.parents(".item").removeClass("error");
								}
							});
							
							$(".reg-form input[type='text']").keydown(function() {
								$(this).parents(".item").removeClass("error");
							});
							
						});
					</script>
				</div>
				<div class="reg-cont_l">
					<div class="item f" style="padding-right:205px;">
						<div class="h">Новый источник пациентов</div>
						<div class="txt">Клиники, работающие с нами, ежедневно получают от 10 до 100  целевых обращений (звонков) в день на диагностические процедуры.</div>
						<i class="ico" style="right:20px; top:-3px;"></i>
					</div>
					<div class="item" style="padding-left:195px;">
						<div class="h">Партнерство по принципу «оплата за результат»</div>
						<div class="txt">Стоимость звонка пациента составляет 300 рублей. 80% наших звонков конвертируется в записи на прием. Вам больше не нужно платить за клики!</div>
						<i class="ico" style="left:45px; top:0px; background-position:0 -97px;"></i>
					</div>
					<div class="item" style="padding-right:205px;">
						<div class="h">Только целевые обращения</div>
						<div class="txt">Вы платите только за звонки продолжительностью более 30 секунд. Это исключает случайные и нецелевые обращения.</div>
						<i class="ico" style="right:28px; top:-14px; background-position:0 -238px;"></i>
					</div>
				</div>
				<div class="clear"></div>
			</div>

		</div>
	</div>
	<div id="footer">
		<div class="copy">
			<p>DocDoc.ru – поиск врачей и клиник<br/>
			Copyright © 2013</p>
		</div>
	</div>
</body>
</html>