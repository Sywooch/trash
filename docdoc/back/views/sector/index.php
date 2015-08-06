<?php
/**
 * @var dfs\docdoc\back\controllers\SectorController $this
 * @var dfs\docdoc\models\SectorModel                $model
 * @var CActiveDataProvider                          $dataProvider
 *
 */
$this->breadcrumbs = array(
	'Направления',
);

$this->menu = array(
	array('label' => 'Добавить направление', 'url' => array('create')),
);
?>

<h1>Направления</h1>

<?php $this->widget(
	'zii.widgets.grid.CGridView',
	array(
		'id'           => 'sector-grid',
		'dataProvider' => $dataProvider,
		'columns'      => array(
			'id',
			'name',
			'name_genitive',
			'name_plural',
			'name_plural_genitive',
			'rewrite_name',
			array(
				'class'    => 'CButtonColumn',
				'template' => '{update} {delete}',
			),
		),
	)
); ?>
