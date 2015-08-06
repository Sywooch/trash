<?php $this->renderPartial("_header", compact("model")); ?>

<div id="lk-accordion" style="width: 640px; margin-top: 20px;">
	<?php
	if (
	!User::model()->find("t.group_number = {$model->group_number} AND t.type = 0 AND t.is_active = 1")
	|| !User::model()->find("t.group_number = {$model->group_number} AND t.type = 1 AND t.is_active = 1")
	|| !User::model()->find("t.group_number = {$model->group_number} AND t.type = 2 AND t.is_active = 1")
	) {
	?>
	<h3>Купить матрешку и стать участником программы</h3>
	<div>
		<?php if (!User::model()->find("t.group_number = {$model->group_number} AND t.type = 0 AND t.is_active = 1")) { ?>
			<a href="/lk/payment/?type=0">Матрешка "Россияночка" 7 кукольная - 1500р</a> <br>
			Матрешка выполнена в стиле традиционной семеновской росписи органическими красителями,покрыта лаком.<br>
			Высота - 16 см<br>
			(ручная работа)<br><br>
		<?php } ?>

		<?php if (!User::model()->find("t.group_number = {$model->group_number} AND t.type = 1 AND t.is_active = 1")) { ?>
		<a href="/lk/payment/?type=1">Матрешка "Россияночка" 15 кукольная - 3500р</a> <br>
		Матрешка выполнена в стиле традиционной семеновской росписи органическими красителями,покрыта лаком.<br>
		Высота- 31 см<br>
		(ручная работа)<br>
		Класическая<br><br>
		<?php } ?>

		<?php if (!User::model()->find("t.group_number = {$model->group_number} AND t.type = 2 AND t.is_active = 1")) { ?>
		<a href="/lk/payment/?type=2">Сувенирная продукция магазина - 5500р</a><br>
		Сувенирная продукция магазина каталог скоро появится каталог где будут представлены товары на выбор!
		<?php } ?>
	</div>
	<?php } ?>
	<?php
	if (
		User::model()->find("t.group_number = {$model->group_number} AND t.type = 0 AND t.is_active = 1")
		|| User::model()->find("t.group_number = {$model->group_number} AND t.type = 1 AND t.is_active = 1")
		|| User::model()->find("t.group_number = {$model->group_number} AND t.type = 2 AND t.is_active = 1")
	) {
		?>
		<h3>Купленные матрешки</h3>
		<div>
			<?php if ($m =
				User::model()->find("t.group_number = {$model->group_number} AND t.type = 0 AND t.is_active = 1")
			) { ?>
				- Матрешка "Россияночка" 7 кукольная - 1500р<br>
			<?php } ?>
			<?php if ($m =
				User::model()->find("t.group_number = {$model->group_number} AND t.type = 1 AND t.is_active = 1")
			) { ?>
				- Матрешка "Россияночка" 15 кукольная - 3500р<br>
			<?php } ?>
			<?php if ($m =
				User::model()->find("t.group_number = {$model->group_number} AND t.type = 2 AND t.is_active = 1")
			) { ?>
				- Сувенирная продукция магазина - 5500р<br>
			<?php } ?>
		</div>
	<?php } ?>
	<h3>Денежные поступления</h3>
	<div>
		<?php foreach (User::model()->findAll("t.group_number = {$model->group_number}") as $usr) { ?>
		<?php if ($usr->operations) { ?>
			<?php foreach ($usr->operations as $operation) { ?>
				<div>- от пользователя с ID <strong><?php echo $operation->user_from; ?></strong>, сумма <strong><?php echo $operation->sum * DOLLAR; ?> руб.</strong></div>
			<?php } ?>	
		<?php } ?>
		<?php } ?>
	</div>
	<h3>Мои деревья</h3>
	<div class="lk-tree">
		<?php foreach (User::model()->findAll("t.group_number = {$model->group_number}") as $usr) { ?>
			<div style="padding-bottom: 20px;">
				<?php $childs = $usr->getTreeHtml(); ?>
				<? if ($childs) { ?>
					<?php echo $childs; ?>
				<?php } ?>
			</div>
		<?php } ?>

		Сообщайте пользователям свою реферальную ссылку (см. выше)
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