<?php
use likefifa\models\AdminModel;

/**
 * @var AdminsController $this
 * @var AdminModel       $model
 */

$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'      => $this->pageTitle,
		'headerIcon' => 'fa fa-user',
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
			<?php echo $form->textFieldGroup($model, 'name') ?>
			<?php echo $form->textFieldGroup($model, 'login') ?>

			<?php if ($model->isNewRecord): ?>
				<?php echo $form->passwordFieldGroup($model, 'password') ?>
			<?php endif; ?>

			<?php echo $form->dropDownListGroup(
				$model,
				'group_id',
				['widgetOptions' => ['data' => $model->groupList]]
			) ?>

			<br/>
			<?php if (!$model->isNewRecord): ?>
				<?php echo $form->passwordFieldGroup($model, 'newPassword') ?>
			<?php endif; ?>
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
