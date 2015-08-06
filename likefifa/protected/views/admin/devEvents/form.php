<?php

/**
 * @var AdminsController $this
 * @var DevEvent       $model
 */

use likefifa\models\DevEvent;

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
			<?php echo $form->textFieldGroup($model, 'value') ?>
			<?php
			echo $form->datePickerGroup(
				$model,
				'date',
				[
					'widgetOptions' => [
						'options'     => [
							'format' => 'yyyy-mm-dd',
						],
						'htmlOptions' => [
							'style' => 'width:130px; min-width: 130px;',
							'placeholder' => $model->getAttributeLabel('date'),
						],
					],
					'prepend'       =>
						'<i class="glyphicon glyphicon-calendar" data-toggle="tooltip" title="' .
						$model->getAttributeLabel('createdFrom') .
						'"></i>'
				]
			);
			?>
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
