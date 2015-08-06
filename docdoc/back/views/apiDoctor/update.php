<?php
/**
 * @var dfs\docdoc\back\controllers\ApiDoctorController $this
 * @var dfs\docdoc\models\ApiDoctorModel                $model
 * @var CActiveForm                                     $form
 */

$this->breadcrumbs = array(
	'Сопоставление врачей, загруженных из API клиник' => array('index'),
	'Редактирование',
);

Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/jquery-ui/jquery-ui.min.js');
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . '/js/jquery-ui/themes/smoothness/jquery-ui.min.css');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/doctor_autocomplete.js');
?>

<h1>Редактирование связи №<?php echo $model->id; ?></h1>

<div class="form">

	<?php $form = $this->beginWidget(
		'CActiveForm',
		array(
			'id'                   => 'apiDoctor-form',
			'enableAjaxValidation' => false,
		)
	); ?>

	<?php echo $form->errorSummary($model); ?>

	<table class="api_doctor-table">
		<tr>
			<td><?php echo $model->getAttributeLabel("name"); ?></td>
			<td><?php echo $model->name; ?></td>
		</tr>
		<tr>
			<td><?php echo $model->getAttributeLabel("api_clinic_id"); ?></td>
			<td><?php echo $model->getApiClinicName(); ?></td>
		</tr>
		<tr>
			<td><?php echo $model->getAttributeLabel("enabled"); ?></td>
			<td><?php echo CHtml::checkBox('enabled', $model->enabled); ?></td>
		</tr>
	</table>

	<?php if ($model->doctor) { ?>
		<p>
			Наш врач: <?php if($model->doctor) {echo $model->doctor->name;} else { echo 'Не найден(Связь битая)';} ?>
			[ <a href="<?php echo $this->createUrl(
				"apiDoctor/unmerged",
				["id" => $model->id]
			) ?>">удалить связь</a> ]
		</p>
	<?php } ?>

	<div class="row">
		Введите ФИО доктора:
		<?php echo CHtml::textField(
			"findDoctorName",
			null,
			array("size" => 64, "class" => "api_doctor-autocomplete", "data-clinicid" => $model->api_clinic_id)
		); ?>
		<?php echo CHtml::hiddenField('doctorId'); ?>
		<?php echo CHtml::hiddenField('clinicId'); ?>
	</div>

	<div class="row">
		<?php echo CHtml::submitButton('Сохранить'); ?>
	</div>

	<?php $this->endWidget(); ?>

</div>
