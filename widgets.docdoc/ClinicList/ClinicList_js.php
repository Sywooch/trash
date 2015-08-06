var specList = widget.byClass("dd_sector_list");
if (specList) {
	DdEvent.add(specList, 'change', function(e) {
		widget.options.spec = this.value;
		widget.loadWidget();
	});
}

var stationList = widget.byClass("dd_clinic_station_list");
if (stationList) {
	DdEvent.add(stationList, 'change', function(e) {
		widget.options.station = this.value;
		widget.loadWidget();
	});
}

var districtList = widget.byClass("dd_clinic_district_list");
if (districtList) {
	DdEvent.add(districtList, 'change', function(e) {
		widget.options.district = this.value;
		widget.loadWidget();
	});
}

var pages = widget.byClass("dd-pagination");
if (pages) {
	var ps = pages.childNodes;
	for (var i=0; i < ps.length; i++) {
		DdEvent.add(ps[i], 'click', function(e) {
			widget.options.page = this.children[0].getAttribute("data-page");
			widget.loadWidget();
		});
	}
}