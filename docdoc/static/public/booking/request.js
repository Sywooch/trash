$(document).ready(function () {
	$('.scroll').niceScroll({
		cursorcolor: "#ccc",
		autohidemode: false,
		cursorwidth: "7px"
	});

	$(".request-popup-close").on("click", function () {
		window.parent.postMessage('close', '*');
		$(document).trigger("closePopup");
	});
});