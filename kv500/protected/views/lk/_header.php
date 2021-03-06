<div class="lk-title">Личный кабинет</div>

<div class="lk-personal">
	<table>
		<tr>
			<td class="label">Пользователь:</td>
			<td><?php echo $model->name; ?></td>
		</tr>
		<?php if ($model->getReferral()) { ?>
			<tr>
				<td class="label">Реферальная ссылка:</td>
				<td><?php echo $model->getReferral(); ?></td>
			</tr>
		<?php } ?>
		<tr>
			<td class="label">Скидка в <a href="/shop/">магазине</a>:</td>
			<td><?php echo $model->getDiscount(); ?>%</td>
		</tr>
		<tr>
			<td class="label">Статус:</td>
			<td>
				<?php if ($model->getStatus()) { ?>
					Активно до <?php echo $model->getStatus(); ?>. <a href="/lk/payment/">Продлить</a>
				<?php } else { ?>
					Не активно. <a href="/lk/payment/">Активировать</a>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td class="label">Персональные средства:</td>
			<td>
				<?php echo $model->balance_personal; ?>$
				<?php if ($model->balance_personal) { ?>
					<a href="/lk/get/">Получить деньги</a>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td class="label">Сумма для покупок в <a href="/shop/">магазине</a>:</td>
			<td><?php echo $model->balance_shop; ?>$</td>
		</tr>
	</table>
</div>