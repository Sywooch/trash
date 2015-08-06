<?php
$this->breadcrumbs = array(
	'Заявки на снятие денег' => array('index'),
	'Просмотр заявки',
);
?>


<h1>Просмотр заявки №<?php echo $model->id; ?></h1>

<?php $form = $this->beginWidget(
	'CActiveForm',
	array(
		'id'                   => 'payment_money-form',
		'enableAjaxValidation' => false,
	)
); ?>

<div class="lk-personal">
	<table style="margin-left: 0;">
		<tr>
			<td class="label">ID Пользователя:</td>
			<td><?php echo $model->user_id; ?></td>
		</tr>
		<tr>
			<td class="label">Сумма снятия:</td>
			<td><strong><?php echo $model->withdrawal; ?>$</strong></td>
		</tr>
		<tr>
			<td class="label">Текущий баланс:</td>
			<td><?php echo $model->user->balance_personal; ?>$</td>
		</tr>
		<tr>
			<td class="label">Реквизиты:</td>
			<td><?php echo $model->text; ?></td>
		</tr>
	</table>
</div>

<?php if (!$model->is_read) { ?>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Обработать', array("name" => "edit")); ?>
	</div>
<?php } ?>

<?php $this->endWidget(); ?>