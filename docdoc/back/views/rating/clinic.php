<?php
use dfs\docdoc\back\controllers\RatingController;

/**
 * @var CActiveDataProvider $dataProvider
 * @var RatingController $this
 */
?>

<h1>Рейтинги клиник</h1>
<?php
echo CHtml::link('Стратегии', "/2.0/ratingStrategy/index") . " &nbsp;";
echo CHtml::link('Рейтинги врачей', "/2.0/rating/doctor") . " &nbsp;";
echo CHtml::link('Рейтинги клиник', "/2.0/rating/clinic") . "<br/><br/>";

$this->renderPartial('_form', ['message' => $message]);


$this->menu = [
	['label' => 'Пересчитать рейтинги', 'url' => ['/2.0/rating/recalculate?type=clinic']],
];
?>

<div class="span-5 last">
	<div id="sidebar">
		<?php
		$this->beginWidget(
			'zii.widgets.CPortlet',
			[
				'title' => 'Действия',
			]
		);
		$this->widget(
			'zii.widgets.CMenu',
			[
				'items'       => $this->menu,
				'htmlOptions' => ['class' => 'operations'],
			]
		);
		$this->endWidget();
		?>
	</div>
</div>

<?php $this->widget(
	'zii.widgets.grid.CGridView',
	[
		'id'           => 'clinic-clinic-grid',
		'dataProvider' => $dataProvider,
		'columns'      => $columns,
	]
); ?>
