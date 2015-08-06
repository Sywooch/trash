<div class="content-wrap content-pad-bottom">
	<div class="page-profile">
		<div class="prof-wrap">
			<?php $this->widget('application.components.likefifa.widgets.LfSalonLkTabsWidget', array(
				'currentTab' => 'profile',		
			)); ?>
			<div class="prof-cont">
				<?php $form=$this->beginWidget('CActiveForm', array(
					'id'=>'master-form-personal',
					'enableAjaxValidation'=>false,
					'htmlOptions' => array(	),
				)); ?>
				<?php $this->widget('application.components.likefifa.widgets.LfSalonLkMenuWidget', array(
					'actions' => $this->actions,
					'currentAction' => $this->action->id,
					'model' => $model,
				)); ?>
				<div class="prof-rht">
					<div style="width:300px;">
						<div class="prof-inp_marg">
							<div class="prof-head-inp">Введите новый пароль:</div>
							<?php echo LfHtml::activePasswordField($model,'password',array('id'=>'prof-pass')); ?>
						</div>
						<div class="prof-inp_marg">
							<div class="prof-head-inp">Повторите пароль:</div>
							<?php echo LfHtml::activePasswordField($model,'repeat_password',array('id'=>'prof-pass-repeat')); ?>
						</div>
						<span class="form-inp_check" data-check-id="show_pass" id="prof-switch_type_pass"><i id="i-check_show_pass" class="png"></i><input type="checkbox" autocomplete="off" id="inp-check_show_pass" name="show_pass" />Отображать пароль при вводе</span>
					</div>
				</div>
				<input type='hidden' name='redirect_link' value='' class='redirect_link' />
				<div class="clearfix"></div>
				<div class="prof-btn_next">
					<div class="button button-blue"><span>Сохранить</span></div>
					<?php echo CHtml::submitButton('Сохранить'); ?>
				</div>
				<?php $this->endWidget(); ?>
			</div>
		</div>
	</div>
</div>
