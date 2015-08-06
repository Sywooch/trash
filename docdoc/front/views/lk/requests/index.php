<?php
/**
 * @var string $title
 * @var array $branches
 * @var array $requestStates
 * @var array $requestPeriod
 * @var string $defaultPeriod
 * @var array $tableConfig
 * @var int $selectedClinic
 * @var bool $needProcessing
 */
?>

<div class="result_title__ct">
	<h1 class="result_main__title"><?php echo $title; ?></h1>
</div>

<br/>
<div id="totalParams" style="display:none"></div>
<br/>

<div class="result_filters">
	<form id="RequestsFiltersForm">
		<input type="hidden" name="lastId" value="">
		<?php
			if ($requestStates) {
				$this->renderPartial('/../filters/status', [
					'field' => 'reqStatus',
					'data' => $requestStates,
				]);
			}
		?>
		<br clear="all" />
		<div style="float:left">
			<?php
				$this->renderPartial('/../filters/date', [
						'field' => 'reqStatus',
						'defaultPeriod' => $defaultPeriod,
						'periods' => $requestPeriod,
						'dateLabel' => 'Дата биллинга',
					]);
			?>
		</div>
		<div style="float:right">
			<ul>
				<?php if (count($branches) > 1): ?>
				<li>
					<div class="filter_doctor">
						<label class="filter_doctor__label">
							Клиника
							<?php echo CHtml::dropDownList('clinic_id', $selectedClinic, $branches, [
									'empty' => 'Все филиалы',
									'class' => 'dt_filter',
								]);
							?>
						</label>
					</div>
				</li>
				<?php endif; ?>
				<?php if ($needProcessing): ?>
				<li>
					<div class="filter_doctor">
						<label class="filter_doctor__label">
							Способ обращения
							<?php echo CHtml::dropDownList('online', Yii::app()->request->getQuery('online'), [
									'yes' => 'Онлайн',
									'no' => 'Не онлайн',
								], [
									'empty' => 'Все',
									'class' => 'dt_filter',
								]);
							?>
						</label>
					</div>
				</li>
				<?php endif; ?>
				<li>
					<div class="filter_doctor">
						<label class="filter_doctor__label">
							Бронирование
							<?php echo CHtml::dropDownList('booking', null, [
								'yes' => 'Онлайн',
								'no' => 'Без бронирования',
							], [
								'empty' => 'Все',
								'class' => 'dt_filter',
							]);
							?>
						</label>
					</div>
				</li>
			</ul>
		</div>
		<br clear="all" />
	</form>
</div>

<br/>
<button id="exportInExcel">Экспорт в Excel</button>
<br/><br/>


<?php
$this->renderPartial('/../elements/datatable', [
		'tableId' => 'RequestsTable',
		'filtersId' => 'RequestsFiltersForm',
		'tableConfig' => $tableConfig,
		'isInit' => false,
	]);
?>

<script>
	$(document).ready(function() {
		var tableConfig = <?php echo json_encode($tableConfig); ?>;

		tableConfig.fields['req_type'].render = function(data, type, row) {
			return '<div class="i-status req_type i-st-' + row['req_type'] + '" title="' + row['req_type_name'] + '"></div>';
		};

		tableConfig.fields['date_admission'].render = function(data, type, row) {
			var html = row['date_admission'];
			if (row['booking_id']) {
				html += ' <span class="booking"><img src="/img/icons/slot_booking.png"></span>';
			}
			return html;
		};

		tableConfig.fields['status'].render = function(data, type, row) {
			var html = '';

			if (row['state']) {
				html += '<span class="state"><span class="i-states i-' + row['state']['class'] + '"></span>' + row['state']['status'] + '</span><br />';
			}

			if (row['records'] && row['records'].length) {
				html += '<div class="request_records-ct">' +
				'<span class="request_records__label">аудиозаписи</span>' +
				'<ul class="request_records">';

				row['records'].forEach(function(record) {
					if (record.duration != '') {
						var duration = Math.floor(record.duration/60);
						duration = duration + " мин. " + (record.duration - duration*60) + " сек.";
					} else {
						duration = '';
					}

					var url = '/lib/swf/docdoc.swf?file=' + record.url + '&as=0';
					html += '<li>' +
					'<div style="width: 200px; height: 20px; margin: 0; overflow: hidden; float: left;">' +
					'<object id="niftyPlayer" width="330" height="20" align="" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000">' +
					'<param value="' + url + '" name="movie"/>' +
					'<param value="high" name="quality"/>' +
					'<param value="#FFFFFF" name="bgcolor"/>' +
					'<embed width="330" height="20" pluginspage="http://www.macromedia.com/go/getflashplayer" swliveconnect="true" type="application/x-shockwave-flash" name="niftyPlayer" bgcolor="#FFFFFF" quality="high" src="' + url + '"/>' +
					'</object>' +
					'</div> ' + duration +
					'</li>';
				});

				html += '</ul></div>';
			}

			var actions = row['actions'];

			if (actions['change']) html += '<button class="action" data-action-type="change">Изменить</button> ';
			if (actions['accept']) html += '<button class="action" data-action-type="accept">Подтвердить</button> ';
			if (actions['refused']) html += '<button class="action" data-action-type="refused">Отклонить</button> ';
			if (actions['came']) html += '<button class="action_came" data-action-type="came">Дошёл</button> ';

			return html;
		};

		var dt = new DataTableWrapper('#RequestsTable', '#RequestsFiltersForm', tableConfig);

		// Обновление количества и стоимости заявок за месяц
		dt.dataTable.on('xhr.td', function (e, settings, json) {
			var html = '<p>Привлеченных пациентов: ' + json.data.length + ', к оплате ' + json.totalCount + ', на сумму ' + json.totalCost + ' р.</p>';
			if (json.onlineCount) {
				var percentage = Math.round(100 * json.onlineSuccessCount / json.onlineCount);
				var mark;

				if (percentage >= 80) {
					mark = '<span class="excellent">Оценка: отлично</span>';
				} else if (percentage >= 50) {
					mark = '<span class="good">Оценка: средне</span>';
				} else {
					mark = '<span class="bad">Оценка: ужасно</span>';
				}

				html += '<p>Из них онлайн: ' + json.onlineCount + '. Обработано в отведённые 15 минут: ' +
					json.onlineSuccessCount + ' или ' + percentage + '% от общего числа. ' + mark + '.</p>';

				if (json.avgProcessingTime) {
					html += '<p>Среднее время обработки онлайн заявок ' + json.avgProcessingTime + '.</p>';
				}
			}
			$('#totalParams').html(html).show();
		});

		dt.editor.on('submitComplete', function(e, type, row) {
			$(dt.currentNode).closest('tr').removeClass('new').addClass(row['DT_RowClass']);
		});

		dt.init();
	});
</script>
<script src="/js/lk/requests.js"></script>
