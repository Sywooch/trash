<?php
use likefifa\models\forms\MasterMailForm;

/**
 * @var MasterController $this
 * @var LfMaster         $model
 * @var MasterMailForm   $mailForm
 */

$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'      => 'Отправка письма',
		'headerIcon' => 'fa fa-female',
	]
);

if($mailForm->success) {
	echo '<p class="text-success">Письмо успешно отправлено!</p>';
}

/** @var TbActiveForm $form */
$form = $this->beginWidget(
	'likefifa\components\system\admin\YbActiveForm',
	[
		'id'          => 'master-email-form',
		'action'      => false,
		'htmlOptions' => [
			'class'   => 'container-fluid',
			'enctype' => 'multipart/form-data',
		],
	]
);

echo $form->textFieldGroup($mailForm, 'subject');
echo $form->textAreaGroup($mailForm, 'text');

$this->widget(
	'booster.widgets.TbButton',
	array(
		'buttonType' => 'submit',
		'context'    => 'success',
		'label'      => 'Отправить письмо'
	)
);

$this->endWidget();
$this->endWidget();