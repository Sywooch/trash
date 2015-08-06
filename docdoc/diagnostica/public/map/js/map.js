
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
			alert(text);
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


function showMore () {
	var coordinats = myMap.getBounds();
	setLeftCollumn(coordinats);
	$("#showMore").remove();
	moveReady = true;
	
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
			alert(text);
		},
		success: function(text){
			page = page+1;
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
			$("#diagnostic-subtype").slideUp();
			rightMenuOpen = false;
		}
		if (leftMenuOpen) { 
			$("#diagnostic-type").slideUp();
			leftMenuOpen = false;
		}
	})
	
	$("#diagnostic-type-btn").click(function(e){
		 
		$("#diagnostic-subtype").slideUp( function() {rightMenuOpen = false;});
		$("#diagnostic-type").slideToggle(300, function() {
			leftMenuOpen = (leftMenuOpen) ? false : true;
		  });
		e.stopPropagation();
	});
	$("#diagnostic-subtype-btn").click(function(e){
		$("#diagnostic-type").slideUp( function() {leftMenuOpen = false;});
		$("#diagnostic-subtype").slideToggle(300, function() {
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
		$("#diagnostic-type").slideUp(300, function(){
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
		$("#diagnostic-subtype").slideUp(300, function(){
			$("#diagnostic-subtype-select").text(textSelectItem);
			rightMenuOpen = false;
		});
	});
}