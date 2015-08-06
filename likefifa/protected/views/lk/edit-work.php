<?php
/**
 * @var LkController $this
 * @var LfWork       $model
 * @var LfMaster     $master
 * @var CActiveForm  $form
 */

Yii::app()->clientScript
	->registerScriptFile(Yii::app()->baseUrl . '/js/vk-photo-import.js')
	->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.tmpl.js');

$this->renderPartial('_header', ['model' => $master]);

Yii::app()->clientScript
	->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.fineuploader-3.0.min.js')
	->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.tmpl.js')
	->registerCssFile(Yii::app()->baseUrl . '/css/fineuploader.css')
	->registerScript(
		'image_upload',
		'createWorkUploader();',
		CClientScript::POS_READY
	);
?>
<script type="text/javascript">
	var top10_count = <?php echo $model::getTop10Count($model->master_id); ?>;
	$(function() {
		createTop10();
		$(document).on('change', '.top10-wrapper input', function() {
			createTop10(this);
		});
	});
</script>

<div class="content-wrap content-pad-bottom">
	<div class="page-profile">
		<div class="prof-wrap">
			<?php $this->widget(
				'application.components.likefifa.widgets.LfLkTabsWidget',
				array(
					'currentTab' => 'profile',
				)
			); ?>
			<div class="prof-cont">
				<?php $form = $this->beginWidget(
					'CActiveForm',
					array(
						'id'                   => 'master-form-edit-photo',
						'enableAjaxValidation' => false,
						'htmlOptions'          => array(
							'enctype' => 'multipart/form-data',
						),
					)
				); ?>
				<?php $this->widget(
					'application.components.likefifa.widgets.LfLkMenuWidget',
					array(
						'actions'       => $this->actions,
						'currentAction' => 'works',
						'model'         => $master,
					)
				); ?>

				<input type="hidden" name="isRemote" id="isRemoteUpload" value="0" />

				<?php if (!$model->isNewRecord): ?>
					<div class="prof-rht">
						<table>
							<tr>
								<td style="padding-right:36px;">
									<div class="prof-head-inp">Фотография:</div>
									<div class="prof-photo_add">

										<div id="load-img_wrap"><img
												class="lk-work-<?php echo $model->id ?>"
												src="<?php echo $model->preview('big') . '?' . rand(); ?>"
												alt="<?php echo $model->image; ?>"
												title="<?php echo $model->image; ?>"/></div>
										<?php echo LfHtml::loader(); ?>
										<div class="loader-overlay"></div>
										<a href="javascript:;" class="rotate rotate-left" data-direction="-90"
										   data-id="<?php echo $model->id ?>"
										   title="Повернуть против часовой стрелки на 90 градусов"></a>
										<a href="javascript:;" class="rotate rotate-right" data-direction="90"
										   data-id="<?php echo $model->id ?>"
										   title="Повернуть по часовой стрелке на 90 градусов"></a>

										<a
											class="various fancybox.ajax crop-image"
											title="Обрезать изображение"
											data-fancybox-type="ajax"
											href="<?php
											echo $this->createUrl("work/cropWindow", array("id" => $model->id));
											?>"></a>

										<div class="prof-photo_add_over"><span>Изменить фотографию</span></div>
										<?php echo $form->fileField(
											$model,
											'image',
											['onchange' => "$(this).closest('form').trigger('submit');"]
										); ?>
									</div>
									<div id="load-img_name"></div>
									<a href="<?php echo $this->createUrl('works'); ?>" class="det-back png">вернуться в
										галерею</a>
								</td>
								<td width="290">
									<div class="prof-inp_marg spec-selector">
										<div class="prof-head-inp">Раздел:</div>
										<?php echo LfHtml::activeDropDownList(
											$model,
											'specialization_id',
											$master->getSpecListForWorks()
										); ?>
										<div class="prof-note-inp" style="padding:2px 0 0 10px;">например, маникюр</div>
									</div>
									<div class="prof-inp_marg service-selector">
										<div class="prof-head-inp">Подраздел:</div>
										<?php echo LfHtml::activeDropDownList(
											$model,
											'service_id',
											$master->getSpecListForWorks(true)
										); ?>
										<div class="prof-note-inp" style="padding:2px 0 0 10px;">например, французский
											маникюр
										</div>
									</div>

									<?php if($model->is_main || $model::getTop10Count($model->master_id) < 10): ?>
										<div style="margin-bottom: 25px;" class="in-bl b-top_check__wrap">
											<?php echo LfHtml::activeCheckBox(
												$model,
												'is_main',
												[
													'label' => 'добавить в ТОП <span class="b-top__num">10</span><span  class="in-bl b-ico_top__big"></span>'
												]
											); ?>
										</div>
									<?php endif; ?>

									<input type='hidden' name='redirect_link' value='' class='redirect_link'/>

									<div class="prof-btn_next" style="text-align:left; padding:0;">
										<div class="button button-blue"><span>Сохранить</span></div>
										<?php echo CHtml::submitButton('Сохранить'); ?>
									</div>
								</td>
							</tr>
						</table>
					</div>
				<?php else: ?>
					<div class="prof-rht lk-edit-works">
						<div id="file-uploader">
							<div class="prof-photo_add in-bl">
								<div class="prof-photo_add_cover" id="load-img_wrap"></div>
							</div>

							<div class="prof-photo_add add-vk-photos in-bl">
								<div class="prof-photo_add_cover" id="load-img_wrap"><span>загрузить фотографии<br/> из Вконтакте</span></div>
							</div>

							<div class="works-uploaded"></div>
							<a href="<?php echo $this->createUrl('works'); ?>" class="det-back png">вернуться в
								галерею</a>

							<div class="prof-btn_next">
								<div class="button button-blue lk-edit-works-button"><span>Сохранить</span></div>
								<?php echo CHtml::submitButton('Сохранить'); ?>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<div class="clearfix"></div>
				<?php $this->endWidget(); ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	var photoImport;
	$(function () {
		photoImport = new VkPhotoImport('.add-vk-photos');
	});
</script>

<script id="workTmpl" type="text/x-jquery-tmpl">
	<div class="work-item new-work-item" id="work-item-${id}">
		<div class="close"></div>
		<div class="forms">
			<div class="prof-inp_marg spec-selector">
				<div class="prof-head-inp">Раздел:</div>
				<?php
	echo LfHtml::activeDropDownList(
		$model,
		'specialization_id',
		$master->getSpecListForWorks(),
		[
			'addition' => '${id}',
			'name'     => 'LfWork[${id}][specialization_id]'
		]
	);
	?>
				<div class="prof-note-inp" style="padding:2px 0 0 10px;">например, маникюр</div>
			</div>
			<div class="prof-inp_marg service-selector">
				<div class="prof-head-inp">Подраздел:</div>
				<?php
				echo LfHtml::activeDropDownList(
					$model,
					'service_id',
					$master->getSpecListForWorks(true),
					[
						'addition' => '${id}',
						'name'     => 'LfWork[${id}][service_id]'
					]
				);
				?>
				<div class="prof-note-inp" style="padding:2px 0 0 10px;">например, французский
					маникюр
				</div>
			</div>
			<div class="work-file-name"></div>
			<div style="margin-bottom: 5px;" class="in-bl b-top_check__wrap top10-wrapper">
				<?php echo LfHtml::activeCheckBox(
					$model,
					'is_main',
					[
						'addition' => '${id}',
						'name'     => 'LfWork[${id}][is_main]',
						'label' => 'добавить в ТОП <span class="b-top__num">10</span><span class="in-bl b-ico_top__big"></span>'
					]
				); ?>
			</div>
		</div>
		<div class="prof-photo_add">
			<div class="photo">
				<img width="100%" src="${imagePath}" data-name="${imageName}" />
			</div>
			<?php echo LfHtml::loader(); ?>
			<div class="loader-overlay"></div>
			<a href="javascript:;" class="rotate rotate-left" data-direction="-90" data-name="${imageName}"
			   data-id="0" title="Повернуть против часовой стрелки на 90 градусов"></a>
			<a href="javascript:;" class="rotate rotate-right" data-direction="90" data-name="${imageName}"
			   data-id="0" title="Повернуть по часовой стрелке на 90 градусов"></a>

			<a
				class="various fancybox.ajax crop-image"
				title="Обрезать изображение"
				data-fancybox-type="ajax"
				href="<?php echo $this->createUrl("work/cropWindow")?>?name=${imageName}"></a>
		</div>
		<div class="clearfix"></div>
		<input type="hidden" name="LfWork[${id}][tempImage]" value="${imageName}" />
		<input type="hidden" name="LfWork[${id}][x1]" value="" data-target="x1" />
		<input type="hidden" name="LfWork[${id}][x2]" value="" data-target="x2" />
		<input type="hidden" name="LfWork[${id}][y1]" value="" data-target="y1" />
		<input type="hidden" name="LfWork[${id}][y2]" value="" data-target="y2" />
		<input type="hidden" name="LfWork[${id}][y2]" value="" data-target="y2" />
		<input type="hidden" name="LfWork[${id}][jcropWidth]" value="" data-target="jcropWidth" />
		<input type="hidden" name="LfWork[${id}][jcropHeight]" value="" data-target="jcropHeight" />
	</div>

</script>

<script id="vkAlbumsList" type="text/x-jquery-tmpl">
<div class="popup-app_head">
	<span>Выберите альбом</span><div class="popup-close png"></div>
</div>
<div class="popup-app_cont">
	<ul class="unstyled vk-albums-modal-list">
		{%each albums%}
			<li><a href="javascript:void(0);" data-id="${aid}" onclick="return photoImport.loadPhotos(this);">${title}</a></li>
		{%/each%}
	</ul>
</div>
</script>

<script id="vkPhotosList" type="text/x-jquery-tmpl">
<div class="popup-app_head">
	<span>Выберите фотографии</span><div class="popup-close png"></div>
</div>
<div class="popup-app_cont">
	<p class="back-to-albums"><a href="javascript:void(0);" onclick="photoImport.getAlbums();">вернуться</a></p>
	<div class="photos-container">
		<ul class="unstyled">
			{%each photos%}
				<li onclick="photoImport.selectPhoto(this);" data-id="${pid}">
					<div>
						<div class="preview" style="background:url(${src}) no-repeat center center;" title="${text}"></div>
					</div>
				</li>
			{%/each%}
			<li class="clearfix"></li>
		</ul>
	</div>

	<div class="popup-footer">
		<div class="button button-pink button-load-photo_vk__no" onclick="photoImport.savePhotos();"><span>Добавить выбранное</span></div>
	</div>
</div>
</script>