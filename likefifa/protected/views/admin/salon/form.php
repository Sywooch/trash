<?php
/**
 * @var SalonController $this
 * @var LfSalon         $model
 */

Yii::import('application.components.system.front.LfHtml');

$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'      => $this->pageTitle,
		'headerIcon' => 'fa fa-group',
	]
);

/** @var TbActiveForm $form */
$form = $this->beginWidget(
	'likefifa\components\system\admin\YbActiveForm',
	[
		'id'          => 'verticalForm',
		'htmlOptions' => [
			'class'   => 'container-fluid',
			'enctype' => 'multipart/form-data',
		],
	]
);
?>
	<div class="row">
	<div class="col-md-7">
	<?php if (!$model->isNewRecord): ?>
		<p><a target="_blank" href="<?php echo $model->getModelUrl(); ?>">Публичный профиль</a></p>
	<?php endif; ?>

	<?php echo $form->checkboxGroup($model, 'is_published') ?>

	<div class="master-accordion">
	<h3><a href="#">Информация о салоне</a></h3>

	<div>
		<?php echo $form->fileFieldGroup($model, 'logo') ?>

		<?php if ($model->isUploaded()): ?>
			<p>
				<?php echo CHtml::image(
					$model->avatar() . '?' . rand(),
					$model->logo,
					[
						'width' => 100,
						'title' => $model->logo,
					]
				) ?>

				<br/>
				<?php echo $model->logo . ', ' . RussianTextUtils::fileSize($model->getFileSize()) ?>
			</p>

			<div class="form-group">
				<?php echo CHtml::checkBox('delete_logo', false, array("class" => "inline-block")); ?>
				<?php echo $form->labelEx(
					$model,
					'delete_logo',
					array("class" => "inline-block", "for" => "delete_logo")
				); ?>
			</div>
		<?php endif; ?>

		<?php echo $form->textFieldGroup($model, 'name') ?>

		<?php echo $form->maskedTextFieldGroup(
			$model,
			'phone',
			['widgetOptions' => ['mask' => '+7 (999) 999 99 99']]
		) ?>

		<?php echo $form->emailFieldGroup($model, 'email') ?>

		<div class="form-group">
			<?php echo $form->labelEx($model, 'rating'); ?>
			<?php echo $form->textField($model, 'rating', array('size' => 5)); ?>
			<i>например, -0.5, -2, 0.7, 3</i>
			<?php echo $form->error($model, 'rating'); ?>
		</div>

		<div class="form-group">
			<label>Будни</label>

			<div class="form-inline">
				<div class="form-group">
					<div class="input-group">
						<?php echo $form->labelEx($model, 'hrs_wd_from'); ?>
						<?php echo $form->dropDownList(
							$model,
							'hrs_wd_from',
							range(0, 23)
						); ?>
						<?php echo $form->labelEx($model, 'hrs_wd_to'); ?>
						<?php echo $form->dropDownList(
							$model,
							'hrs_wd_to',
							range(0, 23)
						); ?>
					</div>
				</div>
			</div>

			<label>Выходные</label>

			<div class="form-inline">
				<div class="form-group">
					<div class="input-group">
						<?php echo $form->labelEx($model, 'hrs_we_from'); ?>
						<?php echo $form->dropDownList(
							$model,
							'hrs_we_from',
							range(0, 23)
						); ?>
						<?php echo $form->labelEx($model, 'hrs_we_to'); ?>
						<?php echo $form->dropDownList(
							$model,
							'hrs_we_to',
							range(0, 23)
						); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<h3><a href="#">Адрес салона</a></h3>

	<div>
		<?php echo $form->textFieldGroup($model, 'add_street') ?>
		<?php echo $form->textFieldGroup($model, 'add_house') ?>
		<?php echo $form->textFieldGroup($model, 'add_korp') ?>
		<?php echo $form->textFieldGroup($model, 'add_info') ?>

		<?php echo $form->select2Group(
			$model,
			'underground_station_id',
			[
				'widgetOptions' => [
					'data'    => UndergroundStation::model()->getListItems(),
					'options' => [
						'placeholder' => 'Выберите станцию метро',
					],
				],
			]
		); ?>

		<?php echo $form->select2Group(
			$model,
			'district_id',
			[
				'widgetOptions' => [
					'data'    => DistrictMoscow::model()->getListItems(),
					'options' => [
						'placeholder' => 'Выберите район',
					],
				],
			]
		); ?>
	</div>

	<h3><a href="#">Прайс-лист</a></h3>

	<div>
		<?php
		$tree = LfSpecialization::model()->getSalonTree($model);
		$serviceIds = $model->getRelationIds('services');
		foreach ($tree as $specId => $spec) {
			?>
			<?php if (!empty($spec['services'])) { ?>
				<div>
					<label class="capitalize price-title">
						<?php echo $spec['name']; ?>
					</label>

					<div>
						<?php
						foreach ($spec['services'] as $serviceId => $serviceParams) {
							$id = 'service-' . $serviceId;
							$price = $model->getPriceForService($serviceId);
							?>
							<div class="checkbox-price">
								<?php echo LfHtml::checkBox(
									'LfSalon[prices][serviceIds][' . $serviceId . ']',
									in_array($serviceId, $serviceIds),
									array(
										'value' => $serviceId,
										'id'    => $id
									)
								); ?>
								<label
									class="checkbox-label checkbox-label-price"
									for="inp-check_<?php echo $id; ?>"
									>
									<?php echo $serviceParams['name']; ?>
								</label>
								<?php echo CHtml::textField(
									'LfSalon[prices][values][' . $serviceId . ']',
									$price ? $price->price : null,
									array("size" => 5)
								);?>
								рублей
								<?php echo LfHtml::checkBox(
									'LfSalon[prices][isFrom][' . $serviceId . ']',
									$price && $price->price_from,
									array(
										'value' => 1,
										'id'    => $id . '_fr',
										'label' => 'от'
									)
								);?>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
		<?php } ?>
	</div>

	<h3><a href="#">Описание</a></h3>

	<div>
		<?php echo $form->textAreaGroup($model, 'description') ?>
	</div>

	<h3><a href="#">Изменить пароль</a></h3>

	<div>
		<?php echo $form->passwordFieldGroup(
			$model,
			'password',
			[
				'widgetOptions' => [
					'htmlOptions' => ['maxlength' => 32]
				]
			]
		) ?>
	</div>

	<?php if ($model->photo): ?>
		<h3><a href="#">Фотографии салона <strong>(<?php echo count($model->photo); ?>)</strong></a></h3>

		<div>
			<a href="<?php echo $model->getSalonPhotoUrl(); ?>" target="_blank">
				Открыть в новом окне
			</a>
		</div>
	<?php endif; ?>

	<h3><a href="#">Мастера</a></h3>

	<div>
		<?php
		$this->widget(
			'likefifa\components\system\admin\YbGridView',
			array(
				'dataProvider' => $model->getDataProviderMasters(),
				'columns'      => array(
					'id',
					'name',
					'surname',
					array(
						'class'    => 'booster.widgets.TbButtonColumn',
						'template' => '{update}{delete}',
						'buttons'  => array(
							'update' => array(
								'url' => '$this->grid->controller->createUrl("admin/master/update", array(
											"id" => $data->primaryKey
										))',
							),
							'delete' => array(
								'url' => '$this->grid->controller->createUrl("admin/master/delete", array(
											"id" => $data->primaryKey
										))',
							),
						),
					),
				),
			)
		);
		?>

		<div class="form-group">
			<?php echo CHtml::link(
				'<i class="fa fa-plus"></i> Добавить мастера',
				Yii::app()->createUrl('admin/master/create', ['salon_id' => $model->id]),
				['class' => 'btn btn-success']
			) ?>
		</div>
	</div>
	</div>

	<br/>
	<?php echo $form->textFieldGroup(
		$model,
		'rating_inner',
		[
			'widgetOptions' => [
				'htmlOptions' => [
					'maxlenght' => 5,
					'style'     => 'width: 200px;'
				]
			]
		]
	) ?>
	<?php $this->widget(
		'booster.widgets.TbButton',
		[
			'buttonType' => 'submit',
			'context'    => 'primary',
			'label'      => $model->isNewRecord ? 'Создать' : 'Сохранить'
		]
	); ?>
	</div>
	</div>
<?php $this->endWidget(); ?>
<?php $this->endWidget(); ?>