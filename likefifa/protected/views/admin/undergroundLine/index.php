<?php
use likefifa\models\CityModel;

/**
 * @var UndergroundLineController $this
 * @var UndergroundLine           $model
 */

$this->breadcrumbs = array(
	'Ветки метро',
);

$dataProvider = $model->search();

$this->menu = array(
	array('label' => 'Добавить ветку', 'url' => array('create')),
);
?>

<?php
$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'         => 'Ветки метро (' . $dataProvider->getTotalItemCount() . ')',
		'headerIcon'    => 'fa fa-road',
		'headerButtons' => [
			[
				'label'     => 'Создать ветку',
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
		'id'           => 'underground-line-grid',
		'dataProvider' => $dataProvider,
		'filter'       => $model,
		'columns'      => array(
			'id',
			array(
				'name'     => 'name',
				'sortable' => true,
				'type'     => 'raw',
				'value'    => '"<span style=\"color: #".$data->color."\">".$data->name."</span>"',
			),
			array(
				'name'   => 'city_id',
				'type'   => 'raw',
				'filter' => CHtml::listData(
					CityModel::model()->withUndergroundStation()->findAll(),
					"id",
					function($post) {
						return CHtml::encode($post->name);
					}
				),
				'value'  => '$data->getCityName()',
			),
			array(
				'class'    => 'booster.widgets.TbButtonColumn',
				'template' => '{update} {delete}',
			),
		),
	)
);
$this->endWidget();
?>
