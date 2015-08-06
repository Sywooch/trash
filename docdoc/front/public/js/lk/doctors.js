$(document).ready(function() {
	var $table = $('#DoctorsTable');
	var dt = $table.data('DataTableWrapper');
	var $currentRow = null;

	$table.on('click', 'a.edit', function () {
		$currentRow = $(this).closest('tr');
		dt.action('edit', $currentRow);
		return false;
	});

	$("#exportInExcel").click(function() {
		location.href = dt.url + "?" + dt.$formFilters.serialize() + "&type=xls";
	});

	$("#newDoctor").click(function() {
		dt.action('create');
	});
});
