<?php $this->renderPartial("_header", compact("model")); ?>

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
	<h3>Мое дерево</h3>
	<div class="lk-tree">
		<?php $childs = $model->getTreeHtml(); ?>
		<? if ($childs) { ?>
			<?php echo $childs; ?>
		<?php } else { ?>
			Никого не найдено. Сообщайте пользователям свою реферальную ссылку (см. выше)
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
		<p><a href="/lk/contacts/">Редактировать</a></p>
	</div>
</div>