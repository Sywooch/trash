<?php
use likefifa\models\CityModel;
use likefifa\models\RegionModel;
use likefifa\components\helpers\ListHelper;

/**
 * @var CityModel      $model
 * @var CityController $this
 */

$dataProvider = $model->search();

$this->breadcrumbs = array(
	'Города',
);


$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'         => 'Города',
		'headerIcon'    => 'fa fa-building',
		'headerButtons' => [
			[
				'label'     => 'Добавить город',
				'url'       => $this->createUrl('create'),
				'icon'      => 'fa fa-plus',
				'showLabel' => false,
			]
		],
	]
);

$this->widget('likefifa\components\system\admin\YbGridView',
	array(
		'id'           => 'city-grid',
		'dataProvider' => $dataProvider,
		'filter'       => $model,
		'ajaxUpdate'   => false,
		'columns'      => array(
			"id",
			array(
				'name'   => 'region_id',
				'type'   => 'raw',
				'filter' => CHtml::listData(RegionModel::model()->active()->orderByName()->findAll(), 'id', 'name'),
				'value'  => '$data->getRegionName()',
			),
			"rewrite_name",
			"name",
			"name_genitive",
			"name_prepositional",
			array(
				'name'   => 'is_active',
				'type'   => 'raw',
				'filter' => $model::$activeFlags,
				'value'  => '$data->getActiveFlag()',
			),
			array(
				'name'   => 'has_underground',
				'type'   => 'raw',
				'filter' => $model::$undergroundStationsFlags,
				'value'  => '$data->getUndergroundFlag()',
			),
			array(
				'class'    => 'booster.widgets.TbButtonColumn',
				'template' => '{update}',
			),
		),
	)
);

$this->endWidget();