<?php
use dfs\docdoc\back\controllers\RatingController;

/**
 * @var CActiveDataProvider $dataProvider
 * @var RatingController $this
 */
?>

<h1>Рейтинги врачей</h1>
<?php
echo CHtml::link('Стратегии', "/2.0/ratingStrategy/index") . " &nbsp;";
echo CHtml::link('Рейтинги врачей', "/2.0/rating/doctor") . " &nbsp;";
echo CHtml::link('Рейтинги клиник', "/2.0/rating/clinic") . "<br/><br/>";
?>

<style type="text/css">
	/* grid border */
	.grid-view table.items th, .grid-view table.items td {
		border: 1px solid gray !important;
	}

	/* disable selected for merged cells */
	.grid-view td.merge {
		background: none repeat scroll 0 0 #F8F8F8;
	}
</style>

<?php $this->renderPartial('_form', ['message' => $message]); ?>

<?php
$this->menu = [
	['label' => 'Пересчитать рейтинги', 'url' => ['/2.0/rating/recalculate?type=doctor']],
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
	\dfs\docdoc\extensions\GroupGridView::class,
	[
		'id'           => 'doctor-clinic-grid',
		'dataProvider' => $dataProvider,
		'columns'      => $columns,
		'mergeColumns' => ['id', 'name'],
	]
); ?>
