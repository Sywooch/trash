<?php
/**
 * @var MasterController $this
 * @var LfMaster         $model
 */

$jcropDir = Yii::getPathOfAlias('application.vendors.tapmodo.jcrop');
Yii::app()->clientScript->registerScriptFile(
	Yii::app()->assetManager->publish("{$jcropDir}/js/jquery.color.js"),
	CClientScript::POS_BEGIN
);
Yii::app()->clientScript->registerScriptFile(
	Yii::app()->assetManager->publish("{$jcropDir}/js/jquery.Jcrop.min.js"),
	CClientScript::POS_BEGIN
);
Yii::app()->clientScript->registerCssFile(
	Yii::app()->assetManager->publish("{$jcropDir}/css/jquery.Jcrop.min.css")
);
Yii::app()->assetManager->publish("{$jcropDir}/css/Jcrop.gif");


$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'      => $this->pageTitle,
		'headerIcon' => 'fa fa-female',
	]
);
?>

<div class="prof-photo_imgs">
	<?php foreach ($model->works as $work): ?>
		<div class="work-item clearfix">
			<div class="item">
				<div class="prof-photo_imgs_wrap" id="prof-photo-<?php echo $work->id; ?>">
					<a
						class="prof-photo_imgs_img"
						href="javascript:void(0);"
						title="">
						<img
							class="lk-work-<?php echo $work->id; ?>"
							width="180"
							src="<?php echo $work->preview('small') . '?' . rand(); ?>"/>
					</a>
					<a href="<?php echo Yii::app()->createUrl('/admin/master/deleteWork', ['id' => $work->id]) ?>"
					   class="del">
						<span><i>удалить</i></span>
						<img src="/i/profile/icon-del-photo.png" class="remmove-img"/>
					</a>

					<div class="loader-overlay"></div>

					<a href="javascript:;" class="rotate rotate-left" data-direction="-90"
					   data-id="<?php echo $work->id ?>"
					   title="Повернуть против часовой стрелки на 90 градусов"></a>
					<a href="javascript:;" class="rotate rotate-right" data-direction="90"
					   data-id="<?php echo $work->id ?>"
					   title="Повернуть по часовой стрелке на 90 градусов"></a>

					<a
						class="various fancybox.ajax crop-image"
						title="Обрезать изображение"
						href="<?php
						echo $this->createUrl("work/cropWindow", array("id" => $work->id));
						?>"></a>
				</div>
			</div>
			<div class="item-settings">
				<input type="hidden" class="work-id" value="<?php echo $work->id ?>"/>

				<div class="spec-selector">
					<?php echo CHtml::label('Специализация: ', 'specialization_id') ?>
					<?php echo CHtml::dropDownList(
						'specialization_id',
						$work->specialization_id,
						$model->getSpecListForWorks(),
						['empty' => 'Выберите специализацию']
					) ?>
				</div>
				<div class="service-selector">
					<?php echo CHtml::label('Услуга: ', 'service_id') ?>
					<?php echo CHtml::dropDownList(
						'service_id',
						$work->service_id,
						$model->getSpecListForWorks(true),
						['empty' => 'Выберите услугу']
					) ?>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>

<?php $this->endWidget(); ?>


<div id="crop-modal-data">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
						class="sr-only">Закрыть</span></button>
				<h4 class="modal-title" id="myModalLabel"> Обрезка фото </h4>
			</div>
			<div class="modal-body">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
				<button type="submit" class="btn btn-primary save-crop-image">Сохранить</button>
			</div>
		</div>
	</div>
</div>

<?php echo CHtml::dropDownList(
	'service_id_tmpl_selector',
	null,
	$model->getSpecListForWorks(true),
	['empty' => 'Выберите услугу', 'class' => 'hide', 'id' => 'service_id_tmpl_selector']
) ?>

<script type="text/javascript">
	window.serviceTree = <?php echo json_encode(LfSpecialization::model()->getIdsTree()); ?>;
	function changeSpec(select) {
		var specId = $(select).val();
		var serviceSelector = $(select).closest('.spec-selector').siblings('.service-selector').find('select');
		var serviceId = serviceSelector.val();
		var tmplSelector = $('#service_id_tmpl_selector')
			.clone()
			.removeAttr('id')
			.attr('name', serviceSelector.attr('name'))
			.removeClass('hide');

		if (specId && serviceTree[specId]) {
			tmplSelector.find('option').each(function (n) {
				if (n == 0) {
					return true;
				}
				var remove = true;
				for (var i = 0; i < serviceTree[specId].length; i++) {
					if ($(this).attr('value') == serviceTree[specId][i]) {
						if($(this).attr('value') == serviceId) {
							$(this).attr('selected', 'selected');
						}
						remove = false;
					}
				}
				if (remove) {
					$(this).remove();
				}
			});
		} else {
			tmplSelector.find('option').each(function (n) {
				if (n == 0) {
					return true;
				}
				$(this).remove();
			});
		}

		serviceSelector.replaceWith(tmplSelector);
	}
	$('.spec-selector select').on('change', function () {
		changeSpec(this);
	});

	$(function () {
		$('.prof-photo_imgs select[name=specialization_id]').each(function () {
			changeSpec(this);
		});
	});

	$(document).on('change', '.service-selector select', function () {
		$.post(homeUrl + 'admin/master/saveWork', {
			id: $(this).closest('.service-selector').siblings('.work-id').val(),
			serviceId: $(this).val(),
			specId: $(this).closest('.service-selector').siblings('.spec-selector').find('select').val()
		});
	});
</script>