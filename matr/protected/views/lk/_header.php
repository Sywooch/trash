
<div style="float: right; padding-top: 10px; padding-right: 20px;">
<a href="/lk">В начало</a> | <a href="/logout">Выйти</a>
	</div>
<h1>Личный кабинет</h1>
<div>
	Реферальная ссылка: <?php echo $model->getReferral(); ?>
</div>
<?php $balance = $model->getBalance(); if ($balance) { ?>
	<div>Баланс: <?php echo $balance; ?> руб. <a href="/lk/get/"><strong>Получить деньги</strong></a></div>
<?php } ?>