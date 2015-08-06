<?php

use dfs\docdoc\models\CityModel;

/**
 * @var \dfs\docdoc\back\controllers\PageController $this
 * @var dfs\docdoc\models\PageModel                 $model
 */

$this->menu = array(
	array('label' => 'Добавить стратегию', 'url' => array('create')),
);
?>

<h1>Стратегии</h1>

<?php

echo CHtml::link('Стратегии', "/2.0/ratingStrategy/index")." &nbsp;";
echo CHtml::link('Рейтинги врачей', "/2.0/rating/doctor")." &nbsp;";
echo CHtml::link('Рейтинги клиник', "/2.0/rating/clinic");

$this->widget(
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
				'name'  => 'name',
				'type'  => 'raw',
				'value' => '$data->name',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'type',
				'type'  => 'raw',
				'value' => '$data->type',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'for_object',
				'type'  => 'raw',
				'value' => '$data->getForObjectTitle()',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'chance',
				'type'  => 'raw',
				'value' => '$data->chance',
			),
			array(
				'class' => 'CDataColumn',
				'name'  => 'params',
				'type'  => 'raw',
				'value' => '$data->params',
			),
			array(
				'class'    => 'CButtonColumn',
				'template' => '{update} {delete} {check}',
				'buttons'  => [
					'check' => [
						'label'     => 'Проверить формулу',
						'imageUrl'  => '/img/icon/ok.png',
						'url'       => 'Yii::app()->createUrl("/2.0/ratingStrategy/check/{$data->id}")',
						'visible'   => '$data->type == "formula"'
					]
				]
			),
		),
	)
); ?>
