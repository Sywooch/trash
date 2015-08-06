$(document).ready(function(){

	$(".jsm-select").each(function(){
		var me = $(this);
		var relatedSelect = me.data('selectRelated');
		var $relatedSelect = $('[data-select='+relatedSelect+']');

		if ( $("option", $relatedSelect).first().attr("data-select-placeholder") != "" ) {
			var $placeholderOption = $("option", $relatedSelect).first();
			var $placeholderClone = $placeholderOption.clone();

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
				me.closest("form").submit();
			}
		});

	});

});