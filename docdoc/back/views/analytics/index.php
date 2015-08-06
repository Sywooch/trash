<?php
use dfs\docdoc\reports\Report;
use dfs\docdoc\reports\RequestConversion;

/**
 * @var array $tableConfig
 * @var array $tabs
 * @var string $activeTab
 * @var Report $report
 */

$GLOBALS['jqueryPath'] = '/js/jquery/jquery.min.js';
?>

<div class="result_title__ct">
	<h1 class="result_main__title">Конверсия</h1>
</div>

<div style="float: left;">

	<div class="page_tabs">
		<ul>
			<?php foreach ($tabs as $name => $tab): ?>
				<li class="<?php echo $name === $activeTab ? 'tab_active' : ''; ?>">
					<a class="link" href="<?php echo $tab['url']; ?>"><?php echo $tab['title']; ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>

	<div class="result_filters patients">
		<form id="AnalyticsFiltersForm">
			<div style="float:left;">
				<?php if ($report instanceof RequestConversion): ?>
					<span>Дата заявок &nbsp; с</span>
					<input name="dateFrom" class="DatePicker date-filter" type="text" value="<?php echo $report->getPeriodBegin(); ?>" size="10">
					<span>по</span>
					<input name="dateTill" class="DatePicker date-filter" type="text" value="<?php echo $report->getPeriodEnd(); ?>" size="10">
					<br />
				<?php endif; ?>
			</div>
		</form>

		<div style="float:right;">
			<button id="exportInExcel">Экспорт в Excel</button>
		</div>
	</div>

	<?php
		$this->renderPartial('/../../front/views/elements/datatable', [
			'tableId' => 'AnalyticsTable',
			'filtersId' => 'AnalyticsFiltersForm',
			'tableConfig' => $tableConfig,
			'isInit' => false,
		]);
	?>

	<script>
		$(document).ready(function() {
			var tableConfig = <?php echo json_encode($tableConfig); ?>;
			var dt = new DataTableWrapper('#AnalyticsTable', '#AnalyticsFiltersForm', tableConfig);
			dt.init();

			$('#AnalyticsFiltersForm input.DatePicker').datetimepicker({
				timepicker: false,
				format: 'd.m.Y',
				lang:'ru',
				onClose: function (dp, $input) {
					dt.reload();
				},
				closeOnDateSelect: true
			});

			$('#exportInExcel').click(function() {
				location.href = dt.url + "?" + dt.$formFilters.serialize() + '&contentType=xls';
			});
		});
	</script>
</div>
