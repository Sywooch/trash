<?php
/**
 * @var int $selectedClinic
 * @var array $branches
 * @var array $tableConfig
 */

$hostStatic = 'https://' . Yii::app()->params['hosts']['static'];
?>

<div class="result_title__ct">
	<h1 class="result_main__title">Врачи</h1>
</div>

<br/>
<div class="result_filters">
	<form id="DiagnosticsFiltersForm">
		<?php if (count($branches) > 1): ?>
			<div>
				<span class="filter_doctor__label-txt">Клиника</span>
				<?php
					echo CHtml::dropDownList('clinic_id', $selectedClinic, $branches, [
						'empty' => 'Все филиалы',
						'class' => 'dt_filter',
					]);
				?>
			</div>
		<?php endif; ?>
	</form>
</div>

<br/>
<button id="newDiagnostic">Добавить диагностику</button>&nbsp;&nbsp;&nbsp;
<button id="exportInExcel">Экспорт в Excel</button>
<br/><br/>



<?php
	$this->renderPartial('/../elements/datatable', [
		'tableId' => 'DiagnosticsTable',
		'tableConfig' => $tableConfig,
		'isInit' => false,
	]);
?>

<script>
	$(document).ready(function() {
		var tableConfig = <?php echo json_encode($tableConfig); ?>;
		var dt = new DataTableWrapper('#DiagnosticsTable', '#DiagnosticsFiltersForm', tableConfig);
		dt.init();
	});
</script>
<link rel="stylesheet" type="text/css" href="<?php echo $hostStatic; ?>/js/chosen/chosen.min.css"/ >
<script src="<?php echo $hostStatic; ?>/js/chosen/chosen.jquery.min.js"></script>
<script src="/js/lk/diagnostics.js"></script>
