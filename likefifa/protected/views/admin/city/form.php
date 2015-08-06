<?php
use likefifa\models\CityModel;
use likefifa\models\forms\CityModelAdminForm;
use likefifa\models\RegionModel;
use likefifa\components\helpers\ListHelper;

/**
 * @var CityModelAdminForm $model
 * @var CActiveForm        $form
 * @var CityController     $this
 * @var string             $h1
 */
$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'      => $this->pageTitle,
		'headerIcon' => 'fa fa-copy',
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
			<?php
			echo $form->dropDownListGroup(
				$model,
				'region_id',
				[
					'widgetOptions' => [
						'data'        => CHtml::listData(
							RegionModel::model()->active()->orderByName()->findAll(),
							'id',
							'name'
						),
						'htmlOptions' => ['empty' => ''],
					],
				]
			);

			echo $form->textFieldGroup($model, 'name');
			echo $form->textFieldGroup($model, 'rewrite_name');
			echo $form->textFieldGroup($model, 'name_genitive');
			echo $form->textFieldGroup($model, 'name_prepositional');
			echo $form->checkboxGroup($model, 'is_active');

			echo $form->select2Group(
				$model,
				'nearStationsElement',
				[
					'widgetOptions' => [
						'asDropDownList' => false,
						'options'        => [
							'width'         => '300px',
							'multiple'      => true,
							'ajax'          => [
								'url'      => $this->createUrl('ajax/metroSuggest'),
								'dataType' => 'json',
								'data'     => 'js:function(term) {return {term:term};}',
								'results'  => 'js:masterSearchMetroResult',
							],
							'initSelection' => 'js:masterSearchMetroResultInit',
							'escapeMarkup' => 'js:function (m) { return m; }',
						]
					]
				]
			);
			?>
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
