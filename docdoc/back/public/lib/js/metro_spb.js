$(document).ready(function(){

	$countStationsLine = {
		line_kv: $("#map_stations .line_kv").length,
        line_kv: $("#map_stations .line_mp").length,
        line_kv: $("#map_stations .line_nv").length,
        line_kv: $("#map_stations .line_pr").length,
        line_kv: $("#map_stations .line_fr").length
	};
	
	$countStationsLineAct = {};
	
	var lineLight = function(){
		for (var line in $countStationsLine) {
			if ($countStationsLine[line] === $countStationsLineAct[line]) {
				$("#metro_lines #"+line).addClass("act");
			}
		}
	};
	
	$('#map_stations').bind('station-light', function() {
		$countStationsLineAct = {
			line_kv: $("#map_stations .line_kv.act").length,
            line_kv: $("#map_stations .line_mp.act").length,
            line_kv: $("#map_stations .line_nv.act").length,
            line_kv: $("#map_stations .line_pr.act").length,
            line_kv: $("#map_stations .line_fr.act").length
		};
		
		lineLight();
		
		showButtonClear();
	});
	
	$("#map_stations div:not('.title')").click(function(){
		$(this).toggleClass("act");
		$lineName = $(this).attr("class").split(" ");
		fullLine($lineName[0]);
		showButtonClear();
	});
    $("#map_stations .title").click(function(){
		$(this).toggleClass("act");
		$lineName = $(this).attr("class").split(" ");
		fullLine($lineName[0]);
		showButtonClear();
	});
	
	$("#metro_lines a").click(function(){
		$idLine = $(this).attr("id");
		if (!$(this).hasClass("act"))
		{
			$(this).addClass("act");
			$("#map_stations div."+$idLine).each(function(){
				$(this).addClass("act");
			});
		}
		else
		{
			$(this).removeClass("act")
			$("#map_stations div."+$idLine).each(function(){
				$(this).removeClass("act");
			});
		}
		showButtonClear();
		return false;
	});
	
	var fullLine = function(lineId) {
		$selectStation = $("#map_stations ."+lineId+".act").length;
		if ($selectStation == $countStationsLine[lineId])
			$("#metro_lines #"+lineId).addClass("act");
		else
			$("#metro_lines #"+lineId).removeClass("act");
	}
	
	var showButtonClear = function(){
		$selectStation = $("#map_stations .act").length;
		$stationsSelectName = [];
		$stationsSelectCount = $('#map_stations div.act').length;
		$('#map_stations div.act').each(function() {
			$stationsSelectName.push($(this).attr('title'));
		});
		$("#select-stations div").html($stationsSelectName.join(', '));
		$("#stations-select-count").html($stationsSelectCount);
		
		if ($selectStation > 0)
			$("#metro-clear-act, #select-stations").css("display","block");
		else
			$("#metro-clear-act, #select-stations").css("display","none");
	}
	
	$("#metro-clear-act").bind("click", function(){
		$("#map_stations .act").removeClass("act");
        $("#metro_lines a.act").removeClass("act");
		showButtonClear();
		return false;
	});
	
});