<?php
$this->breadcrumbs = array(
	'Фото салонов',
);

$dataProvider = $model->search();

$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'         => 'Фото салонов (' . $dataProvider->getTotalItemCount() . ')',
		'headerIcon'    => 'fa fa-camera',
	]
);

$this->widget(
	'likefifa\components\system\admin\YbGridView',
	array(
		'id'           => 'salon-photo-grid',
		'ajaxUpdate'   => false,
		'dataProvider' => $dataProvider,
		'filter'       => $model,
		'columns'      => array(
			array(
				'class'  => 'CDataColumn',
				'name'   => 'id',
				'filter' => $model->id,
			),
			array(
				'class'  => 'CDataColumn',
				'name'   => 'salon_id',
				'type'   => 'raw',
				'filter' => LfSalon::model()->getListItems(),
				'value'  => '($data->salon) ? "<a href=\"".Yii::app()->createUrl("admin/salon/update", array("id" => $data->salon_id))."\">".$data->salon->getFullName()."</a>" : ""'
			),
			array(
				'class'  => 'CDataColumn',
				'name'   => 'image',
				'type'   => 'raw',
				'filter' => false,
				'value'  => '($data->image) ? "<img src=".$data->preview("small")." />" : ""'
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

<style>
	#salon-photo-grid img {
		max-width: 200px !important;
	}
</style>