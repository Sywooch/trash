<?php
use likefifa\models\forms\LfMasterAdminFilter;

/**
 * @var LfMasterAdminFilter $model
 * @var MasterController    $this
 */

$this->breadcrumbs = [
	'Мастера',
];

$dataProvider = $model->search();

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

echo $form->datePickerGroup(
	$model,
	'createdFrom',
	[
		'widgetOptions' => [
			'options'     => [
				'format' => 'dd.mm.yyyy',
			],
			'htmlOptions' => [
				'style'       => 'width:110px; min-width: 110px;',
				'placeholder' => $model->getAttributeLabel('createdFrom'),
			],
		],
		'prepend'       =>
			'<i class="glyphicon glyphicon-calendar" data-toggle="tooltip" title="' .
			$model->getAttributeLabel('createdFrom') .
			'"></i>'
	]
);

echo $form->datePickerGroup(
	$model,
	'createdTo',
	[
		'widgetOptions' => [
			'options'     => [
				'format' => 'dd.mm.yyyy',
			],
			'htmlOptions' => [
				'style'       => 'width:110px; min-width: 110px;',
				'placeholder' => $model->getAttributeLabel('createdTo'),
			],
		],
		'prepend'       =>
			'<i class="glyphicon glyphicon-calendar" data-toggle="tooltip" title="' .
			$model->getAttributeLabel('createdTo') .
			'"></i>'
	]
);


echo $form->select2Group(
	$model,
	'group_name',
	[
		'widgetOptions' => [
			'data'        => CHtml::listData(LfGroup::model()->ordered()->findAll(), 'name', 'name'),
			'options'     => [
				'placeholder' => $model->getAttributeLabel('Группа'),
			],
			'htmlOptions' => [
				'empty' => 'Выбрать группу',
			]
		],
	]
);

echo $form->select2Group(
	$model,
	'specialization_id',
	[
		'widgetOptions' => [
			'data'        => CHtml::listData(LfSpecialization::model()->findAll(), 'id', 'name'),
			'options'     => [
				'placeholder' => $model->getAttributeLabel('Специализация'),
			],
			'htmlOptions' => [
				'empty' => 'Выбрать специализацию',
			]
		],
	]
);

echo $form->select2Group(
	$model,
	'service_id',
	[
		'widgetOptions' => [
			'data'        => CHtml::listData(LfService::model()->findAll(), 'id', 'name'),
			'options'     => [
				'placeholder' => $model->getAttributeLabel('Услуга'),
			],
			'htmlOptions' => [
				'style' => 'width: 200px;',
				'empty' => 'Выбрать услугу',
			]
		],
	]
);
echo '<br/>';
echo $form->textFieldGroup($model, 'name');
echo $form->textFieldGroup($model, 'surname');
echo $form->textFieldGroup($model, 'phone_cell');
echo $form->textFieldGroup($model, 'email');
echo $form->textFieldGroup($model, 'rating');

echo $form->select2Group(
	$model,
	'status',
	[
		'widgetOptions' => [
			'data'    => $model::$statusList,
			'options' => [
				'placeholder' => $model->getAttributeLabel('status'),
			]
		],
	]
);

echo $form->select2Group(
	$model,
	'is_salon',
	[
		'widgetOptions' => [
			'data'        => ['Частник', 'Салонный'],
			'options'     => [
				'placeholder' => 'Тип',
			],
			'htmlOptions' => [
				'empty' => 'Выберите тип',
			],
		],
	]
);

echo $form->textFieldGroup($model, 'comment');

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
		'title'         => 'Мастера (' . $dataProvider->getTotalItemCount() . ')',
		'headerIcon'    => 'fa fa-female',
		'headerButtons' => [
			[
				'label'     => 'Добавить мастера',
				'url'       => $this->createUrl('create'),
				'icon'      => 'fa fa-plus',
				'showLabel' => false,
			]
		],
	]
);
$this->widget(
	'likefifa\components\system\admin\YbGridView',
	array(
		'dataProvider'   => $dataProvider,
		'columns'        => array(
			'id',
			array(
				'name'  => 'created',
				'type'  => 'raw',
				'value' => '$data->getCreatedFormatted()',
			),
			array(
				'name'  => 'group_name',
				'type'  => 'raw',
				'value' => '$data->getGroupName()',
			),
			[
				'header' => 'Имя',
				'name'   => 'fullname',
				'type'   => 'raw',
				'value'  => '"<span class=\"fa fa-lightbulb-o master-free-status status-".$data->is_free."\" data-toggle=\"tooltip\" title=\"".($data->is_free ? "готов" : "не готов")." принимать заказы\"></span> "
				. CHtml::link(CHtml::encode($data->getFullName()), $data->getProfileUrl(true), ["target" => "_blank"])
				. ($data->salon != null ? " <i class=\"fa fa-group\" data-toggle=\"tooltip\" title=\"".CHtml::encode($data->salon->name)."\"></i>" : "")',
			],
			'phone_cell',
			'email',
			[
				'name'   => 'balance',
				'filter' => false,
				'type'   => 'raw',
				'value'  => 'Yii::app()->controller->renderPartial("balance", array("model" => $data))',
			],
			[
				'name'    => 'balance',
				'type'    => 'raw',
				'value'   => '$data->getBalance()',
				'visible' => 'excel',
			],
			'rating',
			array(
				'class'    => 'booster.widgets.TbEditableColumn',
				'name'     => 'status',
				'type'     => 'raw',
				'value'    => '$data->getStatus()',
				'editable' => [
					'type'   => 'select',
					'source' => $model::$statusList,
					'url'    => Yii::app()->createUrl('/admin/master/editField')
				],
			),
			array(
				'name'    => 'Заявки',
				'type'    => 'raw',
				'value'   => 'count($data->appointments)',
				'class'   => 'likefifa\components\system\admin\YbPopoverColumn',
				'options' => array(
					'title'   => 'Заявки мастера',
					'content' => '$data->getAppointmentCountByTypes()',
					'html'    => true,
				)
			),
			array(
				'class'    => 'booster.widgets.TbEditableColumn',
				'name'     => 'comment',
				'type'     => 'raw',
				'value'    => '!empty($data->comment) ? "<p rel=\"tooltip\" title=\"" . CHtml::encode($data->getFormattedComment()) . "\">" . likefifa\components\helpers\StringHelper::truncate($data->getFormattedComment()) . "</p>" : ""',
				'editable' => array(
					'type'      => 'text',
					'emptytext' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
					'url'       => Yii::app()->createUrl('/admin/master/editField')
				)
			),
			array(
				'class'    => 'booster.widgets.TbButtonColumn',
				'template' => '{update} {delete}',
			),
		),
	)
);
$this->endWidget();
