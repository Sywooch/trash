DdEvent.add(document, 'WidgetContentLoad_' + widget.options.id, function(e, data) {

	var ddOnlineOverlay = widget.byClass("dd-online-overlay");
	widget.byClass("dd-online-popup").style.display = 'block';
	ddOnlineOverlay.style.display = 'block';

	DdEvent.add(ddOnlineOverlay, 'click', function(e) {
		widget.destroyWidget();
		return false;
	});

	if (widget.alreadyInit != undefined) {
		return;
	}
	widget.alreadyInit = true;

	DdEvent.add(window, 'message', function (e) {
		if (e.data == 'close') {
			widget.destroyWidget();
			return;
		}

		if (e.data == 'saveSuccess') {
			widget.closeAble = true;
			return;
		}

		if (e.data == 'expand') {
			var modal = widget.byClass("dd-modal-frame");
			var diagn = widget.byClass("dd-online-popup-Diagnostic");
			if (diagn)  {
				diagn.style.height = 570;
				widget.byClass("dd-modal-frame").height = 570;
				modal.height = 570;
			}
			var doc = widget.byClass("dd-online-popup-Doctor");
			if (doc)  {
				modal.height = parseInt(modal.height) + 90;
				doc.style.height = modal.height;
			}
			return;
		}

		if (e.data == 'collapse') {
			var modal = widget.byClass("dd-modal-frame");

			var diagn = widget.byClass("dd-online-popup-Diagnostic");
			if (diagn)  {
				diagn.style.height = 570;
				modal.height = 475;
			}

			var doc = widget.byClass("dd-online-popup-Doctor");
			if (doc)  {
				doc.style.height = modal.height;
				modal.height = parseInt(modal.height) - 95;
			}
			return;
		}
	});

});
