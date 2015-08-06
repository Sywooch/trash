<?php

use dfs\docdoc\back\controllers\PartnerWidgetController;
use dfs\docdoc\models\PartnerWidgetModel;

/**
 * @var PartnerWidgetModel $model
 * @var PartnerWidgetController $this
 */
?>

<?php

$this->menu = [
	['label' => 'Добавить виджет', 'url' => Yii::app()->createUrl(
			"partnerWidget/edit", array(
				CHtml::activeName(new PartnerWidgetModel, "partner_id") => $model->partner_id
			)
		)],
];

?>

<h1>Виджеты партнеров</h1>
<div style="float:left;">
<?php

$this->widget('zii.widgets.CBreadcrumbs', array(
		'links'=> $this->breadcrumbs,
	));

?>
</div>
<?php

$this->widget(
	'zii.widgets.grid.CGridView',
	[
		'id'           => 'partner-grid',
		'dataProvider' => $model->search(),
		'filter'       => $model,
		'ajaxUpdate'   => false,
		'columns'      => [
			[
				'class'  => CDataColumn::class,
				'name'   => 'id',
				'filter' => false,
			],
			[
				'class'  => CDataColumn::class,
				'name'   => 'widget',
				'filter' => false,
			],
			[
				'class'  => CDataColumn::class,
				'name'   => 'json_config',
				'filter' => false,
			],
			[
				'class'  => CDataColumn::class,
				'name'   => 'is_used',
				'filter' => false,
			],
			[
				'class'  => CDataColumn::class,
				'name'   => '',
				'type'   => 'raw',
				'filter' => false,
				'value'  => '"<a href=\"" .
					Yii::app()->createUrl(
						"/2.0/partnerWidget/edit/$data->id"
					)
					. "\">Редактировать</a>"',
			],
			[
				'class'    => 'CButtonColumn',
				'template' => '{delete}',
			],
		],
	]
);
?>
