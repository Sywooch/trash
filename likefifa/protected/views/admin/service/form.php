<?php
/**
 * @var ServiceController $this
 * @var LfService         $model
 */

$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'      => $this->pageTitle,
		'headerIcon' => 'fa fa-cubes',
	]
);

/** @var TbActiveForm $form */
$form = $this->beginWidget(
	'likefifa\components\system\admin\YbActiveForm',
	[
		'id'          => 'verticalForm',
		'htmlOptions' => ['class' => 'container-fluid'],
	]
);
?>
<div class="row">
	<div class="col-md-6">
		<?php echo $form->dropDownListGroup(
			$model,
			'specialization_id',
			['widgetOptions' => ['data' => LfSpecialization::model()->getListItems()]]
		) ?>
		<?php echo $form->textFieldGroup($model, 'name') ?>
		<?php echo $form->textFieldGroup($model, 'rewrite_name') ?>
		<?php echo $form->textFieldGroup($model, 'genitive_name') ?>
		<?php echo $form->textFieldGroup($model, 'weight') ?>

		<?php echo $form->checkboxGroup($model, 'price_from') ?>
		<?php echo $form->textFieldGroup($model, 'unit') ?>
	</div>
</div>

<?php $this->widget(
	'booster.widgets.TbButton',
	[
		'buttonType' => 'submit',
		'context'    => 'primary',
		'label'      => $model->isNewRecord ? 'Создать' : 'Сохранить'
	]
); ?>
<?php
$this->endWidget();

$this->endWidget();
