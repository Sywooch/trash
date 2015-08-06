<?php
/**
 * @var SeoTextController   $this
 * @var LfSeoText $model
 */
$dataProvider = $model->search();

$this->breadcrumbs = array(
	'Сео блоки',
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
echo $form->textFieldGroup($model, 'name');
echo $form->textFieldGroup($model, 'page');

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
		'title'         => 'Сео блоки (' . $dataProvider->getTotalItemCount() . ')',
		'headerIcon'    => 'fa fa-leaf',
		'headerButtons' => [
			[
				'label'     => 'Добавить',
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
		'id'           => 'lf-seo-text-grid',
		'dataProvider' => $dataProvider,
		'ajaxUpdate'   => false,
		'columns'      => array(
			'id',
			'name',
			'page',
			array(
				'class'    => 'booster.widgets.TbButtonColumn',
				'template' => '{copy}  {update} {delete}',
				'buttons' => [
					'copy' => [
						'label'   => '<i class="fa fa-copy"></i>',
						'url'     => 'Yii::app()->createUrl("/admin/seoText/create", ["id" => $data->id])',
						'options' => [
							'data-toggle' => 'tooltip',
							'title'       => 'Копировать',
						],
					],
				]
			),
		),
	)
);

$this->endWidget();
