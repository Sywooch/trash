<?php
/**
 * @var UndergroundStationController $this
 * @var UndergroundStation $model
 */
$this->breadcrumbs=array(
	'Станции метро',
);

$dataProvider = $model->search();

$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'         => 'Станции метро (' . $dataProvider->getTotalItemCount() . ')',
		'headerIcon'    => 'fa fa-road',
		'headerButtons' => [
			[
				'label'     => 'Добавить станцию',
				'url'       => $this->createUrl('create'),
				'icon'      => 'fa fa-plus',
				'showLabel' => false,
			]
		],
	]
);

$this->widget('likefifa\components\system\admin\YbGridView', array(
		'id'=>'underground-station-grid',
		'dataProvider'=> $dataProvider,
		'filter' => $model,
		'ajaxUpdate'=>false,
		'columns'=>array(
			array(
				'name'				=> 'id',
				'filter'			=> false,
				'sortable'			=> true,
			),
			'name',
			array(
				'name'				=> 'undergroundLine',
				'header'			=> UndergroundStation::model()->getAttributeLabel('undergroundLine'),
				'filter'			=> UndergroundLine::model()->getListItems(),
				'sortable'			=> true,
				'type' 				=> 'raw',
				'value' 			=> '"<span style=\"color: #".$data->undergroundLine->color."\">".$data->undergroundLine->name."</span>"',
			),
			array(
				'name'=>'index',
				'type'=>'raw',
				'filter'=> false,
				'value'=>'CHtml::textField("index[$data->id]",$data->index,array("style"=>"width:100px;"))',
				'htmlOptions'=>array("width"=>"50px"),
			),
			array(
				'class'    => 'booster.widgets.TbButtonColumn',
				'template' => '{update} {delete}',
			),
		),
	));
$this->endWidget();
