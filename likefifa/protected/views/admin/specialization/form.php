<?php
/**
 * @var SpecializationController $this
 * @var LfSpecialization         $model
 */

$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'      => $this->pageTitle,
		'headerIcon' => 'fa fa-copy',
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
			'sector_id',
			[
				'widgetOptions' => [
					'data'        => Sector::model()->getListItems(),
					'htmlOptions' => [
						'empty' => 'Направление не выбрано',
					]
				]
			]
		) ?>

		<?php echo $form->checkboxListGroup(
			$model,
			'group_id',
			[
				'widgetOptions' => [
					'data' => LfGroup::model()->getListItems(),
				]
			]
		) ?>

		<?php if (!$model->isNewRecord): ?>
			<?php echo $form->dropDownListGroup(
				$model,
				'binded_service_id',
				[
					'widgetOptions' => [
						'data'        => $model->getServicesListItems(),
						'htmlOptions' => [
							'empty' => 'Услуга не выбрана',
						]
					]
				]
			) ?>
		<?php endif; ?>

		<?php echo $form->textFieldGroup($model, 'name'); ?>
		<?php echo $form->textFieldGroup($model, 'genitive_name'); ?>
		<?php echo $form->textFieldGroup($model, 'profession'); ?>
		<?php echo $form->textFieldGroup($model, 'weight'); ?>
		<?php echo $form->textFieldGroup($model, 'sort'); ?>
		<?php echo $form->textFieldGroup($model, 'rewrite_name'); ?>
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
