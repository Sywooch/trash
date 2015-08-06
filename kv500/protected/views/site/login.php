<div class="login-enter">

<?php
$this->pageTitle=Yii::app()->name . ' - Вход';
//$this->breadcrumbs=array(
//	'Вход',
//);
?>

<h1>Вход</h1>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>

	<div class="row">
		<?php echo $form->textField($model,'username', array("class" => "login-form", "placeholder" => "Логин")); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="row">
		<?php echo $form->passwordField($model,'password', array("class" => "login-form", "placeholder" => "Пароль")); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>
	<p>&nbsp;</p>

	<div class="row rememberMe">
		<?php echo $form->checkBox($model,'rememberMe'); ?>
		<?php echo $form->label($model,'rememberMe', array("style" => "display: inline-block;")); ?>
		<?php echo $form->error($model,'rememberMe'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Войти', array("class" => "btn")); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->

</div>