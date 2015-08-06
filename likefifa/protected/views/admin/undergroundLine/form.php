<?php
/**
 * @var UndergroundLineController $this
 * @var UndergroundLine           $model
 */

use likefifa\models\CityModel;

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
			<?php echo $form->textFieldGroup($model, 'color') ?>
			<?php echo $form->dropDownListGroup(
				$model,
				'city_id',
				[
					'widgetOptions' => [
						'data' => CHtml::listData(CityModel::model()->withUndergroundStation()->findAll(), 'id', 'name')
					]
				]
			) ?>
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
