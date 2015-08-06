<?php
/**
 * @var array $tableConfig
 * @var string $dateFrom
 * @var array $billingPeriods
 * @var \dfs\docdoc\models\ClinicContractModel $tariff
 * @var \dfs\docdoc\models\ContractModel[] $contracts
 * @var array $stat
 * @var int cityId
 * @var array $cities
 * @var array $managers
 * @var \dfs\docdoc\models\UserModel[] $managerId
 *
 */

$GLOBALS['jqueryPath'] = '/js/jquery/jquery.min.js';
Yii::app()
	->clientScript
	->registerScriptFile(CHtml::asset(ROOT_PATH . '/common/vendor/twbs/bootstrap/dist/js/bootstrap.min.js'), CClientScript::POS_END)
	->registerCssFile(CHtml::asset(ROOT_PATH . '/common/vendor/twbs/bootstrap/dist/css/bootstrap.min.css'))
?>

<form id="BillingFiltersForm" class="form-inline">
<div class="result_title__ct">

		<h3 class="result_main__title">Биллинг клиник за

			<?php echo CHtml::dropDownList('dateFrom', $dateFrom, $billingPeriods, [ 'class' => 'dt_filter form-control' ]); ?>
		</h3>
</div>

<div>
<a href="/2.0/clinic/listDetails">Подробный отчет</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/2.0/clinic/list">Старый отчет</a>
</div>
<br/>
<div id="BillingStatistic">
	<?php $this->renderPartial('statistics', [
		'stat'  => $stat,
	]); ?>
</div>
<div class="dt_filters">
	<div class="filter inline">
		<label>
			Город
			<?php echo CHtml::dropDownList('cityId', $cityId,  CHtml::listData($cities, 'id_city', 'title'), [ 'class' => 'dt_filter', 'empty' => 'Все города' ]); ?>
		</label>
	</div>

	<div class="filter inline">
		<label>
			Тариф
			<?php echo CHtml::dropDownList('contractId', $contractId, CHtml::listData($contracts, 'contract_id', 'title'), [ 'class' => 'dt_filter', "empty" => 'все тарифы' ]); ?>
		</label>
	</div>

	<div class="filter inline">
		<label>
			Статус
			<?php
			echo CHtml::dropDownList('status', $status, $statuses, [ 'class' => 'dt_filter', "empty" => 'все статусы' ]);
			?>
		</label>
	</div>
	<div class="filter inline">
		<label>
			Менеджер
			<?php
			echo CHtml::dropDownList('managerId', $managerId, CHtml::listData($managers, 'user_id', 'user_fname'), [ 'class' => 'dt_filter', "empty" => 'все менеджеры' ]);
			?>
		</label>
	</div>

</div>

</form>

<div style="float: left;">
	<?php
		$this->renderPartial('/../../front/views/elements/datatable', [
			'tableId' => 'BillingTable',
			'filtersId' => 'BillingFiltersForm',
			'tableConfig' => $tableConfig,
			'isInit' => false,
			'className' => 'table table-striped',
			'fixHeader' => true,
			'keyTable'  => true,
		]);
	?>

	<div class="modal fade" id="paymentsModal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Поступления</h4>
				</div>
				<div class="modal-body">
					<?php
					$this->renderPartial('/../../front/views/elements/datatable', [
							'tableId' => 'PaymentsTable',
							'tableConfig' => $paymentsTableConfig,
							'isInit' => false,
							'noLoad' => true,
						]);
					echo CHtml::hiddenField('clinic_billing_id', '');
					?>
				</div>
				<div class="modal-footer">
					<button id="newPayment" class="btn btn-success">Создать поступление</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div>

	<script>
		$(document).ready(function() {
			var tableConfig = <?php echo json_encode($tableConfig); ?>;
			var paymentsTableConfig = <?php echo json_encode($paymentsTableConfig); ?>;
			var $billingTable = $("#BillingTable");

			var dt = new DataTableWrapper('#BillingTable', '#BillingFiltersForm', tableConfig);

			dt.dataTable.on('xhr.dt', function (e, settings, json) {
				$('#BillingStatistic').html(json.statisticsHtml).show();
			});

			dt.init();

			new $.fn.dataTable.KeyTable(dt.dataTable);
			new $.fn.dataTable.FixedHeader(dt.dataTable);


			var dtp = new DataTableWrapper('#PaymentsTable', null, paymentsTableConfig);
			dtp.init();

			dtp.dataTable.on('click', 'button.edit', function () {
				var $currentRow = $(this).closest('tr');
				dtp.action('edit', $currentRow);
				return false;
			});

			dtp.dataTable.on('click', 'button.remove', function () {
				var paymentId = $(this).closest('tr').find('td').eq(0).html();
				if (confirm("Вы действительно хотите удалить поступлание?")) {
					$.post("/2.0/clinicBilling/deletePayment", {paymentId: paymentId}, function(data) {
						dtp.reload();
					});
				}
				return false;
			});

			$("#newPayment").click(function() {
				dtp.action('create');
				$("#DTE_Field_clinic_billing_id").val($("#clinic_billing_id").val());
				$(".DTE_Field_Name_clinic_billing_id").hide();
				$(".DTE_Field_Name_id").hide();
				return false;
			});

			$billingTable.on('click' , '.billingPayments', function() {
				dtp.dataTable.ajax.url('/2.0/clinicBilling/payments/?billingId=' + $(this).data('billing'));
				$("#clinic_billing_id").val($(this).data('billing'));
				dtp.reload();
				$('#paymentsModal').modal({'show':true});
				return false;
			});

			$billingTable.on('click' , '.actionAgree', function() {
				var editor = $billingTable.data('DataTableWrapper').editor;
				editor.edit(this.parentNode.parentNode, false);
				editor.set('action_type', 'agree');
				editor.submit();
				return false;
			});

			//изменяем суммы в таблице при закрытии окна
			$('#paymentsModal').on('hide.bs.modal', function (e) {
				var sum = 0;
				$('tbody tr', this).each(
					function() {
						var td = $('td', this).eq(2);
						if (td != undefined) {
							var s = parseFloat($(td).text());
							if (!isNaN(s))
								sum += s;
						}
					}
				);
				var sumRow = $("a.billingPayments[data-billing=" + $("#clinic_billing_id").val() + "]");
				var debet = $(sumRow).parent().prev();
				var credit = $(sumRow).parent().next();
				sumRow.text(sum);
				credit.text(debet.text() - sum);
			})
		});
	</script>

	<script src='/js/jquery-ui/jquery-ui.min.js' type='text/javascript' language="JavaScript"></script>
	<link rel="stylesheet" type="text/css" href="/js/jquery-ui/themes/smoothness/jquery-ui.css"/ >

</div>
