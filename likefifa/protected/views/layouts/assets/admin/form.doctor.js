$(function() {
	
	var doctorCards = {},
	
	
	
	getCheckboxValues = function($selector) {
		var values = [];
		$selector
			.find('input[type="checkbox"]')
			.each(function() {
				if ($(this).attr('checked')) {
					values.push($(this).val());
				}
			});
		
		return values;
	},
	
	
	
	loadDoctorList = function() {
		var $this = $(this),
		$sectorSelector = null,
		$stationsSelector = null,
		
		updateList = function() {
			var data = {},
			val = $this.val() || $this.data('val');
			
			if ($sectorSelector) {
				data.sector = $sectorSelector.val();
			}
			
			if ($stationsSelector) {
				data.undergroundStations = getCheckboxValues($stationsSelector);
			}
			
			$.ajax({
				type: 'GET',
				url:'/admin/doctor/list/',
				data: {Doctor: data},
				dataType: 'html',
				success: function(data) {
					if (!data) {
						data = '<option value="">врачей нет</option>';
						$this.attr('disabled', 'disabled');
					}
					else {
						$this.removeAttr('disabled');
						if ($this.data('empty')) {
							data = '<option value="">не выбран</option>'+data;
							
						}
					}
					$this
						.html(data)
						.val(val)
						.trigger('change');
					
				}
			});
		};
		
		if ($this.data('sector')) {
			$sectorSelector = $($this.data('sector'));
			$sectorSelector.change(updateList);
		}
		
		if ($this.data('stations')) {
			$stationsSelector = $($this.data('stations'));
			$stationsSelector
				.find('input[type="checkbox"]')
					.change(updateList);
		}
		
		updateList();
	},
	
	
	
	loadDoctorCard = function() {
		var $this = $(this),
		$container = $this.prop('doctor-card'),
		id = $this.val(),
		
		renderCard = function() {
			$container.html(doctorCards[id]);
		};
		
		if (!id) {
			$container.html('');
			return;
		}
		
		if (!doctorCards[id]) {
			$container.html('');
			$.ajax({
				type: 'GET',
				url:'/admin/doctor/card/',
				data: {id: id},
				dataType: 'html',
				success: function(data) {
					doctorCards[id] = data;
					renderCard();
				}
			});
		}
		else {
			renderCard();
		}
	};
	
	
	
	$('.doctor-selector').each(function() {
		$(this)
			.find('select')
				.prop('doctor-card', $('.doctor-card'))
				.each(loadDoctorList)
				.change(loadDoctorCard);
		
	});
	
	
	
	$('.underground-stations-selector').each(function() {
		var $this = $(this),
		labels = {},
		ids = {},
		
		drawList = function() {
			var stationNames = [];
			$this.find('input[type="checkbox"]').each(function() {
				if ($(this).attr('checked')) stationNames.push(labels[$(this).attr('id')]);
			});
			
			$('.station-list').html(stationNames.join(', '));
		},
		
		init = function() {
			$this.find('label').each(function() {
				labels[$(this).attr('for')] = $(this).html();
				ids[$('#'+$(this).attr('for')).val()] = $(this).attr('for');
			});
			
			$this.find('.stations-select').click(function(e) {
				e.preventDefault();
				
				$("#overlayPopup").css("display","block").animate({opacity: 0.6}, 200);			
				$.get(
					"/popup/map",
					function(data){
						var stations = {}, splitIds = getCheckboxValues($this), i;
						
						for (i = 0; i < splitIds.length; i++) {
							stations[splitIds[i]] = true;
						}
						
						$("#popup-content")
							.html(data)
							.find('#map_stations div')
								.each(function() {
									if (stations[$(this).data('idline')]) {
										$(this).addClass('act');
									}
								});
						
						$("#close").click(function(){
							$("#overlayPopup").animate({opacity: 0}, 300, function(){$(this).css("display","none")});		
							$("#popup").fadeOut(200, function(){$("#popup-content").html("");});
						});
						
						$('#map-submit').click(function() {
							$("#popup-content")
								.find('#map_stations div')
									.each(function() {
										if (!$(this).hasClass('act')) {
											$('#'+ids[$(this).data('idline')]).removeAttr('checked');
										}
										else {
											$('#'+ids[$(this).data('idline')]).attr('checked', 'checked');
										}
									});
							
							drawList();
							
							$('#close').trigger('click');
						});
								
						$('#map_stations').trigger('station-light');
								
						if ($(window).height() > $("#popup").height())
							$top = $(document).scrollTop()+($(window).height()-$("#popup").height())/2;
						else
							$top = $(document).scrollTop();	
						$("#popup").removeClass("spec").removeClass("doctor-feedback doctor-feedback-form").fadeIn(300).css("top", $top);
					}
				);
			});
			
			drawList();
		};
		
		init();
		
	});
	
	$(document).keydown(function(e){
		if((e.which == 27) && ($("#popup:visible").length == 1))
		{
			$("#overlayPopup").animate({opacity: 0}, 300, function(){$(this).css("display","none")});		
			$("#popup").fadeOut(200, function(){$("#popup-content").html("");});	
		}
	});
	
	$('#doctor-form').submit(function(e) {
		var $this = $(this);
		if ($this.data('no-validate')) return;
		
		e.preventDefault();
		
		$this.find('input[type="submit"]')
			.attr('disabled', 'disabled')
			.blur();
		
		$.ajax({
			url: '/admin/doctor/check/',
			type: 'POST',
			dataType: 'json',
			data: {phone: $('#Doctor_phone').val(), name: $('#Doctor_name').val(), id: $this.data('id')},
			success: function(data) {
				if (
					(!parseInt(data.phoneCount) && !parseInt(data.nameCount)) 
					|| confirm('Врач с '+((data.phoneCount && data.nameCount) ? 'указанными именем и телефоном' : (data.phoneCount ? 'указанным телефоном' : 'указанным именем'))+' уже существует. Вы хотите продолжить сохранение?')
				) {
					
					$this.data('no-validate', true).submit();
				}
				else {
					$this.find('input[type="submit"]')
						.removeAttr('disabled');
				}
			}
		});
		
	});
	
});