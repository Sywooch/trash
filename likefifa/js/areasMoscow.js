function showDistricts(areaId, firstSelect){
    if(!areaId)
        areaId = 0;
    if(!firstSelect)
        $(".hint").addClass("hidden");     
	var data = eval("("+$("#data-area").val()+")");
	var area = data[areaId];
    var districtArr = $("#districtMoscow").val().split(',');

    $("#area").val(areaId);
    document.getElementById('map_pic').src=homeUrl+'i/map/map' + areaId + '.gif';
    if(areaId != 0){
        var output = '<li><div class="district-link selected select_all_areas"><span></span><a class="add_all_areas">выбрать все</a> | <a class="delete_all_areas">удалить все</a></div></li>';
        for(var item in area.districts){
            if(!firstSelect) selectedArea = 'selected';
            else selectedArea = '';
            for(var k in districtArr)
                if(item == districtArr[k])
                    selectedArea = 'selected';

            output += '<li><div class="district-link '+selectedArea+'" data-id="'+item+'"><span></span>'+area.districts[item]['name']+'</div></li>';
        }
    }

	$("#district").html(output);
    
    $(".district-link").click(function(){
        $(this).toggleClass("selected");
        if ( $( this ).hasClass( 'select_all_areas' ) ) {
            if ( $( this ).hasClass( 'selected' ) ) {
                $( '#district .district-link' ).addClass( 'selected' );
            } else {
                $( '#district .district-link' ).removeClass( 'selected' );
            }
        } 
    });

    $(".district-link .add_all_areas").click(function(){
        $( '#district .district-link' ).removeClass( 'selected' );
    }); 
    $(".district-link .delete_all_areas").click(function(){
        $( '#district .district-link' ).addClass( 'selected' );
    });

}

$(document).ready(function(){	
	showDistricts($("#areaMoscow").val(), true);
	$("#district").val($("#districtMoscow").val());
	$("area").click(function(){
        var id = $(this).data("id");
        showDistricts(id, false);
    });

});