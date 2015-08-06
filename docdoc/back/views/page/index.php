<?php

use dfs\docdoc\models\CityModel;

/**
 * @var \dfs\docdoc\back\controllers\PageController $this
 * @var dfs\docdoc\models\PageModel                 $model
 */

$this->breadcrumbs = array(
	'SEO страницы',
);

$this->menu = array(
	array('label' => 'Добавить страницу', 'url' => array('create')),
);
?>

<h1>SEO страницы</h1>

<?php $this->widget(
	'zii.widgets.grid.CGridView',
	array(
		'id'           => 'page-grid',
		'dataProvider' => $model->search(),
		'filter'       => $model,
		'columns'      => array(
			array(
				'class' => 'CDataColumn',
				'name'  => 'id',
				'type'  => 'raw',
				'value' => '$data->id',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'title',
				'type'  => 'raw',
				'value' => '$data->title',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'h1',
				'type'  => 'raw',
				'value' => '$data->h1',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'keywords',
				'type'  => 'raw',
				'value' => '$data->keywords',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'description',
				'type'  => 'raw',
				'value' => '$data->description',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'seo_text_top',
				'type'  => 'raw',
				'value' => '$data->seo_text_top',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'seo_text_bottom',
				'type'  => 'raw',
				'value' => '$data->seo_text_bottom',
			),
			array(
				'class'  => 'CDataColumn',
				'name'   => 'is_show',
				'type'   => 'raw',
				'filter' => $model->showFlags,
				'value'  => '$data->getShowFlag()',
			),
			array(
				'class'  => 'CDataColumn',
				'name'   => 'site',
				'type'   => 'raw',
				'filter' => Yii::app()->params['siteList'],
				'value'  => '$data->getSite()',
			),
			array(
				'class'  => 'CDataColumn',
				'name'   => 'id_city',
				'type'   => 'raw',
				'filter' => CityModel::model()->getCityListWithAny(),
				'value'  => '$data->getCityName()',
			),
			array(
				'class'    => 'CButtonColumn',
				'template' => '{update} {delete}',
			),
		),
	)
); ?>
