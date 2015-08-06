var filter = $(".js-complete-geo");
var data_provider = $('.xml-data-provider');
var search_param_name = data_provider.data('search-param-name') != undefined ? data_provider.data('search-param-name') : 'stations';
var input = $('input[name=' + search_param_name + ']');

if (data_provider.length > 0) {
	var metroList = data_provider.text();
	metroList = JSON.parse(metroList);
	metroList = $.map(metroList, function (item) {
		return {
			value: item.name /*id*/,
			label: item.name,
			id: item.id
		};
	})
	/*.sort(function(a, b){
	 if (a.label < b.label) return -1;
	 if (a.label > b.label) return 1;
	 return 0;
	 });*/

	var self = this;
	self.filter = $(".js-complete-geo");
	self.inputGeo = $(".js-choose-input-geo");

	var m = metroList;

	self.filter.autocomplete({
		minLength: 0,

		source: function (request, response) {
			var term = $.ui.autocomplete.escapeRegex(request.term)
				, startsWithMatcher = new RegExp("^" + term, "i")
				, startsWith = $.grep(m, function (value) {
					return startsWithMatcher.test(value.label || value.value || value);
				})
				, containsMatcher = new RegExp(term, "i")
				, contains = $.grep(m, function (value) {
					return $.inArray(value, startsWith) < 0 &&
						containsMatcher.test(value.label || value.value || value);
				});

			response(startsWith.concat(contains));
		},

		focus: function (event, ui) {
			self.filter.val(ui.item.label);
			input.val(ui.item.id);
			return false;
		},
		select: function (event, ui) {
			self.filter.val('');

			input.val(ui.item.id);
			$(this).val(ui.item.label);
			return false;
		}
	}).data("uiAutocomplete")._renderItem = function (ul, item) {
		return $("<li></li>")
			.data("item.autocomplete", item)
			.append("<a>" + item.label + "</a>")
			.appendTo(ul);
	};

}
