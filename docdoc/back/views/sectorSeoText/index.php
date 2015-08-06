<?php
/**
 * @var dfs\docdoc\back\controllers\SectorSeoTextController $this
 * @var SectorSeoText                                       $model
 * @var CActiveDataProvider                                 $dataProvider
 *
 */

$this->breadcrumbs = array(
	'SEO-блоки',
);

$this->menu = array(
	array('label' => 'Добавить SEO-блок', 'url' => array('create')),
);
?>

<h1>SEO-блоки</h1>

<?php $this->widget(
	'zii.widgets.grid.CGridView',
	array(
		'id'           => 'sector-seo-text-grid',
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
				'name'     => 'position',
				'value'    => '$data->getPositionName()',
				'sortable' => true,
			),
			array(
				'class'    => 'CDataColumn',
				'name'     => 'disabled',
				'value'    => '$data->disabled ? "да" : "нет"',
				'sortable' => true,
			),
			array(
				'class'    => 'CButtonColumn',
				'template' => '{update} {delete}',
			),
		),
	)
); ?>
