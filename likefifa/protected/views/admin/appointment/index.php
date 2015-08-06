<?php
use likefifa\components\system\admin\YbActiveForm;
use likefifa\models\AdminModel;
use likefifa\models\forms\LfAppointmentAdminFilter;

/**
 * @var LfAppointmentAdminFilter $model
 * @var AppointmentController    $this
 */

$this->breadcrumbs = array(
	'Заявки',
);

$dataProvider = $model->search();
?>

<?php
$this->beginWidget('likefifa\components\system\admin\YbBox', ['title' => false]);
/** @var YbActiveForm $form */
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
				'style' => 'width: 70px',
			]
		]
	]
);
echo $form->textFieldGroup($model, 'salon_name');
echo $form->textFieldGroup($model, 'salon_tel');
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
	'status',
	[
		'widgetOptions' => [
			'data'        => $model->getFullStatusList(),
			'htmlOptions' => [
				'empty' => 'Все заявки',
				'style' => 'width:200px'
			],
			'options'     => [
				'placeholder'  => $model->getAttributeLabel('status'),
				'escapeMarkup' => 'js:function(m) {return m;}',
			]
		],
	]
);

echo '<br/>';

echo $form->textFieldGroup($model, 'name');
echo $form->textFieldGroup($model, 'phone');
echo $form->datePickerGroup(
	$model,
	'control',
	[
		'widgetOptions' => [
			'options'     => [
				'format' => 'dd.mm.yyyy',
			],
			'htmlOptions' => [
				'style'       => 'width:125px; min-width: 125px;',
				'placeholder' => $model->getAttributeLabel('control'),
			],
		],
		'prepend'       =>
			'<i class="glyphicon glyphicon-calendar" data-toggle="tooltip" title="' .
			$model->getAttributeLabel('control') .
			'"></i>'
	]
);

echo $form->select2Group(
	$model,
	'admin_id',
	[
		'widgetOptions' => [
			'data'    => AdminModel::model()->getOperatorList(),
			'options' => [
				'placeholder' => $model->getAttributeLabel('admin_id'),
			]
		],
	]
);

echo $form->dropDownListGroup(
	$model,
	'is_viewed',
	[
		'widgetOptions' => [
			'data'        => $model::$yesNoList,
			'htmlOptions' => [
				'empty' => 'Просмотрено (все)',
			]
		]
	]
);

echo $form->dropDownListGroup(
	$model,
	'create_source',
	[
		'widgetOptions' => [
			'data'        => $model::$sourcesList,
			'htmlOptions' => [
				'empty' => 'Источник (все)',
			]
		]
	]
);

echo '<br/>';

echo $form->textFieldGroup($model, 'master_name');
echo $form->textFieldGroup($model, 'master_tel');

echo $form->datePickerGroup(
	$model,
	'date',
	[
		'widgetOptions' => [
			'options'     => [
				'format' => 'dd.mm.yyyy',
			],
			'htmlOptions' => [
				'style'       => 'width:110px; min-width: 110px;',
				'placeholder' => $model->getAttributeLabel('date'),
			],
		],
		'prepend'       =>
			'<i class="glyphicon glyphicon-calendar" data-toggle="tooltip" title="' .
			$model->getAttributeLabel('date') .
			'"></i>'
	]
);
?>

<span style="position: relative">
<?php
echo $form->textFieldGroup(
	$model,
	'service_name',
	[
		'widgetOptions' => [
			'htmlOptions' => [
				'id' => 'search-suggest',
			]
		]
	]
);
?>
</span>
<?php
echo $form->select2Group(
	$model,
	'favoriteLabel',
	[
		'widgetOptions' => [
			'data' => [
				0 => 'Все заявки',
				1 => 'Избранное'
			],
		]
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
?>

<?php
$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'         => 'Заявки (' . $dataProvider->getTotalItemCount() . ')',
		'headerIcon'    => 'fa fa-file',
		'headerButtons' => [
			[
				'label'     => 'Создать заявку',
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
		'id'                       => 'lf-appointment-grid',
		'dataProvider'             => $dataProvider,
		'rowHtmlOptionsExpression' => '$data->favorite != null ? ["class" => "fav-appointment"] : ($data->status == $data::STATUS_NEW ? ["class" => "new-appointment"] : [])',
		'selectableRows'           => 2,
		'bulkActions'              => array(
			'actionButtons'        => array(
				array(
					'id'         => 'mass-input',
					'align'      => 'left',
					'buttonType' => 'button',
					'context'    => 'primary',
					'size'       => 'small',
					'label'      => 'Удалить',
					'url'        => array('batchDelete'),
					'click'      => 'js:batchActions',
				),
			),
			'checkBoxColumnConfig' => array(
				'name' => 'id'
			),
		),
		'columns'                  => array(
			array(
				'name'  => 'id',
				'type'  => 'raw',
				'value' => '"
				<i class=\"fa fa-" . $data::$sourcesIcons[$data->create_source] ."\" data-toggle=\"tooltip\" title=\"Источник: ".$data::$sourcesList[$data->create_source]."\"></i>
				<i class=\"fa fa-" . $data->getStatusIcon() ."\" data-toggle=\"tooltip\" title=\"".$data->getStatus()."\"></i>
				<a href=\"".Yii::app()->createUrl("admin/appointment/update", array("id" => $data->id))."\"><span class=\"app_id\">".$data->id."</span></a>"'
			),
			array(
				'name'  => 'created',
				'type'  => 'raw',
				'value' => '$data->getCreated()',
			),
			array(
				'name'  => 'date',
				'type'  => 'raw',
				'value' => '($data->date) ? ( "<strong>" . date("H:i", $data->date) . "</strong> " . date("d.m.Y", $data->date) ) : "<em>не указано</em>"'
			),
			array(
				'header' => 'Исполнитель',
				'type'   => 'raw',
				'value'  => '$data->getSalonName() ? "<i class=\"fa fa-group\"></i> " . $data->getSalonName() : ($data->getMasterName() ? $data->getMasterName() : $data->master_id || $data->salon_id ? $data->getMasterName() : CHtml::link("Подобрать", Yii::app()->controller->getAppointmentUrl($data)))'
			),
			array(
				'header' => 'Телефон',
				'type'   => 'raw',
				'value'  => '$data->getSalonPhone() ? $data->getSalonPhone() : $data->getMasterPhone()'
			),
			'name',
			'phone',
			array(
				'name'  => 'service_name',
				'type'  => 'raw',
				'value' => '$data->getServiceName() . " " . ($data->departure ? "<i class=\"fa fa-cab\" data-toggle=\"tooltip\" title=\"Выезд\"></i>" : "")',
			),
			array(
				'header' => 'Цена',
				'type'   => 'raw',
				'value'  => '$data->getMergedPrice()',
			),
			array(
				'name'    => 'status',
				'type'    => 'raw',
				'value'   => '$data->getStatus()',
				'visible' => 'excel',
			),
			array(
				'header' => 'Комментарий',
				'type'   => 'raw',
				'value'  => [$this, 'getMergedCommend'],
			),
			array(
				'name'  => 'control',
				'type'  => 'raw',
				'value' => '($data->control) ? ( "<strong>" . date("H:i", $data->control) . "</strong> " . date("d.m.y", $data->control) ) : ( ($data->date) ? ( "<strong>" . date("H:i", $data->date) . "</strong> " . date("d.m.Y", $data->date) ) : "<em>не указано</em>" )'
			),
			array(
				'name'  => 'admin_id',
				'type'  => 'raw',
				'value' => '$data->getAdminName()'
			),
			array(
				'class'             => 'booster.widgets.TbButtonColumn',
				'template'          => '{fav} {update} {delete}',
				'buttons'           => [
					'fav' => [
						'label'   => '<i class="glyphicon glyphicon-star"></i>',
						'url'     => 'Yii::app()->createUrl("/admin/appointment/favorite", ["id" => $data->id])',
						'options' => [
							'data-toggle' => 'tooltip',
							'title'       => 'В избранное',
							'onclick'     => 'js:return addToFavorites(this);'
						],
					],
				],
				'visible'           => $model->status != $model::STATUS_REMOVED,
				'headerHtmlOptions' => ['style' => 'min-width: 70px;']
			),
		),
	)
);
$this->endWidget();
?>
<script type="text/javascript"
		src="<?php echo Yii::app()->getBaseUrl(); ?>/js/jquery.mousewheel.js?<?php echo RELEASE_MEDIA; ?>"></script>
<script type="text/javascript" src="<?php echo Yii::app()->getBaseUrl(
); ?>/js/jquery.jscrollpane.min.js?<?php echo RELEASE_MEDIA; ?>"></script>
<script type="text/javascript"
		src="<?php echo Yii::app()->homeUrl; ?>js/jquery.jsonSuggest.js?<?php echo RELEASE_MEDIA; ?>"></script>
<script type="text/javascript">
	var ajaxRequest;
	var suggest;
	$(function () {
		suggest = new SearchSuggest();
		suggest.formId = 'filter-form2';
		suggest.callback = function () {
			ajaxRequest = $('#search-main').serialize();
			$.fn.yiiGridView.update(
				'lf-appointment-grid',
				{data: ajaxRequest}
			);
		};
		suggest.initSpec('search-suggest');
	});
</script>

<script type="text/javascript">
	// as a global variable
	var gridId = 'lf-appointment-grid';

	$(function () {
		// prevent the click event
		$(document).on('click', '#' + gridId + ' a.bulk-action', function () {
			return false;
		});
	});
	function batchActions(values) {
		var url = $(this).attr('href');
		var ids = [];
		if (values.length > 0) {
			$.ajax({
				type: "POST",
				url: url,
				data: {massRemove: 1, ids: values},
				dataType: 'json',
				success: function (resp) {
					if (resp.status == "success") {
						$.fn.yiiGridView.update(gridId);
					} else {
						alert(resp.msg);
					}
				}
			});
		}
	}
</script>