<?php
/**
 * @var SeoTextController $this
 * @var LfSeoText         $model
 */

$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'      => $this->pageTitle,
		'headerIcon' => 'fa fa-leaf',
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
		<?php echo $form->checkboxGroup($model, 'disabled') ?>
		<?php echo $form->checkboxGroup($model, 'for_gallery') ?>

		<?php echo $form->dropDownListGroup(
			$model,
			'type',
			['widgetOptions' => ['data' => $model->getTypeListItems()]]
		) ?>

		<?php echo $form->textFieldGroup($model, 'name') ?>
		<?php echo $form->textFieldGroup($model, 'page_title') ?>
		<?php echo $form->textAreaGroup($model, 'meta_keywords') ?>
		<?php echo $form->textAreaGroup($model, 'meta_description') ?>
		<?php echo $form->ckEditorGroup($model, 'text') ?>

		<?php echo $form->dropDownListGroup(
			$model,
			'sector_id',
			[
				'widgetOptions' => [
					'data' => Sector::model()->getListItems(),
					'htmlOptions' => [
						'empty' => 'Направление не выбрано'
					]
				]
			]
		) ?>

		<?php echo $form->select2Group(
			$model,
			'specialization_id',
			[
				'widgetOptions' => [
					'data'    => LfSpecialization::model()->getListItems(),
					'options' => [
						'placeholder' => 'Специализация не выбрана',
					],
				],
			]
		); ?>

		<?php echo $form->select2Group(
			$model,
			'service_id',
			[
				'widgetOptions' => [
					'data'    => LfService::model()->getListItems(),
					'options' => [
						'placeholder' => 'Услуга не выбрана',
					],
				],
			]
		); ?>

		<?php echo $form->textFieldGroup($model, 'page') ?>
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
?>