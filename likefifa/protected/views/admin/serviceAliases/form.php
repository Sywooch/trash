<?php
use likefifa\models\AdminModel;
use likefifa\models\LfServiceAlias;

/**
 * @var ServiceAliasesController $this
 * @var LfServiceAlias           $model
 */

$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'      => $this->pageTitle,
		'headerIcon' => 'fa fa-user',
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
			<?php echo $form->select2Group(
				$model,
				'specialization_id',
				[
					'widgetOptions' => [
						'data'        => CHtml::listData(
							LfSpecialization::model()->findAll(['order' => 'name ASC']),
							'id',
							'name'
						),
						'htmlOptions' => [
							'empty' => 'Выберите сециализацию',
						]
					],
				]
			) ?>

			<p>или</p>

			<?php echo $form->select2Group(
				$model,
				'service_id',
				[
					'widgetOptions' => [
						'data'        => CHtml::listData(
							LfService::model()->findAll(['order' => 'name ASC']),
							'id',
							'name'
						),
						'htmlOptions' => [
							'empty' => 'Выберите услугу',
						]
					],
				]
			) ?>

			<?php echo $form->textFieldGroup($model, 'alias') ?>

			<p>
				<strong>Указывайте специализацию или услугу. Нельзя указывать оба значения.</strong>
			</p>
			<p>
				<strong>
					Алиасы можно создавать без дополнительных символов, таких как кавычки и скобки, так как
					пользователь их не будет видеть
				</strong>
			</p>
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
