$(document).ready(function(){
	$countStationsLine = {
		line_zm: $("#map_stations .line_zm.title").length, 
		line_serpyx: $("#map_stations .line_serpyx.title").length, 
		line_kaluga: $("#map_stations .line_kaluga.title").length, 
		line_sokolniki: $("#map_stations .line_sokolniki.title").length, 
		line_lyblino: $("#map_stations .line_lyblino.title").length, 
		line_kalinin: $("#map_stations .line_kalinin.title").length, 
		line_kahov: $("#map_stations .line_kahov.title").length, 
		line_taganka: $("#map_stations .line_taganka.title").length, 
		line_arbat: $("#map_stations .line_arbat.title").length, 
		line_fili: $("#map_stations .line_fili.title").length, 
		line_butovo: $("#map_stations .line_butovo.title").length, 
		line_ring: $("#map_stations .line_ring.title").length
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
			line_zm: $("#map_stations .line_zm.act").length, 
			line_serpyx: $("#map_stations .line_serpyx.act").length, 
			line_kaluga: $("#map_stations .line_kaluga.act").length, 
			line_sokolniki: $("#map_stations .line_sokolniki.act").length, 
			line_lyblino: $("#map_stations .line_lyblino.act").length, 
			line_kalinin: $("#map_stations .line_kalinin.act").length, 
			line_kahov: $("#map_stations .line_kahov.act").length, 
			line_taganka: $("#map_stations .line_taganka.act").length, 
			line_arbat: $("#map_stations .line_arbat.act").length, 
			line_fili: $("#map_stations .line_fili.act").length, 
			line_butovo: $("#map_stations .line_butovo.act").length, 
			line_ring: $("#map_stations .line_ring.act").length		
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
		$(this).next().toggleClass("act");
		$lineName = $(this).attr("class").split(" ");
		fullLine($lineName[0]);
		showButtonClear();
	});
	
	$("#metro_lines a").click(function(){
		$idLine = $(this).attr("id");
		if (!$(this).hasClass("act"))
		{
			$(this).addClass("act");
			$("#map_stations div."+$idLine+":not(.title)").each(function(){
				$(this).addClass("act");
			});
		}
		else
		{
			$(this).removeClass("act")
			$("#map_stations div."+$idLine+":not(.title)").each(function(){
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
		var title, i, found;
		
		$selectStation = $("#map_stations .act").length;
		$stationsSelectName = [];
		$stationsSelectCount = 0; //$('#map_stations div.act').length;
		$('#map_stations div.act').each(function() {
			title = $(this).attr('title');
			found = false;
			for (var i = 0; i < $stationsSelectName.length; i++) {
				if ($stationsSelectName[i] === title) {
					found = true;
					continue;
				}
			} 
			
			if (!found) {
				$stationsSelectName.push(title);
				$stationsSelectCount++;
			}
		});
		$("#select-stations div").html($stationsSelectName.join(', '));
		$("#stations-select-count").html($stationsSelectCount);
		
		if ($selectStation > 0)
			$("#metro-clear-act, #select-stations").css("display","block");
		else
			$("#metro-clear-act, #select-stations").css("display","none");
	}
	
	$("#metro-clear-act").bind("click", function(){
		$("#map_stations .act, #metro_lines a.act").removeClass("act");
		showButtonClear();
		return false;
	});
	
	$(".link-metro").click(function(){
		$(".change-metro").removeClass("hidden");
		$(".change-area").addClass("hidden");
	});
	
});