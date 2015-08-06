<?php
$this->breadcrumbs = array(
	'Пользователи' => array('index'),
	'Просмотр',
);
?>


<h1>Просмотр пользователя №<?php echo $model->id; ?></h1>

<div class="lk-personal">
	<table>
		<tr>
			<td class="label">Пользователь:</td>
			<td><?php echo $model->name; ?></td>
		</tr>
		<tr>
			<td class="label">Идентификатор:</td>
			<td><strong><?php echo $model->id; ?></strong></td>
		</tr>
		<tr>
			<td class="label">Персональные средства:</td>
			<td><?php echo $model->balance_personal; ?>$</td>
		</tr>
	</table>
</div>

<div id="lk-accordion">
	<h3>Денежные поступления</h3>
	<div>
		<?php if ($model->operations) { ?> 
			<?php foreach ($model->operations as $operation) { ?>
				<div>- от пользователя с ID <strong><?php echo $operation->user_from; ?></strong>, сумма <strong><?php echo $operation->sum; ?>$</strong></div>
			<?php } ?>	
		<?php } else { ?>
			Нет поступлений.
		<?php } ?>
	</div>
	<h3>Дерево</h3>
	<div class="lk-tree">
		<?php $childs = $model->getTreeHtml(); ?>
		<? if ($childs) { ?>
			<?php echo $childs; ?>
		<?php } else { ?>
			Никого не найдено.
		<?php } ?>
	</div>
	<h3>Контактная информация</h3>
	<div>
		<?php 
			if (
				$model->skype
				|| $model->phone
				|| $model->city
			) {
		?>
			<?php if ($model->skype) { ?>
				<div><strong>Skype:</strong> <?php echo $model->skype; ?></div>
			<?php } ?>
			<?php if ($model->phone) { ?>
				<div><strong>Телефон:</strong> <?php echo $model->phone; ?></div>
			<?php } ?>
			<?php if ($model->city) { ?>
				<div><strong>Город:</strong> <?php echo $model->city; ?></div>
			<?php } ?>
		<?php } else { ?>
			Информация не указана.
		<?php } ?>
	</div>
</div>