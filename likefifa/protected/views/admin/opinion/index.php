<?php
/**
 * @var OpinionController $this
 * @var LfOpinion         $model
 */

$this->breadcrumbs = [
	'Отзывы',
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
echo $form->dropDownListGroup(
	$model,
	'allowed',
	[
		'widgetOptions' => [
			'data'        => $model->getAllowedListItems(),
			'htmlOptions' => [
				'empty' => 'Все',
			]
		]
	]
);
echo $form->select2Group(
	$model,
	'master_id',
	[
		'widgetOptions' => [
			'data'    => LfMaster::model()->getListItems(),
			'options' => [
				'placeholder' => 'Выберите мастера',
			]
		],
	]
);
echo $form->select2Group(
	$model,
	'salon_id',
	[
		'widgetOptions' => [
			'data'    => LfSalon::model()->getListItems(),
			'options' => [
				'placeholder' => 'Выберите салон',
			]
		],
	]
);

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
		'title'      => 'Отзывы (' . $dataProvider->getTotalItemCount() . ')',
		'headerIcon' => 'fa fa-wechat',
	]
);

$this->widget(
	'booster.widgets.TbListView',
	array(
		'id'            => 'opinions-list',
		'dataProvider'  => $dataProvider,
		'itemView'      => '_opinion',
		'itemsCssClass' => 'comments-list',
		'itemsTagName'  => 'ul',
		'template'      => '{items}{pager}'
	)
);

$this->endWidget();