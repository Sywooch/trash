$(document).ready(function() {
	$(function(){
		$.datepicker.setDefaults($.extend($.datepicker.regional["ru"]));
		$("#crDateShFrom").datepicker( {
			changeMonth : true,
			changeYear: true,
			duration : "fast",
			maxDate : "+1y",
			showButtonPanel: true
		});
		$("#crDateShTill").datepicker( {
			changeMonth : true,
			changeYear: true,
			duration : "fast",
			maxDate : "+1y",

			showButtonPanel: true
		});
		$("#crDateShFrom2").datepicker( {
			changeMonth : true,
			changeYear: true,
			duration : "fast",
			maxDate : "+1y",
			showButtonPanel: true
		});
		$("#crDateShTill2").datepicker( {
			changeMonth : true,
			changeYear: true,
			duration : "fast",
			maxDate : "+1y",

			showButtonPanel: true
		});
	});
	//getClinicByParams();
})




/*  Поиск клиник по заданным параметрам. Подгружает HTML в фильтре при выборе клиник	*/
function getClinicByParams() {
	//alert("http://back/reports/clinicList.htm?"+$('#formFilter').serialize());
	$.ajax({
		type: "get",
		url: "/reports/clinicList.htm",
		async: true,
		data: $('#formFilter').serialize(),
		success: function(html){
			$("#resultsetPane").html(html);
			
			$(".scroll-pane").css("width","328px");
			$('.scroll-pane').jScrollPane({
				width:298,
			    showArrows: true,
			    arrowScrollOnHover: true,
			    arrowButtonSpeed: 5
			});
			
			//$(".jspContainer").css("width","298px");
			//$(".jspContainer").css("height","100%");
			
		}
	});
}




/*	Группа функциядля работа с меню выбора и поиска клиник в отчете "Анализ обращений в клиники по месяцам"*/
function selectClinic ( id, name ) {
	$("#searchClinic"+id).remove();
	var str = "<tr id=\"selectedClinicTr"+id+"\"  onmouseover=\"if (!$(this).hasClass('trSelected')) $(this).attr('class','trActive')\" onmouseout=\"if (!$(this).hasClass('trSelected')) $(this).attr('class','')\"><td><input name=\"clinicList["+id+"]\" value=\""+id+"\" type=\"hidden\"/>"+name+"</td><td><span class=\"i-status arrow-cancel redLabel\" onclick=\"removeFromSelectedClinic("+id+")\"/></td></tr>";
	$("#selectedClinicTable").append(str);

}

function selectAllClinic () {
	$("#clinicList tr").each(function (i) {
		clinicName = $(this).text();
		clinicId = $(this).attr("clinicId");
		selectClinic ( clinicId, clinicName );
	})	
}

function removeAllClinic() {
	$("#selectedClinicTable tr").remove();
}


function removeFromSelectedClinic ( id ) {
	$("#selectedClinicTr"+id).remove();
}

function setFilterLine () {
	var str = "";
	$("#selectedClinicTable tr").each(function (i) {
		if ( str != "" ) {
			str = str + ", " + $(this).text();
		} else {
			str = $(this).text();
		}
	})	
	if ( str.length == 0 ) { str = "Выбрать центр"; }
	$("#statusFilter").text(str);
}
/*	****************************************	*/

