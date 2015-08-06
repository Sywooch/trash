$(function() {
	
	$('.trigger-column').live('change', function() {
		var $this = $(this), 
		gridId = $this.data('grid-id'), 
		settings = $.fn.yiiGridView.settings[gridId], 
		data = {};

		data[$this.data('param')] = $this.attr('checked') ? 1 : 0;
		
		$('#'+gridId).addClass(settings.loadingClass);

		$this
			.attr('disabled', 'disabled')
			.blur();
		
		$.post($this.data('url'), data, function() {
			$.fn.yiiGridView.update(gridId);
		});
	});
	
});