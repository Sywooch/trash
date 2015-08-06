<?php
/**
 * @var MasterController $this
 * @var LfMaster         $model
 * @var MasterMailForm   $mailForm
 */

use likefifa\models\forms\MasterMailForm;

$this->widget(
	'booster.widgets.TbTabs',
	array(
		'type' => 'tabs',
		'tabs' => [
			[
				'id'      => 'tab-main',
				'label'   => 'Основные настройки',
				'content' => $this->renderPartial('_main_form', compact('model'), true),
				'active'  => true,
			],
			[
				'id'      => 'tab-works',
				'label'   => 'Работы',
				'content' => $this->renderPartial('_works', compact('model'), true),
				'visible' => !$model->isNewRecord,
			],
			[
				'id'      => 'tab-email',
				'label'   => 'Отправить письмо',
				'content' => $this->renderPartial('_send_email', compact('model', 'mailForm'), true),
				'visible' => !$model->isNewRecord,
			]
		],
	)
);

