
<div class="result_title__ct">
	<h1 class="result_main__title">Заявки</h1>
</div>

<div class="result_filters patients">
	<form id="PatientsFiltersForm">
		<input type="hidden" name="lastId" value="">
		<br clear="all" />
		<div style="float:left">
			<?php
				$this->renderPartial('/../filters/date', [
						'field' => 'reqStatus',
						'defaultPeriod' => $defaultPeriod,
						'periods' => $requestPeriod,
						'dateLabel' => 'Дата заявки',
					]);
			?>
		</div>
		<div style="float:right">
			<?php
				$this->renderPartial('/../filters/selects', [
					'filters' => [
						'reqKind' => [
							'label' => 'Тип',
							'data' => $kinds,
						],
						'reqStatus' => [
							'label' => 'Статус',
							'data' => array_combine(array_keys($requestStates), array_column($requestStates, 'title')),
						],
						'reqType' => [
							'label' => 'Способ обращения',
							'data' => $requestTypes,
						],
					]
				]);
			?>
		</div>
		<br clear="all" />
	</form>
</div>

<br/>
<button id="exportInExcel">Экспорт в Excel</button>
<br/><br/>

<?php
	$this->renderPartial('/../elements/datatable', [
			'tableId' => 'PatientsTable',
			'filtersId' => 'PatientsFiltersForm',
			'tableConfig' => $tableConfig,
			'isInit' => false,
		]);
?>

<script>
	$(document).ready(function() {
		var tableConfig = <?php echo json_encode($tableConfig); ?>;

		tableConfig.fields['partner_status'].render = function(data, type, row) {
			var html = '';

			if (data['state']) {
				var isReject = data['state']['name'] === 'reject';
				html += '<span class="state"' + (isReject ? ' title="' + data['reject_reason'] + '" style="cursor:help; color:red;"' : '') + '>';
				if (!isReject) html += '<span class="i-states i-' + data['state']['class'] + '"></span>';
				html += data['state']['title'];
				html += '</span><br />';
			}

			return html;
		};

		var dt = new DataTableWrapper('#PatientsTable', '#PatientsFiltersForm', tableConfig);
		dt.init();

		$('input.DatePicker').datetimepicker({
			timepicker: false,
			format: 'd.m.Y',
			lang:'ru',
			onClose: function (dp, $input) {
				dt.reload();
			},
			closeOnDateSelect: true
		});

		$('#exportInExcel').click(function() {
			location.href = dt.url + "?" + dt.$formFilters.serialize() + '&type=xls';
		});

		// Обновление количества и стоимости заявок за выбранный период
		dt.dataTable.on('xhr.td', function (e, settings, json) {
			var wordRequests = [ 'заявка', 'заявки', 'заявок' ];
			var params = {
				'totalDoctor': 'заявки на подбор врача',
				'totalDiagnosticsMrtKt': 'запись на МРТ/КТ',
				'totalDiagnosticsOther': 'запись на диагностику (кроме МРТ/КТ)'
			};

			var html = '';
			html += '<label class="help_label">Статистика с ' + json.from + ' по ' + json.to + '</label>';
			html += '<p>Всего заявок: ' + json.allCount + '</p>';
			html += '<p>Подтверждённых заявок: ' + json.total.count + '</p>';
			html += '<p>Заработано: ' + json.total.cost + ' р., из них:</p>';
			html += '<ul>';
			$.each(json, function(key, row) {
				if (params[key] && row) {
					html += '<li>' + params[key] + ' - ' + row.cost + ' р. (';
					html += row.count + ' ' + declension(row.count, wordRequests);
					html += ')</li>';
				}
			});
			html += '</ul>';

			$('#PartnerStatistics').html(html).show();
		});
	});
</script>
