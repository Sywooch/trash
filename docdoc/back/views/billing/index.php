<?php
/**
 * @var array $tableConfig
 * @var string $dateFrom
 * @var string $dateTo
 * @var array $stat
 * @var string $withBranches
 * @var array $billingPeriods
 * @var \dfs\docdoc\models\ClinicContractModel $tariff
 * @var \dfs\docdoc\models\ContractModel[] $contracts
 */

$GLOBALS['jqueryPath'] = '/js/jquery/jquery.min.js';
?>

<div class="result_title__ct">
	<h1 class="result_main__title">Заявки в биллинге</h1>
</div>

<div id="BillingStatistic">
	<?php $this->renderPartial('statistics', [
		'dateFrom'     => $dateFrom,
		'dateTo'       => $dateTo,
		'tariff'       => $tariff,
		'stat'         => $stat,
	]); ?>
</div>

<div style="float: left;">

	<div class="dt_filters">
		<form id="BillingFiltersForm">
			<input type="hidden" name="clinicId" value="<?php echo $clinic ? $clinic->id : 0; ?>" class="dt_filter" />
			<input type="hidden" name="recalculate" value="" class="dt_filter" />

			<div class="filter inline">
				<label>
					Клиника
					<?php echo CHtml::textField('clinicName', $clinic ? $clinic->name : '', [ 'class' => 'clinic_autocomplete', 'size' => 60 ]); ?>
				</label>
			</div>

			<div class="filter inline">
				<label>
					Учитывать филиалы
					<?php echo CHtml::checkBox('withBranches', $withBranches, [ 'class' => 'dt_filter', 'uncheckValue' => '' ]); ?>
				</label>
			</div>

			<div class="clear"></div>

			<div class="filter inline">
				<label>
					Месяц
					<?php echo CHtml::dropDownList('dateFrom', $dateFrom, $billingPeriods, [ 'class' => 'dt_filter' ]); ?>
				</label>
			</div>

			<div class="filter inline">
				<label>
					Тариф
					<?php echo CHtml::dropDownList('contractId', $contractId, CHtml::listData($contracts, 'contract_id', 'title'), [ 'class' => 'dt_filter' ]); ?>
				</label>
			</div>

			<div class="buttons inline">
				<button id="recalculateCost">Пересчитать стоимость заявок</button>
			</div>
		</form>

		<div style="float:right;">
			<button id="exportInExcel">Экспорт в Excel</button>
		</div>
	</div>

	<?php
		$this->renderPartial('/../../front/views/elements/datatable', [
			'tableId' => 'BillingTable',
			'filtersId' => 'BillingFiltersForm',
			'tableConfig' => $tableConfig,
			'isInit' => false,
		]);
	?>

	<script>
		$(document).ready(function() {
			var tableConfig = <?php echo json_encode($tableConfig); ?>;
			var dt = new DataTableWrapper('#BillingTable', '#BillingFiltersForm', tableConfig);

			var showContractsForClinic = function(ids) {
				$('option', dt.$formFilters[0]['contractId']).each(function(index, option) {
					option.disabled = (!ids || $.inArray(parseInt(option.value, 10), ids) !== -1) ? '' : 'disabled';
				});
			};

			dt.dataTable.on('xhr.td', function (e, settings, json) {
				$('#BillingStatistic').html(json.statisticsHtml).show();
				showContractsForClinic(json.clinicContractIds);
			});

			dt.init();

			showContractsForClinic(tableConfig.clinicContractIds);

			$('#recalculateCost').click(function() {
				var recalculate = dt.$formFilters[0].recalculate;
				recalculate.value = '1';
				dt.reload();
				recalculate.value = '';
			});

			$('#exportInExcel').click(function() {
				location.href = dt.url + "?" + dt.$formFilters.serialize() + '&contentType=xls';
			});

			dt.$table.on('click', 'a.edit', function () {
				dt.action('edit', $(this).closest('td'));
				return false;
			});

			$('.clinic_autocomplete', dt.$formFilters).autocomplete({
				source: function( request, response ) {
					$.ajax({
						url: '/2.0/clinic/clinicAutocomplete',
						dataType: "json",
						data: { term: request.term },
						success: function( data ) { response( data ); }
					});
				},
				select: function (event, ui) {
					dt.$formFilters[0]['clinicId'].value = ui.item.id;
					dt.reload();
				}
			});
		});
	</script>

	<script src='/js/jquery-ui/jquery-ui.min.js' type='text/javascript' language="JavaScript"></script>
	<link rel="stylesheet" type="text/css" href="/js/jquery-ui/themes/smoothness/jquery-ui.css"/ >

</div>
