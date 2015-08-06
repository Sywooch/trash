<?php
use likefifa\models\AdminModel;

/**
 * @var AppointmentController $this
 * @var LfAppointment         $model
 * @var CActiveForm           $form
 */

if ($model->date) {
	$app_date_date = date("d.m.Y", $model->date);
	$app_date_time = date("H:i", $model->date);
} else {
	$app_date_date = '';
	$app_date_time = '';
}

if ($model->control) {
	$app_control_date = date("d.m.Y", $model->control);
	$app_control_time = date("H:i", $model->control);
} else {
	$app_control_date = '';
	$app_control_time = '';
}
?>

<?php
$this->beginWidget(
	'likefifa\components\system\admin\YbBox',
	[
		'title'      => $this->pageTitle,
		'headerIcon' => 'fa fa-file',
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
echo $form->select2Group(
	$model,
	'status',
	[
		'widgetOptions' => [
			'data'    => $model->statusList,
			'options' => [
				'placeholder' => $model->getAttributeLabel('status'),
			]
		],
	]
);
if ($model->status == 30) {
	echo '<p><em>' . $model->reason . '</em></p>';
}
?>

<!-- Дата создания -->
<?php if (!$model->isNewRecord): ?>
	<div class="form-group">
		<label>Дата создания</label>
		<strong><?php echo date("H:i", $model->getNumericCreated()); ?></strong>
		<?php echo date("d.m.y", $model->getNumericCreated()); ?>
	</div>
<?php endif; ?>

<!-- Дата приема -->
<div class="form-group">
	<?php echo $form->labelEx($model, 'date'); ?>
	<div class="form-inline">
		<div class="form-group">
			<div class="input-group">
				<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
				<?php $this->widget(
					'likefifa\components\system\admin\YbDatePicker',
					[
						'id'          => 'date-date-value',
						'name'        => 'date-date-value',
						'value'       => $app_date_date,
						'htmlOptions' => ['style' => 'width:110px; min-width: 110px;'],
					]
				); ?>
			</div>
		</div>
		<div class="form-group">
			<?php
			$this->widget(
				'booster.widgets.TbTimePicker',
				[
					'name'               => 'date_time',
					'value'              => $app_date_time,
					'options'            => [
						'disableFocus' => true,
						'showMeridian' => false,
					],
					'htmlOptions'        => ['style' => 'width:65px; min-width: 65px;', 'class' => 'date_time'],
					'wrapperHtmlOptions' => ['class' => 'col-md-3'],
				]
			);
			?>
		</div>
	</div>
	<?php echo $form->hiddenField($model, 'date') ?>
</div>

<!-- Дата контроля -->
<div class="form-group">
	<?php echo $form->labelEx($model, 'control'); ?>
	<div class="form-inline">
		<div class="form-group">
			<div class="input-group">
				<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
				<?php $this->widget(
					'likefifa\components\system\admin\YbDatePicker',
					[
						'id'          => 'control-date-value',
						'name'        => 'control-date-value',
						'value'       => $app_control_date,
						'htmlOptions' => ['style' => 'width:110px; min-width: 110px;'],
					]
				); ?>
			</div>
		</div>
		<div class="form-group">
			<?php
			$this->widget(
				'booster.widgets.TbTimePicker',
				[
					'name'               => 'control_time',
					'value'              => $app_control_time,
					'options'            => [
						'disableFocus' => true,
						'showMeridian' => false,
					],
					'htmlOptions'        => [
						'style' => 'width:65px; min-width: 65px;',
						'class' => 'control_time'
					],
					'wrapperHtmlOptions' => ['class' => 'col-md-3'],
				]
			);
			?>
		</div>
	</div>
	<?php echo $form->hiddenField($model, 'control') ?>
</div>

<!-- Выбор мастера -->
<?php if (!$model->isNewRecord && ($model->master_id != null || $model->salon_id != null)): ?>
	<div class="form-group">
		<?php if ($model->master_id != null): ?>
			<?php echo $form->labelEx($model, 'master_id'); ?>
			<span class="a_id_<?php echo $model->id; ?>">
					<?php echo CHtml::link(
						$model->master->getFullName(),
						$model->master->getProfileUrl(),
						[
							'target' => '_blank'
						]
					) ?>
				</span>
		<?php else: ?>
			<?php echo $form->labelEx($model, 'salon_id'); ?>
			<span class="a_id_<?php echo $model->id; ?>">
						<?php echo CHtml::link(
							$model->salon->getFullName(),
							$model->salon->getProfileUrl(),
							[
								'target' => '_blank'
							]
						) ?>
				</span>
		<?php endif; ?>

		<a
			href="#"
			title="Перевести на другого мастера/салон"
			data-toggle="tooltip"
			appointment="<?php echo $model->id; ?>"
			class="on_change fa fa-pencil"
			></a>
	</div>
<?php else: ?>
	<?php echo $form->select2Group(
		$model,
		'master_id',
		[
			'widgetOptions' => [
				'data'        => LfMaster::model()->getListItems(false, 0, true),
				'options'     => [
					'placeholder' => 'Выберите мастера',
				],
				'htmlOptions' => [
					'class'     => 'masters-drop-down',
					'data-type' => 'master',
				]
			],
		]
	); ?>

	<?php echo $form->select2Group(
		$model,
		'salon_id',
		[
			'widgetOptions' => [
				'data'        => LfSalon::model()->getListItems(false, 0, true),
				'options'     => [
					'placeholder' => 'Выберите салон',
				],
				'htmlOptions' => [
					'class'     => 'masters-drop-down',
					'data-type' => 'salon',
				]
			],
		]
	); ?>
<?php endif; ?>

<!-- Телефон мастера -->
<div class="form-group">
	<?php echo $form->labelEx($model, 'master_tel'); ?>
	<div class="appointment-master-phone">
		<?php if ($model->master_id): ?>
			<?php echo $model->master->phone_cell; ?>
		<?php endif; ?>
	</div>
</div>

<?php if (!$model->isNewRecord && $model->underground_station_id != null): ?>
	<div class="row">
		<strong>Желаемое местоположение: </strong>
		<span
			style="color:#<?php echo $model->undergroundStation->undergroundLine->color ?>"><?php echo $model->undergroundStation->name ?></span>
	</div>
<?php endif; ?>

<?php echo $form->textFieldGroup($model, 'name') ?>
<?php echo $form->maskedTextFieldGroup(
	$model,
	'phone',
	['widgetOptions' => ['mask' => '+7 (999) 999 99 99']]
) ?>

<?php if ($model->departure): ?>
	<div class="form-group">
		<label>Адрес выезда к клиенту</label>
		<?php echo $model->address; ?>
	</div>
<?php endif; ?>

<!-- Название услуги -->
<?php if (!$model->isNewRecord && $model->master_id): ?>
	<?php echo $form->select2Group(
		$model,
		'service_id',
		[
			'widgetOptions' => [
				'data' => $model->master->getServiceListItems(),
			],
		]
	); ?>
<?php else: ?>
	<div class="form-group">
		<?php echo $form->labelEx($model, 'service_name'); ?>
		<div class="appointment-master-services"></div>
	</div>
<?php endif; ?>

<!-- Стоимость услуги -->
<?php if (!$model->isNewRecord && $model->master_id): ?>
	<?php echo $form->textFieldGroup(
		$model,
		'service_price',
		[
			'widgetOptions' => [
				'size'  => 6,
				'value' => $model->getPrice() ? $model->getPrice()->price : ''
			]
		]
	) ?>
	<?php echo $form->textFieldGroup(
		$model,
		'service_price2',
		[
			'widgetOptions' => [
				'size'  => 6,
				"value" => $model->getServicePrice2()
			]
		]
	) ?>
<?php else: ?>
	<div class="form-group">
		<label>Цена</label>

		<div class="appointment-service-price"></div>
		<?php echo $form->hiddenField($model, 'service_price'); ?>
	</div>
<?php endif; ?>

<?php echo $form->textFieldGroup($model, 'reason') ?>

<?php if ($model->isCurrentAdmin()): ?>
	<?php echo $form->hiddenField($model, 'admin_id', ["value" => AdminModel::getModel()->id]) ?>
<?php endif; ?>

<?php if (AdminModel::getModel()->isFullAccess()): ?>
	<?php echo $form->select2Group(
		$model,
		'admin_id',
		[
			'widgetOptions' => [
				'data'    => AdminModel::model()->getOperatorList(),
				'options' => [
					'placeholder' => $model->getAttributeLabel('admin_id'),
				]
			],
		]
	); ?>
<?php endif; ?>

<?php echo $form->textFieldGroup($model, 'operator_comment') ?>

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
<?php
$this->endWidget();
$this->endWidget();
?>

<script>
	$(function () {

		function get_app_date(attribute) {
			var date_date = $('#' + attribute + '-date-value').val();
			var date_time = $('.' + attribute + '_time').val();
			if (date_date) {
				var y = date_date[6] + date_date[7] + date_date[8] + date_date[9];
				var m = parseInt(date_date[3] + date_date[4]) - 1;
				var d1 = parseInt(date_date[0] + date_date[1]);
				var h = parseInt(date_time[0] + date_time[1]);
				var i = parseInt(date_time[3] + date_time[4]);
				var d = new Date(y, m, d1, h, i);

				var statusField = $('#LfAppointment_status');
				if (attribute == 'date' && statusField.val() == 0) {
					statusField.val(40);
				}

				return (d.getTime() / 1000);
			}
			return "";
		}

		$(".datepicker").datepicker({dateFormat: "dd.mm.yy"});
		$('#LfAppointment_date').val(get_app_date('date'));
		$('#date-date-value').on('change', function () {
			$('#LfAppointment_date').val(get_app_date('date'));
		});
		$('.date_time').on('change', function () {
			$('#LfAppointment_date').val(get_app_date('date'));
		});

		$('#control-date-value').on('change', function () {
			$('#LfAppointment_control').val(get_app_date('control'));
		});
		$('.control_time').on('change', function () {
			$('#LfAppointment_control').val(get_app_date('control'));
		});

		$('.submit_b input').on('click', function () {
			pr1 = parseInt($('#LfAppointment_service_price').val());
			pr2 = parseInt($('#LfAppointment_service_price2').val());
			if (pr2 < pr1) {
				alert('Конечная цена не может быть меньше начальной!');
				return false;
			}
			return true;
		});

	});

	var initMasterService = false;
	<?php if($model->isNewRecord && $model->master_id != null): ?>
	initMasterService = true;
	<?php endif; ?>
</script>