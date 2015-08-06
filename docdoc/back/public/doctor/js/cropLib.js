var jcrop_api;

var shablon = function(crop,width,height){
	this.c = crop;
	this.w = width;
	this.h = height;
}


function initJcrop(elt) {
	$(elt).Jcrop({
		onSelect: updateCoords,
		onRelease: releaseCheck
       },function(){ jcrop_api = this; } );
	alert('www');
}

			
function releaseCheck() {
	jcrop_api.setOptions({ allowSelect: true });
};


function updateCoords(c){$('#x').val(c.x);$('#y').val(c.y);$('#w').val(c.w);$('#h').val(c.h);};


function checkCoords()
{
	if (parseInt($('#w').val())) return true;
//	alert('Auaaeeoa ?anou ecia?a?aiey aey nio?aiaiey');
	return false;
};

function openEditBlock() {
	$("#fullImg").show();
}

function setShablon (id) {
	if (id != 0) {
		jcrop_api.enable();
		jcrop_api.setOptions( { aspectRatio: shablonList[id].c, minSize: [ shablonList[id].w, shablonList[id].h ] });
		jcrop_api.focus();
	} else {
		jcrop_api.release();
//					jcrop_api.disable();
		jcrop_api.focus();
	}
}