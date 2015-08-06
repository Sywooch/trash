<?php
use likefifa\models\forms\LfSalonAdminFilter;

/**
 * @var LfSalonAdminFilter $model
 */

$dataProvider = $model->search();

$this->breadcrumbs = array(
	'Салоны',
);

$this->beginWidget('likefifa\components\system\admin\YbBox', ['title' => false]);
/** @var TbActiveForm $form */
$form = $this->beginWidget(
	'likefifa\components\system\admin\YbActiveForm',
	[
		'method' => 'get',
		'id'     => 'filter-form',
		'type'   => 'inline',
	]
);

echo $form->textFieldGroup(
	$model,
	'id',
	[
		'widgetOptions' => [
			'htmlOptions' => [
				'style' => 'width: 70px;',
			]
		]
	]
);
echo $form->select2Group(
	$model,
	'is_published',
	[
		'widgetOptions' => [
			'data'    => $model::$statusList,
			'options' => [
				'placeholder' => 'Статус',
			]
		],
	]
);
echo $form->textFieldGroup($model, 'name');
echo $form->textFieldGroup($model, 'phone_numeric');
echo $form->textFieldGroup($model, 'email');


$this->widget(
	'booster.widgets.TbButton',
	array(
		'buttonType' => 'submit',
		'context'    => 'primary',
		'label'      => 'Применить'
	)
);
echo '&nbsp;';
$this->widget(
	'booster.widgets.TbButton',
	array(
		'buttonType' => 'link',
		'url'        => ['index'],
		'context'    => 'default',
		'label'      => 'Сбросить'
	)
);
$this->endWidget();
$this->endWidget();

$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'         => 'Салоны (' . $dataProvider->getTotalItemCount() . ')',
		'headerIcon'    => 'fa fa-group',
		'headerButtons' => [
			[
				'label'     => 'Добавить салон',
				'url'       => $this->createUrl('create'),
				'icon'      => 'fa fa-plus',
				'showLabel' => false,
			]
		],
	]
);

$this->widget(
	'likefifa\components\system\admin\YbGridView',
	[
		'id'                       => 'lf-salon-grid',
		'dataProvider'             => $dataProvider,
		'ajaxUpdate'               => false,
		'columns'                  => [
			'id',
			[
				'header' => 'Регистрация',
				'name'   => 'created',
				'type'   => 'raw',
				'value'  => '$data->getCreated()',
			],
			[
				'name'  => 'name',
				'type'  => 'raw',
				'value' => 'CHtml::link(CHtml::encode($data->name), $data->getProfileUrl(true), ["target" => "_blank"])',
			],
			'phone',
			'rating',
			[
				'class'    => 'booster.widgets.TbEditableColumn',
				'name'     => 'rating_inner',
				'editable' => [
					'type'   => 'text',
					'url'    => Yii::app()->createUrl('/admin/salon/editField'),
					'emptytext' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
				],
			],
			[
				'header' => 'Мастеров',
				'type'   => 'raw',
				'value'  => 'CHtml::link(count($data->masters), ["/admin/master/index", "salon_id" => $data->id], ["target" => "_blank"])'
			],
			array(
				'class'    => 'booster.widgets.TbEditableColumn',
				'name'     => 'is_published',
				'header'   => 'Статус',
				'type'     => 'raw',
				'value'    => '$data->getStatus()',
				'editable' => array(
					'type'   => 'select',
					'source' => $model::$statusList,
					'url'    => Yii::app()->createUrl('/admin/salon/editField')
				)
			),
			'email',
			array(
				'class'    => 'booster.widgets.TbButtonColumn',
				'template' => '{update} {delete}',
			),
		],
	]
);

$this->endWidget();

