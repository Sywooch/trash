var sectorList = widget.byClass("dd_sector_list");
DdEvent.add(sectorList, 'change', function(e) {
	widget.options.sector = this.value;
	widget.loadWidget();
});

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

var atHome = widget.byClass("dd_atHome");
if (atHome) {
	DdEvent.add(atHome, 'click', function(e) {
		widget.options.atHome = (this.checked) ? 1 : 0;
		widget.loadWidget();
	});
}

var orderExp = widget.byClass("dd_order_experience");
if (orderExp) {
	DdEvent.add(orderExp, 'click', function(e) {
		widget.options.order = "experience";
		widget.options.orderDirection = (orderExp.className.indexOf("DESC") > -1) ? "ASC" : "DESC";
		widget.loadWidget();
		return false;
	});
}

var orderRating = widget.byClass("dd_order_doctorRating");
if (orderRating) {
	DdEvent.add(orderRating, 'click', function(e) {
		widget.options.order = "doctorRating";
		widget.options.orderDirection = (orderRating.className.indexOf("DESC") > -1) ? "ASC" : "DESC";
		widget.loadWidget();
		return false;
	});
}


var orderPrice = widget.byClass("dd_order_price");
if (orderPrice) {
	DdEvent.add(orderPrice, 'click', function(e) {
		widget.options.order = "price";
		widget.options.orderDirection = (orderPrice.className.indexOf("DESC") > -1) ? "ASC" : "DESC";
		widget.loadWidget();
		return false;
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
