var strB = '<span class="marker"><span>';
var strA = '<span class="markerPoint"><span>';


function setPoints (points, step) {
	var countPoint = points.length;	


	if ( countPoint > step ) {
		for ( var i = step; i < countPoint; i++ ) {
			var coordinates = points[i].coords,
	            properties = {
	                id : points[i].id,
	                name : '',  
	                body : 'Идет загрузка данных ...'
	                
	            },
	            options = {
	            	iconImageHref: '/st/i/map/dotGreen.png', // картинка иконки
	                iconImageSize: [14, 14], 
	                iconImageOffset: [-15, -10], 
	                maxWidth : 350,
	                balloonCloseButton : false,
	                balloonShadow : false,
	                balloonLayout : myBalloonContentBodyLayout,
	                balloonOffset : [0, -20]
	            }
	            placemark = new ymaps.Placemark(coordinates, properties, options);
	        myMap.geoObjects.add(placemark, points[i].id);
	        placemark.events.add('click', onClick);
		}
		
		for ( var i = 0; i < step; i++ ) {
			var coordinates = points[i].coords,
	            properties = {
	                name : points[i].name,
	                id : points[i].id,
	                body : 'Идет загрузка данных ...',
	                iconContent:strB + points[i].name + "</span></span>"
	            },
	            options = {
	            	iconImageHref: '/st/i/common/null.gif', // картинка иконки
	                iconImageSize: [50, 50], 
	                iconOffset: [-25, -20], 
	                iconImageOffset: [0,-20],
	                maxWidth : 350,
	                balloonCloseButton : false,
	                balloonShadow : false,
	                balloonLayout : myBalloonContentBodyLayout,
	                balloonOffset : [0, -20]
	            },
	            placemark = new ymaps.Placemark(coordinates, properties, options);
	
			var id = points[i].id;
			myMap.geoObjects.each(function (geoObject) {
			    if (geoObject.properties.get('id') == id) {
			    	myMap.geoObjects.remove(geoObject);
			        return false;
			    }
			});
	        myMap.geoObjects.add(placemark, points[i].id);
	        placemark.events.add('click', onClick);
	        placemark.events.add('mouseenter', onMouseOver);
	        placemark.events.add('mouseleave', onMouseOut);	
		}
		
	} else {
		for ( var i = 0; i < countPoint; i++ ) {
		
			var coordinates = points[i].coords,
	            properties = {
	                name : points[i].name,
	                id : points[i].id,  
	                body : 'Идет загрузка данных ...',
	                iconContent:strB+points[i].name+"</span></span>"
	            },
	            options = {
	            	iconImageHref: '/st/i/common/null.gif', // картинка иконки
	                iconImageSize: [50, 50], 
	                iconOffset: [-25, -20], 
	                iconImageOffset: [0,-20],
	                maxWidth : 350,
	                balloonCloseButton : false,
	                balloonShadow : false,
	                balloonLayout : myBalloonContentBodyLayout,
	                balloonOffset : [0, -20]
	            },
	            placemark = new ymaps.Placemark(coordinates, properties, options);
	
			var id = points[i].id;
			myMap.geoObjects.each(function (geoObject) {
			    if (geoObject.properties.get('id') == id) {
			    	myMap.geoObjects.remove(geoObject);
			        return false;
			    }
			});
	        myMap.geoObjects.add(placemark, points[i].id);
	        placemark.events.add('click', onClick);
	        placemark.events.add('mouseenter', onMouseOver);
	        placemark.events.add('mouseleave', onMouseOut);	
		}
	}
}

closeBall = function(){
	this.myMap.balloon.close();
	moveReady = true;
};




function onClick(e) {
	moveReady = false;
	var placemark = e.get('target'),
    name = placemark.properties.get('name');
    id = placemark.properties.get('id');
    
    if ( name != '' ) {
    	placemark.properties.set('iconContent','<span class="marker markerViolet"><span>'+name+'</span></span>');
    } 
    
    
    $.ajax( {
    	url: '/map/getPoint.php', 
    	type: "get",
        data: "id="+id+"&diagnostica="+$("#selectDiagnpostic").val()+"&subDiagnostica="+$("#selectSubDiagnpostic").val(),
        error : function(xml,text){
			//console.log(text);
		},
        success : function (html) {
        	moveReady = false;
        	placemark.properties.set('body', html);
        	myMap.geoObjects.each(function (geoObject) {
        	    if (geoObject.properties.get('id') == id) {
        	    	var projection = myMap.options.get('projection');
        	    	var position = geoObject.geometry.getCoordinates();
        	    	var positionpx = projection.toGlobalPixels(position, myMap.getZoom());
        	    	size = myMap.container.getSize();
        	    	myMap.panTo(projection.fromGlobalPixels([positionpx[0], positionpx[1]-60 ], myMap.getZoom()),{delay: 0});
        	    	return false;
        	    	//alert("www");
        	    }
        	});
        }
    });
}

function onMouseOver(e) {
	var placemark = e.get('target');
    name = placemark.properties.get('name');
	id = placemark.properties.get('id');
	$("#item_"+id).attr("class","item act");
	placemark.properties.set('iconContent','<span class="marker markerGreen"><span>'+name+'</span></span>');
	$("#resultSet .item")
}
function onMouseOut(e) {
	var placemark = e.get('target');
	id = placemark.properties.get('id');
    name = placemark.properties.get('name');
    $("#item_"+id).attr("class","item");
	placemark.properties.set('iconContent','<span class="marker markerViolet"><span>'+name+'</span></span>');
}


function initItem () {
	$("#resultSet .item").bind ('click', function() {
		$("#resultSet .act").attr("class","item");
		$(this).attr("class","item act");
		moveReady = false;
		var id = $(this).attr("pointId");
		myMap.geoObjects.each(function (geoObject) {
		    if (geoObject.properties.get('id') == id) {
		    	$.ajax( {
    		    	url: '/map/getPoint.php', 
    		    	type: "get",
    		        data: "id="+id+"&diagnostica="+$("#selectDiagnpostic").val()+"&subDiagnostica="+$("#selectSubDiagnpostic").val(),
    		        error : function(xml,text){
    					//console.log(text);
    				},
    		        success : function (html) {
    		        	geoObject.properties.set('body', html);
    		        	geoObject.balloon.open(myMap.getCenter());
    		        	var projection = myMap.options.get('projection');
	        	    	var position = geoObject.geometry.getCoordinates();
	        	    	var positionpx = projection.toGlobalPixels(position, myMap.getZoom());
	        	    	size = myMap.container.getSize();
	        	    	myMap.panTo(projection.fromGlobalPixels([positionpx[0], positionpx[1]-60 ], myMap.getZoom()),{delay: 0});
    		        }
    		    });
		    }
		});
/*
		myMap.panTo([parseFloat($(this).attr("Long")), parseFloat($(this).attr("Lat"))], {flying: true,duration: 1000,checkZoomRange: true,
			callback: function () {
//					myMap.setZoom(15, {duration: 1000});
				myMap.geoObjects.each(function (geoObject) {
				    if (geoObject.properties.get('id') == id) {
				    	$.ajax( {
		    		    	url: '/map/getPoint.php', 
		    		    	type: "get",
		    		        data: "id="+id+"&diagnostica="+$("#selectDiagnpostic").val()+"&subDiagnostica="+$("#selectSubDiagnpostic").val(),
		    		        error : function(xml,text){
		    					alert(text);
		    				},
		    		        success : function (html) {
		    		        	moveReady = false;
		    		        	geoObject.properties.set('body', html);
		    		        	myMap.setZoom(15, {duration: 1000});
		    		        	geoObject.balloon.open(myMap.getCenter());
		    		        	var projection = myMap.options.get('projection');
    		        	    	var position = geoObject.geometry.getCoordinates();
    		        	    	var positionpx = projection.toGlobalPixels(position, myMap.getZoom());
    		        	    	size = myMap.container.getSize();
    		        	    	myMap.panTo(projection.fromGlobalPixels([positionpx[0], positionpx[1]-60 ], myMap.getZoom()),{delay: 0});
    		        
		    		        }
		    		    });
				    }
				});
				} 
		});
		*/
	})


	$("#resultSet .item").bind ('mouseover', function() {
		var id = $(this).attr("pointId");
		if(myMap && myMap.geoObjects)
			myMap.geoObjects.each(function (geoObject) {
			    if (geoObject.properties.get('id') == id) {
			    	geoObject.properties.set('iconContent','<span class="marker markerGreen"><span>'+geoObject.properties.get('name')+'</span></span>');
			        return false;
			    }
			});
		$(this).attr("class","item act");
	});

	$("#resultSet .item").bind ('mouseout', function() {
		var id = $(this).attr("pointId");
		if(myMap && myMap.geoObjects)
			myMap.geoObjects.each(function (geoObject) {
			    if (geoObject.properties.get('id') == id) {
			        geoObject.properties.set('iconContent','<span class="marker markerViolet"><span>'+geoObject.properties.get('name')+'</span></span>');
			        return false;
			    }
			});
		$(this).attr("class","item");
	});
}