$(document).ready(function(){
	$('[data-select="select-spec"]').each(function(){
		var	self = $(this);
			specList = JSON.parse($('.xml-data-speclist').text()),
			curretnSpec = self.data('current-id'),
			selectedId = self.data('selected');
		$.each(specList,function(index, spec){
			self.append('<option '+ (selectedId && selectedId == spec.id ? 'selected' : '' )+' value="'+spec.id+'">'+spec.name+'</option>');
		}); 

	});

	$('[data-select="select-geo"]').each(function(){
		var	self = $(this);
			stationList = JSON.parse($('#geoDataJson').val()),
			currentStation = self.data('current-id'),
			selectedId = self.data('selected');
		$.each(stationList,function(index, station){
			self.append('<option '+ (selectedId && selectedId == station.id ? 'selected' : '' )+' value="'+station.id+'">'+station.label+'</option>');
		});

	});

	$(".jsm-select").each(function(){
		var me = $(this),
		    relatedSelect = me.data('selectRelated'),
		    $relatedSelect = $('[data-select='+relatedSelect+']');

		if ( $("option", $relatedSelect).first().attr("data-select-placeholder") != "" ) {
			var $placeholderOption = $("option", $relatedSelect).first(),
				$placeholderClone = $placeholderOption.clone();

			me.gotPlaceholder = true;
		}
								
		/*
		 me.click(function(){
		 $placeholderOption.hide().removeAttr("selected");
		 });
		 */

		$relatedSelect.change(function(){
			/* if we've selected any option besides placeholder */
			/* we remove placeholder */
			if (
				$placeholderOption &&
					$("option", $relatedSelect).not($placeholderOption).is(":selected")
				) {
				$placeholderOption.remove();
				me.gotPlaceholder = false;
				//console.log("мы что-то выбрали кроме плейсхолдера");
			}

			/* if we've NOT selected any option besides placeholder */
			/* we do nothing */
			else if (
				$("option", $relatedSelect).not($placeholderOption).not(":selected")
					&&
					$placeholderOption.is(":selected")
				) {
				// do nothing
				//console.log("мы выбрали только плейсхолдер");
			}

			/* if we've NOT selected any option and we got no placeholder */
			/* we clone placeholder and prepend it to select */
			else if (
				$("option", $relatedSelect).not($placeholderOption).not(":selected")
					&&
					//$placeholderOption.not(":selected")
					me.gotPlaceholder == false
				) {
				$placeholderOption = $placeholderClone.clone().attr("selected", "selected");
				$relatedSelect.prepend(
					$placeholderOption
				);
				me.gotPlaceholder = true;
				//console.log("мы сняли все галки и плейсхолдера у нас тоже нет");
			}





			if ( $(".homepage").length == 0 ) {
				var $selectedOptions = $("option:selected", $relatedSelect);
				if ( $selectedOptions.length == 1 ) {
					me.children("span").text( ($("option:selected", $relatedSelect)).text() );
				}
				else if ( $selectedOptions.length > 1 ) {
					var metroText = $selectedOptions.first().text() + ' [и еще ' + ($selectedOptions.length-1) + ']'
					me.children("span").text( metroText );
				}
				else if ( $selectedOptions.length == 0 ) {
					var metroText = $placeholderClone.attr("data-select-placeholder");
					me.children("span").text( metroText );
				}
				else {
					//console.log("something is wrong");
				};
			}


			if ( me.attr("data-autosubmit") == "false" ) {

			}
			else {
				var geo = $('select[data-select="select-geo"]');
				var spec = $('select[data-select="select-spec"]');
				if(geo.val())
					$('input[name="geoValue"]').val(geo.val().join(','));
				if(spec.val())
					$('input[name="diagnostic"]').val(spec.val());
				
				me.closest("form").submit();
			}
		});

	});

});