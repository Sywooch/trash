<?php
/**
 * @var WorkController $this
 * @var LfWork         $model
 * @var LfWork[]       $works
 */

use likefifa\models\RegionModel;

$i = 0;
$diff = 0;
if ($model->master->city->region_id == RegionModel::MO_ID) {
	$diff = 10;
}
?>

<div class="modal-dialog">
	<?php echo CHtml::form(Yii::app()->createUrl("admin/appointment")); ?>
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
					class="sr-only">Закрыть</span></button>
			<h4 class="modal-title" id="myModalLabel">
				Выбор фотографий для главной страницы
				<strong>
					<?php echo $model->master->city->region->name_genitive ?>
				</strong>
			</h4>
		</div>
		<div class="modal-body">
			<div class="index-grid">
				<div class="image-preview">
					<?php echo CHtml::image(
						$model->preview('small'),
						'',
						['class' => 'preview', 'data-id' => $model->id]
					) ?>
				</div>

				<div class="tr-container">
					<div class="tr clearfix">
						<?php
						for (; $i < 9; $i++) {
							if ($i != 0 && $i % 3 == 0) {
								echo '</div><div class="tr clearfix">';
							}

							if (array_key_exists($i + $diff, $works)) {
								$work = $works[$i + $diff];
								echo
									'<div data-index="' . ($i + $diff) . '">' .
									CHtml::image($work->preview('small'), '', ['data-id' => $work->id]) .
									'</div>';
							} else {
								echo '<div data-index="' . ($i + $diff) . '"></div>';
							}
						}
						?>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
		</div>
	</div>
	<?php echo CHtml::endForm(); ?>
</div>