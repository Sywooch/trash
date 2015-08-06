/**
 * MixPanel больше не используется
 */

function trimPhone(phone) {
	phone = phone.replace(/[^\d]/g, '');
	return phone.length == 10 ? '7' + phone : phone;
}

// SetApp - после нажатия кнопки “Записаться на прием” открывается форма, это событие должно отслеживать заполнение этой формы.
function setAppMixPanel(form, params) {

	mixpanel.identify(trimPhone(form['requestPhone'].value));

	if ($(form).hasClass('callback_form')) {
		trackCallBackForm(form, params);
	} else {
		trackRequestForm(form, params);
	}
}

function trackCallBackForm(form, params)
{
	mixpanel.track('CallMeBack', params.created ? { time: params.created - 1 } : {});
}

function trackRequestForm(form, params)
{
	var obj = {
		'Spec': null,
		'Clinic': null,
		'Metro': null,
		'Area': null,
		'Name': null,
		'Price': null,
		'Discount': null,
		'Reviews': null,
		'Rating': null,
		'Experience': null,
		'Awards': null,
		'Photo': null,
		'City': $('#CurrentCityName').text(),
		'Type': null,
		'DocID': null,
		'ClinID': null,
		'PartnerId': mixpanel_pid,
        'RatingStrategy': rating_strategy
	};

	if (params.created) {
		obj.time = params.created - 1;
	}

	if ($(form).hasClass('m-main')) {
		obj.Type = 'Mobile';
		if (form['requestSpec']) {
			obj.Spec = form['requestSpec'].value;
			obj.Metro = form['requestGeo'].value;
		}
	} else {
		var doctorId = form['doctor'] ? parseInt(form['doctor'].value, 10) : 0;
		var clinicId = form['clinic'] ? parseInt(form['clinic'].value, 10) : 0;

		if (clinicId > 0) {
			var $inputData = $('form.form_request_clinic input[data-clinic-id='+clinicId+']');
			obj.ClinID = clinicId;
			if ($inputData.length > 0) {
				obj.Clinic = $inputData.data('clinic-name');
				obj.Metro = $inputData.data('clinic-metro');
				obj.Area = $inputData.data('clinic-area');
				obj.Type = form['formType'].value;
			}
		}

		if (doctorId > 0) {
			var $inputData = $('form.doctor_desc__request input[data-doctor-id='+doctorId+']');
			obj.DocID = doctorId;
			if ($inputData.length > 0) {
				obj.Clinic = $inputData.data('clinic-name');
				obj.Metro = $inputData.data('clinic-metro');
				obj.Name = $inputData.data('doctor-name');
				obj.Price = $inputData.data($inputData.data('doctor-special-price') > 0 ? 'doctor-special-price' : 'doctor-price');
				obj.Discount = $inputData.data('doctor-special-price') > 0;
				obj.Reviews = $inputData.data('doctor-reviews');
				obj.Rating = $inputData.data('doctor-rating');
				obj.Experience = parseInt($inputData.data('doctor-experience'), 10);
				obj.Awards = $inputData.data('doctor-awards');
				obj.Photo = $inputData.data('doctor-image') ? true : false;
				obj.Spec = $inputData.data('doctor-spec').split(', ');
				obj.Type = form['formType'].value;
			}
		}
	}

	if (obj.Type) {
		mixpanel.people.set({ 'Name': form['requestName'].value });
		mixpanel.track('SetApp', obj);
	}
}

$(function() {
	var specVal = $('.search_input_spec').val();
	var locationVal = $('.search_input_geo').val();
	var locationType = $('form.search_form input[name=search_location_type]').val();

	if (!specVal) {
		specVal = $('form.search_form input[name=spec_init]').val();
	}
	if (!locationVal) {
		locationVal = $('form.search_form input[name=geo_init]').val();
	}

	// ChangeCity - вызывается при изменении города
	$('#ChangeCityBlock form').submit(function() {
		mixpanel.track('ChangeCity', {
			'City': $('#ChangeCityBlock li[data-cityid='+this['cityid'].value+']').text(),
			'PartnerID': mixpanel_pid,
            'RatingStrategy': rating_strategy
		});
	});

	// SpecLink - переход по одной из специальностей из блока под поиском
	mixpanel.track_links('main a.spec_list_link', 'SpecLink', function(link) {
		return {
			'Spec': link.innerText,
			'PartnerID': mixpanel_pid,
            'RatingStrategy': rating_strategy
		};
	});

	// Search - поиск врачей через большой блок сверху, для главной страницы
	// ChangeSpec - если в верхнем блоке поиска поменяли специальность
	// ChangeLoc - поиск врачей через большой блок сверху
	$('form.search_form').submit(function() {
		var spec = $('.search_input_spec').val();
		var geo = $('.search_input_geo').val();
		if ($('.mainpage form.search_form').length > 0) {
			mixpanel.track('Search', {
				'Spec': spec ? spec : null,
				'Location': geo ? geo : null,
				'LocType': geo ? locationType : null,
				'LocMulti': geo ? geo.indexOf(',') > 0 : null,
				'PartnerID': mixpanel_pid,
                'RatingStrategy': rating_strategy
			});
		}
		else {
			if (spec && specVal != spec) {
				mixpanel.track('ChangeSpec', {
					'Spec': spec,
					'PartnerID': mixpanel_pid,
                    'RatingStrategy': rating_strategy
				});
			}
			if (geo && locationVal != geo) {
				mixpanel.track('ChangeLoc', {
					'Location': geo,
					'LocType': locationType,
					'LocMulti': geo.indexOf(',') > 0,
					'PartnerID': mixpanel_pid,
                    'RatingStrategy': rating_strategy
				});
			}
		}
	});

	// VisitHome - изменен чекбокс “Вызов на дом”. Свойство checked: “checked”, “unchecked”
	mixpanel.track_links('a.link-departure', 'VisitHome', function(link) {
		return {
			'Checked': $('.filter_input_checkbox', link).attr('checked') ? 'unchecked' : 'checked',
			'PartnerID': mixpanel_pid,
            'RatingStrategy': rating_strategy
		};
	});

	// Sorting - изменена сортировка. Свойство SortType: “Стаж”, “Рейтинг”, “Цена”
	mixpanel.track_links('.filter_sort a', 'Sorting', function(link) {
		return {
			'SortType': link.innerText,
			'PartnerID': mixpanel_pid,
            'RatingStrategy': rating_strategy
		};
	});

	// GoToLink - переход по одной из ссылок на странице, которые не указаны выше (например, из футера сайта)
	mixpanel.track_links('.track_links a', 'GoToLink', function(link) {
		return {
			'GoToLink': link.innerText,
			'PartnerID': mixpanel_pid,
            'RatingStrategy': rating_strategy
		};
	});

	// FormSetApp - фиксирует нажатие кнопки “Записаться на прием”
	$('form.doctor_desc__request input.ui-btn').click(function() {
        mixpanel.track(
            'FormSetApp', {
                'PartnerID': mixpanel_pid,
                'RatingStrategy': rating_strategy
            });
	});

	$(document).on('requestCreated', function(e, obj, req_id, params){
		setAppMixPanel(obj, params);
	})

    var mixpanel_tracker = {
        HomePage: function (params) {
            mixpanel.track('HomePage', {
                'PartnerID': mixpanel_pid,
                'RatingStrategy': rating_strategy
            });
        },
        SearchPage: function (params) {

            mixpanel.track(
                'SearchPage',
                {
                    'Spec': params.Spec,
                    'Location': params.Location,
                    'LocType': params.LocType,
                    'LocMulti': params.LocMulti,
					'PartnerID': mixpanel_pid,
                    'RatingStrategy': rating_strategy
                }
            );
        },
        DoctorPage: function(params){
            mixpanel.track(
                'DoctorPage',
                {
                    'Spec':params.Spec,
                    'Clinic': params.Clinic,
                    'Metro' : params.Metro,
                    'Area': params.Area,
                    'Name':params.Name,
                    'Price': params.Price,
                    'Discount' : params.Discount,
                    'Reviews': params.Reviews,
                    'Rating': params.Rating,
                    'Experience' : params.Experience,
                    'Awards': params.Awards,
                    'Photo':params.Photo,
                    'City': params.City,
                    'DocID' : params.DocID,
                    'ClinID': params.ClinID,
					'PartnerID': mixpanel_pid,
                    'RatingStrategy': rating_strategy
                }
            );
        }
    }

    if (window.global_track !== undefined) {
        if(mixpanel_tracker[global_track.name] != undefined && typeof(mixpanel_tracker[global_track.name]) === 'function'){
            mixpanel_tracker[global_track.name](global_track.params);
        }
    }

});
