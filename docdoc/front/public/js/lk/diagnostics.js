$(document).ready(function() {
	var $table = $('#DiagnosticsTable');
	var dt = $table.data('DataTableWrapper');
	var $currentRow = null;

	$table.on('click', 'a.edit', function () {
		$currentRow = $(this).closest('tr');
		dt.action('edit', $currentRow);
		return false;
	});

	$table.on('click', 'a.delete', function () {
		$currentRow = $(this).closest('tr');
		var rowData = dt.dataTable.row($currentRow).data();

		if (confirm('Диагностика будет удалена')) {
			$.ajax({
				url: '/lk/diagnostics/change?id=' + rowData.id,
				type: 'post',
				data: {
					data: rowData,
					delete: 1
				},
				success: function(response) {
					dt.dataTable.row($currentRow).remove().draw(false);
				}
			});
		}

		return false;
	});

	dt.editor.on('open', function (e) {
		$('.DTE_Action_Edit #DTE_Field_clinic').prop('disabled', 'disabled');
		$('.DTE_Action_Edit #DTE_Field_diagnostic').prop('disabled', 'disabled');
	});

	dt.editor.on('close', function (e) {
		$('#DTE_Field_clinic').prop('disabled', '');
		$('#DTE_Field_diagnostic').prop('disabled', '');
	});

	$("#exportInExcel").click(function() {
		location.href = dt.url + "?" + dt.$formFilters.serialize() + "&type=xls";
	});

	$("#newDiagnostic").click(function() {
		dt.action('create');
	});
});