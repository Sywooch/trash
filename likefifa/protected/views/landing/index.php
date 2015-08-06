<div id='overlay' class='contract-window-overlay'></div>
<div class='contract-window'>
	<div class='close'></div>
	<div class='content'>
		<?php echo file_get_contents(Yii::getPathOfAlias('webroot.protected.data') . '/contract.html'); ?>
	</div>
</div>
<div class="content-wrap content-pad-bottom">
	<div>
		<p style="font-size: 140%; text-align:center; line-height:1.5;"><strong><i><span style = "color:#ca2092;">LikeFifa</span> - это новый масштабный интернет-проект,<br>который объединяет на одной площадке мастеров красоты и их клиентов.</i></strong></p>
	</div>
	<div class="col-right">
	
		<div class="city"></div>
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'form-register',
			'enableAjaxValidation'=>false,
			'htmlOptions' => array(
				'class' => 'form-wrap form-landing',
				'style' => 'display: none',
				'autocomplete' => 'off'
			),
		)); ?>
		
			<div class="form-n">Ваш профиль:</div>
			<div class="reg-type_user"><?php echo LfHtml::radioButtonList('selector', $selector, array('master' => 'мастер','salon' => 'салон')); ?></div>
			<div class="reg-auth_panel in-bl">
				<a class="auth-service odnoklassniki" href="/landing/?service=odnoklassniki" title="Войти через Одноклассники"></a>
				<a class="auth-service vkontakte" href="/landing/?service=vkontakte" title="Войти через Вконтакте"></a>
				<a class="auth-service facebook" href="/landing/?service=facebook" title="Войти через Facebook"></a>
				Войдите через<br> социальную сеть
			</div>
			<div class="master form-n">Ваши Имя и Фамилия: *</div>
			<div class="master"><?php echo LfHtml::activeTextField($master,'fullName'); ?></div>
			<div class="salon form-n">Название салона: *</div>
			<div class="salon"><?php echo LfHtml::activeTextField($salon,'name'); ?></div>
			<div class="master form-n">Ваш телефон: *</div>
			<div class="master"><?php echo LfHtml::activeTextField($master,'phone_cell'); ?></div>
			<div class="salon form-n">Ваш телефон: *</div>
			<div class="salon"><?php echo LfHtml::activeTextField($salon,'phone'); ?></div>
			<div class="master form-n">Ваш e-mail: *</div>
			<div class="master"><?php echo LfHtml::activeTextField($master,'email'); ?>
			<p><?php echo LfHtml::error($master,'email'); ?></p></div>
			<div class="salon form-n">Ваш e-mail: *</div>
			<div class="salon"><?php echo LfHtml::activeTextField($salon,'email'); ?>
			<p><?php echo LfHtml::error($salon,'email'); ?></p></div>
			<div class="master form-n">Ваш пароль: *</div>
			<div class="master"><?php echo LfHtml::activePasswordField($master,'password', array('autocomplete' => 'off')); ?></div>
			<div class="salon form-n">Ваш пароль: *</div>
			<div class="salon"><?php echo LfHtml::activePasswordField($salon,'password', array('autocomplete' => 'off')); ?></div>

			<div class='landing-contract'>
				<span class="form-inp_check form-inp_check-landing" data-check-id="contract" <?php if (!Yii::app()->getModule('payments')->isActive()): ?>zz="2" style="display: none;"<?php endif; ?>>
					<i id="i-check_contract" class="png png-contract"></i>
					<input type="checkbox" value="1" id="inp-check_contract" checked />
					Я принимаю условия <span class='contract-link'>Договора оферты</span>
				</span>	
			</div>


			<div class='button-unavailable-landing'></div>
			<div class="button button-blue" id="form-landing-reg" style="margin-top:10px;"><span style="width: 247px; text-align:center;">Зарегистрироваться</span></div>
			<div><br>* - поля, обязательные для заполнения</div>
		<?php $this->endWidget(); ?>
	</div>
	<div class="col-left">
		<div class="head">Если индустрия красоты – это Ваша работа… Добро пожаловать на LikeFifa!</div>
		<div class="txt-bl-item">
			<div class="txt-bl-head">Найди новых клиентов!</div>
			Вам больше не придется искать клиентов – теперь они найдут Вас сами. Чем больше информации Вы укажете о себе – тем больше клиентов увидят Вашу анкету.
			<div class="ico"></div>
		</div>
		<div class="txt-bl-item">
			<div class="txt-bl-head">Получи подарок!</div>
			Все мастера, зарегистрировавшиеся до <?php echo $this->getActionDate(); ?>, получают в
				подарок <?php echo Yii::app()->params["bonusMaster"]; ?> рублей на счет.
			<div class="ico ico-2"></div>
		</div>
		<div class="txt-bl-item">
			<div class="txt-bl-head">Присоединяйся и ты! </div>
			К нам уже присоединилось <div class="count-corner"></div><div class="count-people"><?php $n = (string)$this->mastersCount; $i=0; while(isset($n[$i])):?><span><?php echo $n[$i]; $i++;?></span><?php endwhile;?></div><div class="count-corner count-corner-r"></div> мастеров красоты
			и  <div class="count-corner"></div><div class="count-people"><?php $n = (string)$this->salonsCount; $i=0; while(isset($n[$i])):?><span><?php echo $n[$i]; $i++;?></span><?php endwhile;?></div><div class="count-corner count-corner-r"></div> салонов. 
			<div class="ico ico-3"></div>
		</div>
	</div>
	<div class="clearfix"></div>
</div>
