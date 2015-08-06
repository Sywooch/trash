
/*	Сортировка	*/
function reSort () {
	var sort = "non";
	switch ($("#sortRS").val()) {
		case 'non' : sort = 'asc'; break;
		case 'des' : sort = 'asc'; break;
		case 'asc' : sort = 'des'; break;
		default : sort = 'non'; break;
	}
	window.location.href = "/map.php?diagnostic="+$("#selectDiagnpostic").val()+"&subDiagnostica="+$("#selectSubDiagnpostic").val()+"&sortRS="+sort;
	return false;
}
        	

function setLeftCollumn ( coordinats ) {
	coord = coordinats;
	if(!myMap.balloon.isOpen()){
		$.ajax({
			url: "/map/mapChangeResultSet.php",
			type: "get",
			data: 	{	
						'startPage': page,
						'diagnostica': $("#selectDiagnpostic").val(),
						'subDiagnostica': $("#selectSubDiagnpostic").val(),
						'sortRS': $("#sortRS").val(),
						'coordinats': coord
					},
			async: false,
			error: function(xml,text){
				console.log(text);
			},
			success: function(text){
				page = page;
				$("#resultSet").html(text);
			},
			complete : function(){
				api.reinitialise();
				ready = true;
			}
		});
	}	
}


function showMore () {
	var coordinats = myMap.getBounds();
	setLeftCollumn(coordinats);
	$("#showMore").remove();
	moveReady = true;
	
}

function initDropDowns() {

	$('.b-dropdown_list .b-dropdown_item').click(function (){
		var $clickedItem = $(this),
			$wrapper = $clickedItem.closest('.b-dropdown'),
			$currentItem = $(".b-dropdown_item__current", $wrapper ),
			$currentItemText = $(".b-dropdown_item__text", $wrapper );

		//$currentItem.html($clickedItem.html());
		$currentItemText.html($clickedItem.text());
		$('.b-dropdown_item.s-current', $wrapper ).removeClass('s-current');
		$clickedItem.addClass('s-current');

		$('.b-dropdown_list', $wrapper ).hide();
		$wrapper.removeClass("s-open");

	});

	$('.b-dropdown_item__current').click(function (evt){
		evt.stopPropagation();
		var $wrapper = $(this).closest('.b-dropdown');
		var $dropdownList = $('.b-dropdown_list', $wrapper );

		if (($dropdownList).is(":visible")) {
			$dropdownList.hide();
			$wrapper.removeClass("s-open");
		}
		else {
			$wrapper.addClass("s-open");
			$dropdownList.show();
		}

	});
}

/*	Скроллинг	*/



function getNextPage(){
	$.ajax({
		url: "/map/mapAddResultSet.php",
		type: "get",
		data: 	{	
					'startPage': page+1,
					'diagnostica': $("#selectDiagnpostic").val(),
					'subDiagnostica': $("#selectSubDiagnpostic").val(),
					'sortRS': $("#sortRS").val(),
					'coordinats': coord
				},
		async: false,
		error: function(xml,text){
			console.log(text);
		},
		success: function(text){
			page = page+1;
			if($("#resultSet").length)
				$("#resultSet").html($("#resultSet").html() + text);
		},
		complete : function(){
			api.reinitialise();
			ready = true;
		}
	});	
}
/*	##########################################################	*/



/*	Меню	*/
var rightMenuOpen = false;
var leftMenuOpen = false;

$(function(){
	$("body").click(function(){
		if (rightMenuOpen) { 
			$("#diagnostic-subtype").slideUp(100);
			rightMenuOpen = false;
		}
		if (leftMenuOpen) { 
			$("#diagnostic-type").slideUp(100);
			leftMenuOpen = false;
		}
	})
	
	$("#diagnostic-type-btn").click(function(e){
		 
		$("#diagnostic-subtype").slideUp(100, function() {rightMenuOpen = false;});
		$("#diagnostic-type").slideToggle(100, function() {
			leftMenuOpen = (leftMenuOpen) ? false : true;
		  });
		e.stopPropagation();
	});
	$("#diagnostic-subtype-btn").click(function(e){
		$("#diagnostic-type").slideUp(100, function() {leftMenuOpen = false;});
		$("#diagnostic-subtype").slideToggle(100, function() {
			rightMenuOpen = (rightMenuOpen) ? false : true;
		  });
		if ( !$("#diagnostic-subtype-btn").hasClass("blocked") ) {
			$("#diagnostic-subtype-btn").attr("class", "inp round");
		}
		e.stopPropagation();
	});
	$("#diagnostic-type .item").click(function(){
		textSelectItem = $(this).text();
		$("#diagnostic-type .act").removeClass("act");
		$(this).addClass("act");
		$("#diagnostic-type").slideUp(100, function(){
			$("#diagnostic-type-select").text(textSelectItem);
		});
		$("#selectDiagnpostic").val($(this).attr("selId"));
		if ( menuItem[$("#selectDiagnpostic").val()][1].length > 0) {

			var str = "<div class=\"filter-list-full\">";
			for (var i=1; i < menuItem[$("#selectDiagnpostic").val()][1].length; i++ ) {
				str += "<div class=\"item\" selId=\""+menuItem[$("#selectDiagnpostic").val()][1][i][1]+"\">"+ menuItem[$("#selectDiagnpostic").val()][1][i][0] +"</div>";
			}
			str += "</div>";

			$("#diagnostic-subtype-btn").attr("class", "inp round selected");
			$("#diagnostic-subtype-select").text("Выберите из списка");
			$("#selectSubDiagnpostic").val("");
			$("#diagnostic-subtype").html(str);
			initSubMenu();
		} else {
			$("#diagnostic-subtype-select").text("Нет вариантов");
			$("#selectSubDiagnpostic").val("");
			$("#diagnostic-subtype-btn").attr("class","inp round blocked");
			$("#diagnostic-subtype").html('');
		}

		//alert(menuItem[$("#selectDiagnpostic").val()]);
	});
	initSubMenu();
})





function initSubMenu () {
	$("#diagnostic-subtype .item").click(function(){
		textSelectItem = $(this).text();
		$("#selectSubDiagnpostic").val($(this).attr("selId"));
		
		$("#diagnostic-subtype .act").removeClass("act");
		$(this).addClass("act");
		$("#diagnostic-subtype").slideUp(100, function(){
			$("#diagnostic-subtype-select").text(textSelectItem);
			rightMenuOpen = false;
		});
	});
}

$(document).ready(function(){
	$(window).resize(function(){
		if ($(window).width() < 1250)
			$("body").removeClass("window-1400").addClass("window-1250");
		else
			$("body").removeClass("window-1250");
		if ($(window).width() < 1400 && $(window).width() >= 1250)
			$("body").addClass("window-1400");
		if ($(window).width() >= 1400)
			$("body").removeClass("window-1400");
		$("#col-right-map, #left-col-list").css({height: $(window).height() - $("#header-wrap").height()});
	}).trigger("resize");

	initDropDowns();
});