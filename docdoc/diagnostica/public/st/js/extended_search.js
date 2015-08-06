$(function () {

	if ($(".search_input_geo").length > 0) {

		var myMetro;

		$(function () {
			var $MetroSelected = $('.metro_selected');

			var oMetro = new als.Metro({
				city: $('input[name=location_trigger][value=location_metro]').attr('rel'),
				node: '.metrobox'
			});

			oMetro.addEventListener('mapchange', function (e, station) {
				//console.dir(arguments);
				var m = oMetro.getCurrentList();
//		console.dir(m);

				$('.metro_selected_title, .metro_selected, .als_metro_deselect').toggle(m.length > 0);

				var h = $MetroSelected.height();

				$MetroSelected
					//.css('height',h+'px')
					.html('')
				;

				/* dd disabling all stations in stations list */
				//$(".js-stationselect").removeClass("s-active");
				/* dd disabling all stations in stations list */

				$(".js-geoselect").removeClass("s-active");

				/* dd checking if selected stations are in any region */
				var stationsArrayChecked = []
				var makeArrayFrom = function () {
					for (var i = m.length - 1; i >= 0; i--) {
						stationsArrayChecked.push(m[i].id);
					}
				}
				makeArrayFrom();

				function isArrayInArray(arraySup, arraySub) {
					arraySup.sort();
					arraySub.sort();
					var i, j;
					for (i = 0, j = 0; i < arraySup.length && j < arraySub.length;) {
						if (arraySup[i] < arraySub[j]) {
							++i;
						} else if (arraySup[i] == arraySub[j]) {
							++i;
							++j;
						} else {
							// sub[j] not in sup, so sub not subbag
							return false;
						}
					}
					// make sure there are no elements left in sub
					return j == arraySub.length;
				}

				$(".js-regionselect").each(function () {
					var me = $(this);
					me.stationsArray = (me.data("station-id-array").toString()).split(",");
					me.allStationsChecked = false;

					if (isArrayInArray(stationsArrayChecked, me.stationsArray)) {
						me.addClass("s-active");
					}
					;
				});
				/* dd checking if selected stations are in any region */

				$.each(m, function (i, val) {
					$Item = $('<dl class="metro_selected_label" rel="' + val.id + '"><dt>' + val.name + '</dt><dd></dd><input type="hidden" name="metroId" value="' + val.id + '"/></dl>');


					$Item.find('dd').click(function () {
						//console.log('удалить');
						oMetro.toggleStation(val.id, false);
						//$Item.remove();

						/* dd */
						var $relatedElements = $(".js-stationselect" + "[data-station-id=" + val.id + "]");
						$relatedElements.removeClass("s-active");
						/* dd end */
					});

					/* dd activating stations in stations list */
					var $relatedElements = $(".js-stationselect" + "[data-station-id=" + val.id + "]");
					$relatedElements.addClass("s-active");
					/* dd activating stations in stations list */


					/* dd */
					$(document).on('click', '.metro_selected_label dd', function () {
						$(this).parent().remove();
						return false;
					});

					$('.js-stationselect[data-station-id=' + val.id + ']').addClass("s-active");


					$MetroSelected.append($Item);
				});

				$MetroSelected.css('height', 'auto');

				var hew_h = $MetroSelected.height();

				$MetroSelected
					.css('height', h + 'px')
					//.animate()

					.animate({'height': hew_h}, 000, function () {
						$MetroSelected.css('height', 'auto');
					})

				;


			});
			//oMetro.removeEventListener('stationchange',function(){})


			myMetro = oMetro;


			/* dd */
			oMetro.initActionButtonsFlag = false;
			$('.js-popup-tr').click(function () {
				var metroIds = $('#geoValue').val();
				if (metroIds.length > 0) {
					oMetro.initActionButtons();
					oMetro.show(function () {
						metroIds = metroIds.split(",");
						oMetro.setSelectedStations(metroIds);
						$('.metro_selected').show();
						oMetro.node.trigger('mapchange');
					});
				}
			});
			/* dd */

			$('.als_metro_toggle_scheme').click(function (e) {
				e.stopPropagation();
				var opened = $('.metrobox').hasClass('opened');
				if (!opened) {
					$(this).hide();
					$('.metro_preloader').show();
				}

				if (!opened) {

					oMetro.show(function () {
						oMetro.setSelectedStations($MetroSelected.find('dl').map(function () {
							return $(this).attr('rel');
						}));

						oMetro.node.trigger('mapchange');
						$('.als_metro_toggle_scheme').html('Скрыть схему метро').show();
						$('.metro_preloader').hide();


					});
				} else {
					oMetro.hide();
					$('.als_metro_toggle_scheme').html('Показать схему метро');
					$('.metro_selected_title, .metro_selected, .als_metro_deselect').toggle($.trim($('.metro_selected').html()).length > 0);
				}
			});

			$('.ex_location_map_trigger_metro').click(function () {
				$('.ex_location_map_triggers .pseudo').removeClass('active');
				$(this).addClass('active');
				if ($.trim($('.metro_selected').html()).length > 0 && !$('.metrobox').hasClass('opened')) {
					$('.metro_selected_title, .metro_selected, .als_metro_deselect').show();
				}
//		else if($.trim($('.metro_selected').html()).length<1 && ($('input[name=foot_min]').val() !== '' || $('input[name=foot_min2]').val() !== '' || $('select[name=only_foot] option:selected').val() !== '0')){
//			return; // Просто не показываем метро
//		}
				else {
					$('.metro_preloader').show();
					$('.als_metro_toggle_scheme').hide();
					oMetro.show(function () {
						oMetro.setSelectedStations($MetroSelected.find('dl').map(function () {
							return $(this).attr('rel');
						}));
						oMetro.node.trigger('mapchange');
						$('.als_metro_toggle_scheme').html('Скрыть схему метро').show();
						$('.metro_preloader').hide();
					});

				}

			}).click()
			;
			$('.ex_location_map_trigger_ymap').click(function () {
				var
					obl_id = $('.sf_head_region_button').attr('rel'),
					isMetro = obl_id == 10 || obl_id == 1 || obl_id == -1;
				;

				$('.ex_location_map_triggers .pseudo').removeClass('active');
				$(this).addClass('active');

				$('.ex_location_map_triggers .ex_location_select_button').removeClass('ex_location_select_button_active');
				$(this).addClass('ex_location_select_button_active');
				$('.ex_location_type[rel=location_metro]').hide();
				oMetro.hide();
			});

			$(document).ready(function () {
				$(".input_metro_submit").click(function () {

					var metroIds = [];
					$('input[name="metroId"]').each(function () {
						metroIds.push($(this).val());
					});
					metroIds = metroIds.toString();

					$('#geoValue').val(metroIds);

					/* dd */
					var metroIds = $('#geoValue').val();
					if (metroIds.length > 0) {
						metroIds = metroIds.split(",");
						this.stations = metroIds;
					}


					/* dd end */


				});
			});
			/* dd */

			/* selecting stations by one */
			var $metroListStations = $(".metro_list_stations");
			$(".js-stationselect", $metroListStations).click(function () {
				var me = $(this);
				var selectedId = me.data("stationId");
				oMetro.toggleStation(selectedId);
				oMetro.node.trigger('mapchange');
			});
			/* selecting stations by one end */

			/* selecting stations by regions */
			var $metroListGeo = $(".regions_list");
			$(".js-regionselect").each(function () {
				var me = $(this);
				me.stationsArray = me.data("station-id-array").toString();
				me.stationsArray = me.stationsArray.split(",");
				me.checkStation = function (stationId) {
					me.stationsArray.indexOf(stationId) > -1
				}

				me.click(function () {

					if (!me.hasClass("s-active")) {
						for (var i = me.stationsArray.length - 1; i >= 0; i--) {
							oMetro.setSelectedStations(me.stationsArray[i]);
						}
						oMetro.setSelectedStations(me.stationsArray);
						oMetro.node.trigger('mapchange');
						$(".js-regionselect").removeClass("s-active");
						me.addClass("s-active");
						if (me.hasClass("js-regionselect-whole")) {
							me.parent().children(".regions_sublist").children().addClass("s-active");
						}
					}


					else if (me.hasClass("s-active") && me.hasClass("js-regionselect-whole")) {

						oMetro.deselect();
						me.removeClass("s-active");
						me.parent().children(".regions_sublist").children().removeClass("s-active");

					}

					else if (me.hasClass("s-active") && !me.hasClass("js-regionselect-whole") && $(".js-regionselect-whole.s-active").length == 0) {
						oMetro.deselect();
						//}
						me.removeClass("s-active");
					}

					else if (me.hasClass("s-active") && !me.hasClass("js-regionselect-whole") && $(".js-regionselect-whole.s-active").length > 0) {
						oMetro.deselect();
						//}
						for (var i = me.stationsArray.length - 1; i >= 0; i--) {
							oMetro.setSelectedStations(me.stationsArray[i]);
						}
						oMetro.setSelectedStations(me.stationsArray);
						oMetro.node.trigger('mapchange');
						$(".js-regionselect").removeClass("s-active");
						me.addClass("s-active");
						if (me.hasClass("js-regionselect-whole")) {
							me.parent().children(".regions_sublist").children().addClass("s-active");
						}
					}
				});
			});
		});

	}
	<!-- if -->


});

var callMetro = function () {

	var myMetro;

	$(function () {
		var $MetroSelected = $('.metro_selected');

		var oMetro = new als.Metro({
			city: $('input[name=location_trigger][value=location_metro]').attr('rel'),
			node: '.metrobox'
		});

		oMetro.addEventListener('mapchange', function (e, station) {

			var m = oMetro.getCurrentList();

			$('.metro_selected_title, .metro_selected, .als_metro_deselect').toggle(m.length > 0);

			var h = $MetroSelected.height();

			$MetroSelected
				.css('height', h + 'px')
				.html('')
			;

			$.each(m, function (i, val) {
				$Item = $('<dl class="metro_selected_label" rel="' + val.id + '"><dt>' + val.name + '</dt><dd></dd><input type="hidden" name="metroId" value="' + val.id + '"/></dl>');


				$Item.find('dd').click(function () {
					//console.log('удалить');
					oMetro.toggleStation(val.id, false);
					$Item.remove();

					/* dd */
					var $relatedElements = $(".js-stationselect" + "[data-station-id=" + val.id + "]");
					$relatedElements.removeClass("s-active");
					/* dd end */
				});

				$MetroSelected.append($Item);
			});
			$MetroSelected.css('height', 'auto');

			var hew_h = $MetroSelected.height();

			$MetroSelected
				.css('height', h + 'px')
			;


		});


		myMetro = oMetro;

		$('.als_metro_toggle_scheme').click(function (e) {
			e.stopPropagation();
			var opened = $('.metrobox').hasClass('opened');
			if (!opened) {
				$(this).hide();
				$('.metro_preloader').show();
			}

			if (!opened) {

				oMetro.show(function () {
					oMetro.setSelectedStations($MetroSelected.find('dl').map(function () {
						return $(this).attr('rel');
					}));

					oMetro.node.trigger('mapchange');
					$('.als_metro_toggle_scheme').html('Скрыть схему метро').show();
					$('.metro_preloader').hide();
				});
			} else {
				oMetro.hide();
				$('.als_metro_toggle_scheme').html('Показать схему метро');
				$('.metro_selected_title, .metro_selected, .als_metro_deselect').toggle($.trim($('.metro_selected').html()).length > 0);
			}
		});

		$('.ex_location_map_trigger_metro').click(function () {
			$('.ex_location_map_triggers .pseudo').removeClass('active');
			$(this).addClass('active');
			if ($.trim($('.metro_selected').html()).length > 0 && !$('.metrobox').hasClass('opened')) {
				$('.metro_selected_title, .metro_selected, .als_metro_deselect').show();
			}

			else {
				$('.metro_preloader').show();
				$('.als_metro_toggle_scheme').hide();
				oMetro.show(function () {
					//console.log("shown!");
					oMetro.setSelectedStations($MetroSelected.find('dl').map(function () {
						return $(this).attr('rel');
					}));
					oMetro.node.trigger('mapchange');
					$('.als_metro_toggle_scheme').html('Скрыть схему метро').show();
					$('.metro_preloader').hide();
				});

			}

		}).click()
		;
		$('.ex_location_map_trigger_ymap').click(function () {
			var
				obl_id = $('.sf_head_region_button').attr('rel'),
				isMetro = obl_id == 10 || obl_id == 1 || obl_id == -1;
			;

			$('.ex_location_map_triggers .pseudo').removeClass('active');
			$(this).addClass('active');

			$('.ex_location_map_triggers .ex_location_select_button').removeClass('ex_location_select_button_active');
			$(this).addClass('ex_location_select_button_active');
			$('.ex_location_type[rel=location_metro]').hide();
			oMetro.hide();
		});
	});
}