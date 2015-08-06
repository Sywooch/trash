<?php
/**
 * @var MasterController $this
 * @var LfMaster         $model
 */

use likefifa\models\CityModel;

Yii::import('application.components.system.front.LfHtml');

if (Yii::app()->request->getQuery("salon_id") != null) {
	$model->salon_id = Yii::app()->request->getQuery("salon_id");
}

$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'      => $this->pageTitle,
		'headerIcon' => 'fa fa-female',
	]
);

/** @var TbActiveForm $form */
$form = $this->beginWidget(
	'likefifa\components\system\admin\YbActiveForm',
	[
		'id'          => 'masterForm',
		'htmlOptions' => [
			'class'   => 'container-fluid',
			'enctype' => 'multipart/form-data',
		],
	]
);
?>
	<?php echo $form->errorSummary($model); ?>
	<div class="row">
	<div class="col-md-7">

	<?php if (!$model->isNewRecord): ?>
		<p>
			<a href="<?php echo $model->getModelUrl(); ?>" target="_blank">Публичный профиль</a>
			<?php if ($model->getSocialLink()) { ?>
				| <a href="<?php echo $model->getSocialLink(); ?>" target="_blank">Мастер в социальной сети</a>
			<?php } ?>
		</p>
		<p>
			<strong>Баланс:</strong> <?php echo $model->getBalance(); ?>
		</p>
	<?php endif; ?>

	<?php echo $form->checkboxGroup($model, 'is_published') ?>
	<?php echo $form->checkboxGroup($model, 'is_blocked') ?>

	<div class="master-accordion">
	<h3><a href="#">Личная информация</a></h3>

	<div>
		<?php echo $form->textFieldGroup($model, 'name') ?>
		<?php echo $form->textFieldGroup($model, 'surname') ?>
		<?php echo $form->maskedTextFieldGroup(
			$model,
			'phone_cell',
			['widgetOptions' => ['mask' => '+7 (999) 999 99 99']]
		) ?>
		<?php echo $form->textFieldGroup($model, 'rewrite_name') ?>
		<?php echo $form->emailFieldGroup($model, 'email') ?>

		<div class="form-group">
			<?php echo $form->labelEx($model, 'rating'); ?>
			<?php echo $model->rating; ?>
		</div>

		<div class="form-group">
			<?php echo $form->labelEx($model, 'rating_diff'); ?>
			<?php echo $form->textField($model, 'rating_diff', ['size' => 5, 'class' => 'form-control']); ?>
			<i>например, -0.5, -2, 0.7, 3</i>
			<?php echo $form->error($model, 'rating_diff'); ?>
		</div>

		<div class="row">
			<?php echo $form->labelEx($model, 'group'); ?>
			<?php echo LfHtml::checkBoxList(
				"LfMaster[groupIds]",
				CHtml::listData($model->masterGroups, 'group_id', 'group_id'),
				LfGroup::model()->getListItems()
			); ?>
		</div>

		<?php echo $form->select2Group(
			$model,
			'experience',
			[
				'widgetOptions' => [
					'data' => $model->getExperienceListItems(),
				],
			]
		); ?>

		<div class="form-group">
			<?php echo $form->labelEx($model, 'birth_date'); ?>
			<div class="form-inline">
				<div class="form-group">
					<div class="input-group">
						<?php echo $form->dropDownList($model, 'birth_day', range(1, 31)); ?>
						<?php echo $form->dropDownList($model, 'birth_month', $model->getMonthListItems()); ?>
						<?php echo $form->dropDownList($model, 'birth_year', range(1920, intval(date('Y')))); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="form-group">
			<?php echo $form->labelEx($model, 'photo'); ?>
			<?php echo $form->fileField($model, 'photo'); ?>
			<?php echo $form->error($model, 'photo'); ?>

			<?php if ($model->isUploaded('photo')) { ?>
				<p>
					<img
						src="<?php echo $model->avatar(); ?>?<?php echo rand(); ?>"
						width="100"
						alt="<?php echo $model->photo ?>"
						title="<?php echo $model->photo ?>"
						/>
					<br/>
					<?php echo
						$model->photo . ', ' . RussianTextUtils::fileSize($model->getFilesize('photo')) ?>
				</p>
			<?php } ?>
		</div>

		<?php echo $form->checkboxGroup($model, 'delete_photo') ?>

		<?php echo $form->select2Group(
			$model,
			'gender',
			[
				'widgetOptions' => [
					'data' => $model->getGenderListItems(),
				],
			]
		); ?>
	</div>

	<h3><a href="#">Адрес</a></h3>

	<div>
		<?php echo $form->select2Group(
			$model,
			'city_id',
			[
				'widgetOptions' => [
					'data'        => CHtml::listData(
						CityModel::model()->active()->orderByName()->findAll(),
						'id',
						'name'
					),
					'htmlOptions' => [
						'empty' => '',
					]
				],
			]
		); ?>

		<?php $form->textFieldGroup($model, 'add_street') ?>
		<?php $form->textFieldGroup($model, 'add_house') ?>
		<?php $form->textFieldGroup($model, 'add_korp') ?>
		<?php $form->textFieldGroup($model, 'add_flat') ?>

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

		<div class="form-group">
			<label>Координаты</label>

			<div class="form-inline">
				<div class="form-group">
					<div class="input-group">
						<?php echo $form->textField(
							$model,
							'map_lat',
							['placeholder' => $model->getAttributeLabel('map_lat')]
						) ?>
						<?php echo $form->textField(
							$model,
							'map_lng',
							['placeholder' => $model->getAttributeLabel('map_lng')]
						) ?>
					</div>
				</div>
			</div>
		</div>

		<?php echo $form->checkboxGroup($model, 'has_departure') ?>
		<?php echo $form->checkboxGroup($model, 'departure_to_all') ?>

		<div class="form-group chk-list">
			<?php echo CHtml::checkBoxList(
				'LfMaster[departureDistrictIds]',
				$model->getRelationIds('departureDistricts'),
				DistrictMoscow::model()->getListItems()
			); ?>
		</div>
	</div>

	<h3><a href="#">Прайс-лист</a></h3>

	<div>
		<?php
		$tree = LfSpecialization::model()->getTree($model);
		$serviceIds = $model->getRelationIds('services');
		foreach ($tree as $treeGroup) {
			?>
			<div class="price-group-title-lk">Услуги <?php echo $treeGroup["genitive_one"]; ?>:</div>
			<?php foreach ($treeGroup["spec"] as $specId => $spec) { ?>
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
										'LfMaster[prices][serviceIds][' . $serviceId . ']',
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
										<?php echo
											$serviceParams['name'] .
											(!empty($serviceParams['unit']) ? ' (' . $serviceParams['unit'] . ')'
												: ''); ?>
									</label>
									<?php echo CHtml::textField(
										'LfMaster[prices][values][' . $serviceId . ']',
										$price ? $price->price : null,
										array("size" => 5)
									);?>
									рублей
									<?php echo LfHtml::checkBox(
										'LfMaster[prices][isFrom][' . $serviceId . ']',
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
		<?php } ?>
	</div>

	<h3><a href="#">Образование</a></h3>

	<div>
		<div class="educations-master">
			<?php
			if ($model->educations) {
				foreach ($model->educations as $education) {
					?>
					<div class="education-container">
						<div class="close glyphicon glyphicon-remove"></div>
						<input class="education-id" type="hidden" value="<?php echo $education->id; ?>"
							   name="education[INDEX][id]">

						<div class="form-group">
							<?php echo $form->labelEx($model, 'edu_organization'); ?>
							<input type="text" value="<?php echo $education->organization; ?>" size="100"
								   class="form-control"
								   name="education[INDEX][organization]"/>
						</div>
						<div class="form-group">
							<?php echo $form->labelEx($model, 'edu_fak'); ?>
							<input type="text" value="<?php echo $education->course; ?>" size="100" class="form-control"
								   name="education[INDEX][course]"/>
						</div>
						<div class="form-group">
							<?php echo $form->labelEx($model, 'edu_spec'); ?>
							<input type="text" value="<?php echo $education->specialization; ?>" size="100"
								   class="form-control"
								   name="education[INDEX][specialization]"/>
						</div>
						<div class="form-group">
							<?php echo $form->labelEx($model, 'edu_year'); ?>
							<input type="text" value="<?php echo $education->graduation_year; ?>" size="5"
								   class="form-control"
								   name="education[INDEX][graduation_year]"/>
						</div>
					</div>
				<?php
				}
			}
			?>
			<div class="education-container-example">
				<div class="education-container">
					<div class="close glyphicon glyphicon-remove"></div>
					<input class="education-id" type="hidden" value="" name="education[INDEX][id]">

					<div class="form-group">
						<?php echo $form->labelEx($model, 'edu_organization'); ?>
						<input type="text" value="" size="100" name="education[INDEX][organization]"
							   class="form-control"/>
					</div>
					<div class="form-group">
						<?php echo $form->labelEx($model, 'edu_fak'); ?>
						<input type="text" value="" size="100" name="education[INDEX][course]" class="form-control"/>
					</div>
					<div class="form-group">
						<?php echo $form->labelEx($model, 'edu_spec'); ?>
						<input type="text" value="" size="100" name="education[INDEX][specialization]"
							   class="form-control"/>
					</div>
					<div class="form-group">
						<?php echo $form->labelEx($model, 'edu_year'); ?>
						<input type="text" value="" size="5" name="education[INDEX][graduation_year]"
							   class="form-control"/>
					</div>
				</div>
			</div>
		</div>
		<button class="education-add">Добавить образование</button>

		<p>&nbsp;</p>

		<?php echo $form->textAreaGroup($model, 'achievements') ?>
	</div>

	<h3><a href="#">График работы</a></h3>

	<div>
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

	<h3><a href="#">Прочее</a></h3>

	<div>
		<?php echo $form->textAreaGroup($model, 'add_info') ?>

		<?php echo $form->select2Group(
			$model,
			'salon_id',
			[
				'widgetOptions' => [
					'data'    => LfSalon::model()->getListItems(),
					'options' => [
						'placeholder' => 'Выберите салон',
					],
				],
			]
		); ?>
	</div>

	<?php if (count($model->works) > 0): ?>
		<h3><a href="#">Прочее</a></h3>

		<div>
			<div class="form-group-content">
				<a href="<?php echo $model->getWorksUrl(); ?>" target="_blank">
					Открыть в новом окне
				</a>
			</div>
		</div>
	<?php endif; ?>

	</div>

	<?php echo $form->textAreaGroup($model, 'comment') ?>

	<?php $this->widget(
		'booster.widgets.TbButton',
		[
			'buttonType' => 'submit',
			'context'    => 'primary',
			'label'      => $model->isNewRecord ? 'Создать' : 'Сохранить'
		]
	); ?>

	<div class="stay-here-container">
		<?php echo CHtml::checkBox('LfMaster_stay-here', false, ["class" => "inline-block"]); ?>
		<?php echo $form->labelEx($model, 'stay-here', ["class" => "inline-block"]); ?>
	</div>

	</div>
	</div>
<?php $this->endWidget(); ?>
<?php $this->endWidget(); ?>