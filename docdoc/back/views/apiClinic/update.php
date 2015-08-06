<?php
/**
 * @var dfs\docdoc\back\controllers\ApiClinicController $this
 * @var dfs\docdoc\models\ClinicModel                   $model
 * @var dfs\docdoc\models\ApiClinicModel                $clinic
 * @var CActiveForm                                     $form
 */

$this->breadcrumbs = array(
	'Сопоставление API клиник' => array('index'),
	'Редактирование',
);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/clinic_autocomplete.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/jquery-ui/jquery-ui.min.js');
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . '/js/jquery-ui/themes/smoothness/jquery-ui.min.css');

?>

<h1>Редактирование связи № <?php echo $model->id; ?></h1>

<div class="form">

	<?php $form = $this->beginWidget(
		'CActiveForm',
		array(
			'id'                   => 'apiClinic-form',
			'enableAjaxValidation' => false,
		)
	); ?>

	<?php echo $form->errorSummary($model); ?>
	<?php echo $form->errorSummary($clinic); ?>

	<div class="row">
		Клиника API: <?php echo $model->name; ?>
	</div>

	<div class="row">
		Введите название клиники:
		<?php echo CHtml::textField(
			"findDoctorName",
			null,
			array("size" => 64, "class" => "api_clinic-autocomplete")
		); ?>
		<?php echo CHtml::hiddenField('clinicId'); ?>
	</div>

	<div class="row">
		<?php echo CHtml::submitButton('Сохранить'); ?>
	</div>

	<?php $this->endWidget(); ?>

</div>
