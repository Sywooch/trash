$(function() {
	"use strict";

	// Search - поиск врачей на главной странице
	$(document).on('search', function(e, link, params) {
		mixpanel.track('Search', {
			'Spec': params.speciality ? params.speciality : null,
			'Location': params.metro ? params.metro : null,
			'LocType': params.metro ? 'Metro' : null,
			'LocMulti': false,
			'PartnerID': window.mixpanel_pid
		});
	});

	// Sorting - изменена сортировка
	$(document).on('changeSearchSort', function(e, input, value) {
		mixpanel.track('Sorting', {
			'SortType': $(input).find('option:selected').data('name'),
			'PartnerID': window.mixpanel_pid
		});
	});

	// FormSetApp - фиксирует нажатие кнопки “Записаться на прием”
	mixpanel.track_links('div.to-form-doctor-btn a', 'FormSetApp', function(link) {
		return { 'PartnerID': window.mixpanel_pid };
	});

	// SetApp - после нажатия кнопки “Записаться на прием” открывается форма, это событие должно отслеживать заполнение этой формы.
	$(document).on('requestCreated', function(e, form, data, params) {
		var $form = $(form);

		var obj = {
			'Spec': $form.data('doctor-spec'),
			'Clinic': $form.data('clinic-name'),
			'Metro': $form.data('clinic-metro'),
			'Area': null,
			'Name': $form.data('doctor-name'),
			'Price': $form.data($form.data('doctor-special-price') > 0 ? 'doctor-special-price' : 'doctor-price'),
			'Discount': $form.data('doctor-special-price') > 0,
			'Reviews': $form.data('doctor-reviews'),
			'Rating': $form.data('doctor-rating'),
			'Experience': parseInt($form.data('doctor-experience'), 10),
			'Awards': $form.data('doctor-awards'),
			'Photo': $form.data('doctor-image') ? true : false,
			'City': $form.data('city-name'),
			'Type': $form.data('form-type'),
			'DocID': parseInt($form.data('doctor-id'), 10),
			'ClinID': parseInt($form.data('clinic-id'), 10),
			'PartnerId': window.mixpanel_pid
		};

		if (params.created) {
			obj.time = params.created - 1;
		}

		if (obj.Type) {
			mixpanel.identify(trimPhone(data.phone));
			mixpanel.people.set({ 'Name': data.name });
			mixpanel.track('SetApp', obj);
		}
	});

	if (window.global_track !== undefined) {
		$.each(window.global_track, function(name, params) {
			params['PartnerID'] = window.mixpanel_pid;
			mixpanel.track(name, params);
		});
	}
});