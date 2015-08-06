<?php
use likefifa\models\AdminModel;

/**
 * @var UndergroundStationController $this
 * @var UndergroundStation           $model
 */

$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'      => $this->pageTitle,
		'headerIcon' => 'fa fa-road',
	]
);

/** @var TbActiveForm $form */
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
			<?php echo $form->textFieldGroup($model, 'name') ?>
			<?php echo $form->textFieldGroup($model, 'rewrite_name') ?>
			<?php echo $form->textFieldGroup($model, 'index') ?>

			<?php echo $form->select2Group(
				$model,
				'underground_line_id',
				[
					'widgetOptions' => [
						'data'    => UndergroundLine::model()->getListItems(),
						'options' => [
							'placeholder' => $model->getAttributeLabel('underground_line_id'),
						]
					],
				]
			); ?>

			<div class="form-group">
				<?php echo $form->labelEx($model, 'Cities'); ?><br>
				<?php echo CHtml::checkBoxList(
					'cities',
					$stationsCities,
					$citiesList,
					array('labelOptions' => array('style' => 'display:inline'))
				); ?>
				<?php echo $form->error($model, 'cities'); ?>
			</div>

			<?php echo $form->select2Group(
				$model,
				'district_id',
				[
					'widgetOptions' => [
						'data'    => DistrictMoscow::model()->getListItems(),
						'options' => [
							'placeholder' => $model->getAttributeLabel('district_id'),
						]
					],
				]
			); ?>
		</div>
	</div>

<?php
$this->widget(
	'booster.widgets.TbButton',
	[
		'buttonType' => 'submit',
		'context'    => 'primary',
		'label'      => $model->isNewRecord ? 'Создать' : 'Сохранить'
	]
);

$this->endWidget();

$this->endWidget();
