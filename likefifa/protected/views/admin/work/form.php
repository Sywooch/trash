<?php
/**
 * @var WorkController $this
 * @var LfWork         $model
 */

use likefifa\components\system\admin\YbActiveForm;

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
		'headerIcon' => 'fa fa-camera',
	]
);

/** @var YbActiveForm $form */
$form = $this->beginWidget(
	'likefifa\components\system\admin\YbActiveForm',
	[
		'id'          => 'verticalForm',
		'htmlOptions' => ['class' => 'container-fluid'],
	]
);
?>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<div class="prof-photo_imgs work-single-photo">
					<div class="work-item clearfix">
						<div class="item">
							<div class="prof-photo_imgs_wrap" id="prof-photo-<?php echo $model->id; ?>">
								<a
									class="prof-photo_imgs_img"
									href="javascript:void(0);"
									title="">
									<img
										class="lk-work-<?php echo $model->id; ?>"
										width="180"
										src="<?php echo $model->preview('small') . '?' . rand(); ?>"/>
								</a>

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
									href="<?php
									echo $this->createUrl("work/cropWindow", array("id" => $model->id));
									?>"></a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php echo $form->textFieldGroup($model, 'alt') ?>
			<?php echo $form->textFieldGroup($model, 'likes') ?>

			<?php echo $form->select2Group(
				$model,
				'master_id',
				[
					'widgetOptions' => [
						'data'    => LfMaster::model()->getListItems(),
						'options' => [
							'placeholder' => 'Выберите мастера',
						],
					],
				]
			); ?>

			<?php echo $form->select2Group(
				$model,
				'specialization_id',
				[
					'widgetOptions' => [
						'data'    => LfSpecialization::model()->getListItems(),
						'options' => [
							'placeholder' => 'Специализация не выбрана',
						],
					],
				]
			); ?>
		</div>
	</div>

<?php $this->widget(
	'booster.widgets.TbButton',
	[
		'buttonType' => 'submit',
		'context'    => 'primary',
		'label'      => $model->isNewRecord ? 'Создать' : 'Сохранить'
	]
); ?>
<?php
$this->endWidget();

$this->endWidget();
?>

<div id="crop-modal-data">
	<div class="modal-dialog modal-lg">
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
