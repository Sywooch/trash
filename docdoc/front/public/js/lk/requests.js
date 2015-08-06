$(document).ready(function() {
	var $formFilters = $('#RequestsFiltersForm');
	var $table = $('#RequestsTable');
	var dt = $table.data('DataTableWrapper');
	var checkedStatus = null;


	// Обработка переключения состояния
	$('label.filter_state__label', $formFilters).click(function() {
		$('.filter_doctor__input', $formFilters).prop('checked', false);

		var val = $('#'+this.htmlFor, $formFilters).val();
		var checked = $('#'+this.htmlFor, $formFilters).prop('checked');

		if (val == checkedStatus) {
			$('#'+this.htmlFor, $formFilters).prop('checked', false);
			checkedStatus = null;
		} else {
			$('#'+this.htmlFor, $formFilters).prop('checked', true);
			checkedStatus = val;
		}

		dt.reload();
		return false;
	});

	$table.on('click', 'button.action_came', function() {
		var editor = $table.data('DataTableWrapper').editor;
		editor.edit(this.parentNode.parentNode, false);
		editor.set('action_type', 'came');
		editor.submit();
	});

	// Нажатие ссылки "аудиозаписи"
	$table.on('click', '.request_records__label', function() {
		$('.request_records', $(this).closest('.request_records-ct')).toggleClass('s-open');
	});

	$('input.DatePicker').datetimepicker({
		timepicker: false,
		format: 'd.m.Y',
		lang:'ru',
		onClose: function (dp, $input) {
			dt.reload();
		},
		closeOnDateSelect: true
	});

	$("#exportInExcel").click(function() {
		var input = $('input[name=lastId]', dt.$formFilters);
		var lastId = input.val();
		input.val('');
		location.href = dt.url + "&" + dt.$formFilters.serialize() + "&type=xls";
		input.val(lastId);
	});
});