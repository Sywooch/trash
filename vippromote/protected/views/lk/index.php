<?php $this->renderPartial("_header", compact("model")); ?>

<div id="lk-accordion">
	<h3>Денежные поступления</h3>
	<div>
		<?php if ($model->operations) { ?> 
			<?php foreach ($model->operations as $operation) { ?>
				<div>- от пользователя с ID <strong><?php echo $operation->user_from; ?></strong>, сумма <strong><?php echo $operation->sum * DOLLAR; ?> руб.</strong></div>
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
	<h3>Презентация VIP-promote</h3>
	<div class="lk-tree">
		<p><a href="/files/presentation.pdf">Скачать презентацию VIP-promote.pdf</a></p>
		<p style="text-align: center;"><img src="/images/presentation/1.JPG" style="max-width: 100%;" /></p>
		<p style="text-align: center;"><img src="/images/presentation/2.JPG" style="max-width: 100%;" /></p>
		<p style="text-align: center;"><img src="/images/presentation/3.JPG" style="max-width: 100%;" /></p>
		<p style="text-align: center;"><img src="/images/presentation/4.JPG" style="max-width: 100%;" /></p>
		<p style="text-align: center;"><img src="/images/presentation/5.JPG" style="max-width: 100%;" /></p>
	</div>
	<h3>Контактная информация</h3>
	<div>
		<?php 
			if (
				$model->skype
				|| $model->phone
				|| $model->city
				|| $model->perfect
				|| $model->payer
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
			<?php if ($model->perfect) { ?>
				<div><strong>Кошелек PerfectMoney:</strong> <?php echo $model->perfect; ?></div>
			<?php } ?>
			<?php if ($model->payer) { ?>
				<div><strong>Кошелек Payeer:</strong> <?php echo $model->payer; ?></div>
			<?php } ?>
		<?php } else { ?>
			Информация не указана.
		<?php } ?>
		<p><a href="/lk/contacts/">Редактировать</a></p>
	</div>
</div>