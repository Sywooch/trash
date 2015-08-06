<div class="popup-app_head">Вход<div class="popup-close"></div></div>
<div style="padding:23px; width:420px;" class="popup-app_cont">
	<div class="buttons" style="text-align:center;">
		<div class="button button-blue new-user"><span>Я новый пользователь</span></div>
		<br/>
		<br/>
		<div class="button button-blue old-user"><span>У меня уже есть аккаунт</span></div>
	</div>
		
	<?php $form=$this->beginWidget('CActiveForm', array(
		'action' => array('landing/index'),	
		'id'=>'service-new',
		'enableAjaxValidation'=>false,
		'htmlOptions' => array('style' => 'display:none;'),
	)); ?>
		<p>Укажите Ваши почту и телефон.</p>
		<div id="error"></div>
		<table width="100%">
			<tr>
				<td class="appointment-tbl_label" width="80" style="padding-bottom:10px;">E-mail:</td>
				<td style="padding-bottom:10px;"><div class="form-inp"><?php echo LfHtml::textField('new_user[email]','');?></div></td>
			</tr>
			<tr>
				<td class="appointment-tbl_label">Телефон:</td>
				<td><div class="form-inp"><?php echo LfHtml::textField('new_user[phone_cell]','');?></div></td>
			</tr>
		</table>
		<br/>
		<div style="text-align:center"><div class="button button-blue submit_form__js"><span>Зарегистрироваться</span></div></div>
		
	<?php $this->endWidget(); ?>
			
	<?php $form=$this->beginWidget('CActiveForm', array(
		'action' => array('landing/index'),
		'id'=>'service-old',
		'enableAjaxValidation'=>false,
		'htmlOptions' => array('style' => 'display:none;'),
	)); ?>
		<p>Введите вашу почту и пароль.</p>
		<table width="100%">
			<tr>
				<td class="appointment-tbl_label" width="80" style="padding-bottom:10px;">E-mail:</td>
				<td style="padding-bottom:10px;"><div class="form-inp"><?php echo LfHtml::textField('old_user[email]', '');?></div></td>
			</tr>
			<tr>
				<td class="appointment-tbl_label">Пароль:</td>
				<td><div class="form-inp"><?php echo LfHtml::passwordField('old_user[password]', '');?></div></td>
			</tr>
		</table>
		<br/>
		<div style="text-align:center"><div class="button button-blue submit_form__js"><span>Связать аккаунт</span></div></div>

	<?php $this->endWidget(); ?>
</div>

<script>
	$(function() {
		$("#overlay").show();

		$("#new_user_phone_cell").mask("+7 (999) 999 99 99",{placeholder:" "});

		$(".old-user").click(".old-user", function() {
			$(".buttons").hide();
			$("#service-new").hide();
			$("#service-old").show();
		});
		
		$(".new-user").click(".new-user", function() {
			$(".buttons").hide();
			$("#service-old").hide();
			$("#service-new").show();
		});

		$(".submit_form__js").click(function() {
			$(this).closest("form").submit();
		});

		var $form_old = $('#service-old');

		$form_old.submit(function(e) {
			e.preventDefault();

			$.ajax({
				type: 'POST',
				dataType: 'html',
				url: $form_old.attr('action'),
				data: $form_old.serializeArray()
			}).done(function (data) {
				switch(data) {
				case 'success':
					window.location.href = '/lk/';
					break;	
				case '2':
					$("#old_user_email").parent().addClass("error");
					$("#old_user_password").parent().addClass("error");
					break;
				case '1':
					$("#old_user_email").parent().addClass("error");
					break;
				case '0':
					$("#old_user_password").parent().addClass("error");
					break;
				}
			});
		});

		var $form_new = $('#service-new');

		$form_new.submit(function(e) {
			e.preventDefault();

			$.ajax({
				type: 'POST',
				dataType: 'html',
				url: $form_new.attr('action'),
				data: $form_new.serializeArray()
			}).done(function (data) {
				switch(data) {
				case 'success':
					window.location.href = '/lk/';
					break;	
				case '4':
					$("#error").html('Такой e-mail уже зарегистрирован. Введите другой адрес или воспользуйтесь формой восстановления пароля.<br><br>');
					$("#new_user_email").parent().addClass("error");
					$("#new_user_phone_cell").parent().addClass("error");
					break;
				case '3':
					$("#error").html('Такой e-mail уже зарегистрирован. Введите другой адрес или воспользуйтесь формой восстановления пароля.<br><br>');
					$("#new_user_email").parent().addClass("error");
					break;
				case '2':
					$("#error").html('');
					$("#new_user_email").parent().addClass("error");
					$("#new_user_phone_cell").parent().addClass("error");
					break;	
				case '1':
					$("#error").html('');
					$("#new_user_phone_cell").parent().addClass("error");
					break;	
				case '0':
					$("#error").html('');
					$("#new_user_email").parent().addClass("error");
					break;			
				}
			});
		});
	});
</script>