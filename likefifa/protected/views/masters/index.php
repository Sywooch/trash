<?php
/**
 * @var MastersController $this
 * @var LfMaster          $model
 * @var string            $searchUrl
 * @var LfOpinion         $opinion
 */
Yii::app()->clientScript->registerScriptFile(
	Yii::app()->assetManager->publish(
		Yii::getPathOfAlias('application.vendors.malsup.form') . '/jquery.form.js',
		false,
		-1,
		YII_DEBUG
	),
	CClientScript::POS_HEAD
);
?>
<div class="content-wrap content-pad-bottom">
	<?php if (isset($_GET["cv"])) { ?>
		<div class="back-to-lk">
		<a href="<?php echo Yii::app()->createUrl("lk"); ?>" class="button button-pink">
			<span>Вернуться в личный кабинет</span>
		</a>
		</div><?php } ?>
	<div class="det-back">
		<?php if ($searchUrl): ?>
			<a href="<?php echo $searchUrl; ?>"> вернуться к результатам поиска</a>
		<?php elseif ($model->salon): ?>
			<a href="<?php echo $model->salon->getModelUrl(); ?>"> вернуться в салон</a>
		<?php
		else: ?>
			<a href="<?php echo $this->createUrl('site/index'); ?>"> на главную</a>
		<?php endif; ?>
	</div>

	<?php $this->renderPartial("partials/_index", compact('model', 'opinion')); ?>
</div>

<script type="text/javascript">
	// Если нужно показать все работы
	if (/works/.test(document.location.hash)) {
		initPhotosCallback = function () {
			$('.det-works_switch_open').not('.det-works_full_link').trigger('click');
		};
	}
	// Если нужно показать работу
	if (/work-/.test(document.location.hash)) {
		initPhotosCallback = function () {
			var parts = /work-(\d+)/.exec(document.location.href);
			if (parts != null) {
				var workId = parts[1];
				var link = $('a[data-work-id=' + workId + ']');
				if (link.length > 0) {
					link.trigger('click');
				}
			}
		};
	}
</script>
