<?php
/**
 * @var array $tableConfig
 */
?>

<div class="result_title__ct">
	<h1 class="result_main__title">Тарифы</h1>
</div>

<br/>

<?php
$this->renderPartial('/../elements/datatable', [
	'tableId' => 'ContractsTable',
	'tableConfig' => $tableConfig,
	'isInit' => true,
]);
?>
