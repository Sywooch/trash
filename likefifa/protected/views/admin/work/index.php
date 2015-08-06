<?php
/**
 * @var LfWorkAdminFilter $model
 * @var WorkController    $this
 */

use likefifa\components\system\admin\YbActiveForm;
use likefifa\models\forms\LfWorkAdminFilter;
use likefifa\models\RegionModel;

Yii::app()->clientScript
	->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.mousewheel.js')
	->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.jscrollpane.min.js')
	->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.jsonSuggest.js');

$this->breadcrumbs = array(
	'Работы',
);

$dataProvider = $model->search();


$this->beginWidget('likefifa\components\system\admin\YbBox', ['title' => false]);
/** @var YbActiveForm $form */
$form = $this->beginWidget(
	'likefifa\components\system\admin\YbActiveForm',
	[
		'method' => 'get',
		'id'     => 'search-main',
		'type'   => 'inline',
	]
);

echo CHtml::hiddenField(
	'specialization',
	Yii::app()->request->getQuery('specialization'),
	[
		'id' => 'specialization',
	]
);
echo CHtml::hiddenField(
	'service',
	Yii::app()->request->getQuery('service'),
	[
		'id' => 'service',
	]
);
?>

<div class="form-group">
	<div class="suggest-container">
		<?php echo CHtml::textField(
			'query',
			Yii::app()->request->getQuery('query'),
			[
				'autocomplete' => 'off',
				'id'           => 'search-suggest',
				'placeholder'  => 'Укажите услугу',
				'class'        => 'form-control',
			]
		) ?>
	</div>
</div>

<?php
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

echo $form->datePickerGroup(
	$model,
	'createdFrom',
	[
		'widgetOptions' => [
			'options'     => [
				'format' => 'dd.mm.yyyy',
			],
			'htmlOptions' => [
				'style' => 'width:110px; min-width: 110px;',
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
				'style' => 'width:110px; min-width: 110px;',
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
	'region',
	[
		'widgetOptions' => [
			'data'    => CHtml::listData(RegionModel::model()->findAll(), 'id', 'name'),
			'options' => [
				'placeholder' => 'Выберите регион',
			],
			'htmlOptions' => [
				'empty' => 'Выберите регион',
			],
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
		'title'      => 'Работы (' . $dataProvider->getTotalItemCount() . ')',
		'headerIcon' => 'fa fa-camera',
	]
);

$this->widget(
	'likefifa\components\system\admin\YbGridView',
	[
		'id'           => 'lf-work-grid',
		'dataProvider' => $dataProvider,
		'columns'      => [
			[
				'name'  => 'created',
				'value' => '$data->getCreatedFormatted()',
			],
			[
				'name'  => 'master_id',
				'type'  => 'raw',
				'value' => '($data->master) ? "<a href=\"".Yii::app()->createUrl("admin/master/update", array("id" => $data->master_id))."\">".$data->master->getFullName()."</a>" : ""'
			],
			[
				'name'  => 'image',
				'type'  => 'raw',
				'value' => '($data->image) ? "<a href=\"" . $data->preview("full") . "\" target=\"_blank\">
				<img src=\"" . $data->preview(\'small\') . "\" /></a>" : ""'
			],
			"alt",
			"likes",
			'sort',
			'click_count',
			[
				'class'    => 'booster.widgets.TbButtonColumn',
				'template' => '{indexGrid} {update} {delete}',
				'buttons'           => [
					'indexGrid' => [
						'label'   => '<i class="glyphicon glyphicon-th"></i>',
						'url'     => 'Yii::app()->createUrl("/admin/work/indexGrid", ["id" => $data->id])',
						'options' => [
							'data-toggle' => 'tooltip',
							'title'       => 'На главную',
							'onclick'     => 'js:return IndexGrid.init(this);'
						],
					],
				],
			],
		],
	]
);

$this->endWidget();
?>

<script>
	var ajaxRequest;
	var suggest;
	$(function () {
		suggest = new SearchSuggest();
		suggest.formId = 'search-main';
		suggest.initSpec('search-suggest');
	});
</script>