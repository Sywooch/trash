<?php
/**
 * @var ArticleController $this
 * @var CActiveDataProvider $dataProvider
 */
$this->breadcrumbs = array(
	'Статьи',
);

$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'         => 'Статьи (' . $dataProvider->getTotalItemCount() . ')',
		'headerIcon'    => 'fa fa-file-text',
		'headerButtons' => [
			[
				'label'     => 'Создать статью',
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
		'id'           => 'article-grid',
		'dataProvider' => $dataProvider,
		'columns'      => array(
			'id',
			array(
				'class'    => 'CDataColumn',
				'name'     => 'name',
				'sortable' => true,
			),
			array(
				'class'    => 'CDataColumn',
				'name'     => 'disabled',
				'value'    => '$data->disabled ? "да" : "нет"',
				'sortable' => true,
			),
			array(
				'class'    => 'booster.widgets.TbButtonColumn',
				'template' => '{update} {delete}',
			),
		),
	)
);

$this->endWidget();
