<?php
/**
 * @var array $stat
 */
?>
<div class="alert alert-warning">
<h4>Итого</h4>
<p><b>всего заявок</b> <?=$stat['totalNum'] . " / " . $stat['totalCost']?>,
	из них: <b>на сегодня</b> <?=$stat['totalTodayNum'] . " / " . $stat['totalTodayCost']?>,
	<b>получено</b> <?=$stat['totalRecieved']?>,
	<b>долг</b>  <?=$stat['totalCredit']?>
	<br/><b>Контроль</b> <?=$stat['totalRequestNumInBillingControl']?> / <?=$stat['totalRequestCostInBillingControl']?>
</p>
</div>

