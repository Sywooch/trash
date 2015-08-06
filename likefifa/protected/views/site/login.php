<?php
/**
 * @var SiteController $this
 * @var LoginForm      $model
 */

$this->pageTitle = Yii::app()->name . ' - Вход';
$this->breadcrumbs = array(
	'Вход',
);
?>

<div class="login-box">

	<div class="header">
		Авторизация
	</div>

	<?php
	/** @var TbActiveForm $form */
	$form = $this->beginWidget(
		'booster.widgets.TbActiveForm',
		array(
			'id'                     => 'login-form',
			'type'                   => 'horizontal',
			'enableClientValidation' => true,
			'clientOptions'          => array(
				'validateOnSubmit' => true,
			),
			'focus'                  => [$model, 'username'],
		)
	);
	?>

	<fieldset class="col-sm-12">


		<div class="form-group">
			<div class="controls row">
				<div class="input-group col-sm-12">
					<?php echo $form->textField(
						$model,
						'username',
						[
							'class'       => 'form-control',
							'placeholder' => $model->getAttributeLabel('username')
						]
					); ?>
					<span class="input-group-addon"><i class="fa fa-user"></i></span>
				</div>
				<?php echo $form->error($model, 'username'); ?>
			</div>
		</div>

		<div class="form-group">
			<div class="controls row">
				<div class="input-group col-sm-12">
					<?php echo $form->passwordField(
						$model,
						'password',
						[
							'class'       => 'form-control',
							'placeholder' => $model->getAttributeLabel('password')
						]
					); ?>
					<span class="input-group-addon"><i class="fa fa-key"></i></span>
				</div>
				<?php echo $form->error($model, 'password'); ?>
			</div>
		</div>

		<div class="confirm">
			<?php echo $form->checkBox($model, 'rememberMe'); ?>
			<?php echo $form->label($model, 'rememberMe'); ?>
		</div>

		<div class="row">

			<button type="submit" class="btn btn-lg btn-primary col-xs-12">Войти</button>

		</div>

	</fieldset>

	<?php $this->endWidget(); ?>

	<div class="clearfix"></div>

</div>
