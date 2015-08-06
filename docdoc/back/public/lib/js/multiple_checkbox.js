/*
 Dropdown with Multiple checkbox select with jQuery - May 27, 2013
 (c) 2013 @ElmahdiMahmoud
 license: http://www.opensource.org/licenses/mit-license.php
 */

$(".dropdown dt a").click(function () {
	var ob = $(this).parent().next().find("ul");
	$(".dropdown dd ul").removeClass('activeDropDown');
	ob.addClass("activeDropDown");
	$(".dropdown dd ul").not(".activeDropDown").slideUp('fast');
	$(".activeDropDown").slideToggle('fast');
});

$(document).bind('click', function (e) {
	var $clicked = $(e.target);
	if (!$clicked.parents().hasClass("dropdown")) $(".dropdown dd ul").hide();
});


$('.multiSelect input[type="checkbox"]').click(function () {
	var ob = $(this).parents('.dropdown').children('dt');

	$(this).next().toggleClass("act");

	var title = '';

	if ($(this).next().hasClass('act')) {
		if ($(this).parent().hasClass('noItem')) {
			resetMultiSelect($(this).parent().parent());
			$(this).next().toggleClass("act");
		} else {
			$(this).parents(".multiSelect").find(".noItem span").removeClass("act");
			$(this).parents(".multiSelect").find(".noItem input").removeAttr("checked");
			$(this).parents(".dropdown").find(".multiSel").html("");
		}

		ob.find(".hidden").hide();
	} else {
		var ret = ob.find(".hidden");
		ob.find("a").append(ret);
		if (ob.find(".multiSel").html() == '') {
			ret.show();
		}
	}

	$(this).parents(".activeDropDown").find(".multiCheckbox").each(function() {
		if ($(this).next().hasClass('act')) {
			title = title + $(this).next().html() + ", ";
		}
	});
	var html = '<span title="' + title + '">' + title + '</span>';
	ob.find(".multiSel").html(html);

});

$('.multiSelect span').click(function() {
	$(this).prev().trigger("click");
});

$('.multiAllSelect').click(function() {
	$(this).toggleClass("clear");
	if (!$(this).hasClass("clear")) {
		resetMultiSelect($(this).parent());
		$(this).html("Выбрать все");
	} else {
		resetMultiSelect($(this).parent());
		$(this).parent().find('.multiCheckbox').each(function(){
			$(this).trigger("click");
		});
		$(this).html("Удалить все");
	}
});

function resetMultiSelect(ob)
{
	ob.find(".multiCheckbox").removeAttr("checked");
	ob.find("span").removeClass("act");
	ob.parents(".dropdown").find(".multiSel").html("");
	ob.parents(".dropdown").find(".hidden").show();
}