<?php
/**
* @var array $tableConfig
*/
?>
<div class="result_title__ct">
	<h1 class="result_main__title">Отзывы</h1>
</div>

<br/><br/>

<?php
$this->renderPartial('/../elements/datatable', [
	'tableId' => 'ReviewsTable',
	'tableConfig' => $tableConfig,
	'isInit' => true,
]);
?>
