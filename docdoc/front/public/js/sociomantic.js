
//default
var product = {};

var sociomantic_tracker = {
    track: function (name, params) {
      	if (this[name] != undefined && typeof(this[name]) === 'function') {
            this[name](params);
        }
    },
    SearchPage: function (params) {
        product.category = [params.City, params.Spec];
        (params.Location !== undefined && params.Location != '') && product.category.push(params.Location);
    },
    HomePage: function (params) {
        product.category = [params.City]
    },
    DoctorPage: function (params) {
		product = {
            identifier: params.DocID,
			fn: params.Spec.join(', '),
            brand: 'отзывы: ' + params.Reviews,
            description: 'Поможем найти врача на docdoc.ru',
            category: [params.City, params.SearchingSpec],
            amount: params.Amount,
            price: params.Price,
            currency: 'RUB',
            url: params.Url,
            photo: params.PhotoUrl
        };
    }
};

$(document).ready(function () {
	if (window.global_track !== undefined) {
		sociomantic_tracker.track(global_track.name, global_track.params);
	}

	$(document).on('requestPopupReady', function (e, params) {

        if (params.doctor_id !== undefined && window.sociomantic !== undefined) {
            sociomantic.sonar.adv['docdoc-ru'].clear();

            var amount = 0;

            if(params.special_price !== undefined && params.special_price !== ''){
                amount = parseFloat(params.special_price).toFixed(2);
            } else if(params.price !== undefined && params.price !== '') {
                amount = parseFloat(params.price).toFixed(2);
            }

            window.basket = {
                products: [
                    {
                        identifier: params.doctor_id,
                        amount: amount,
                        currency: 'RUB',
                        quantity: 1
                    }
                ]
            };

            sociomantic.sonar.adv['docdoc-ru'].track();
        }
    });


    $(document).on('requestCreated', function (e, obj, req_id) {
        if(window.sociomantic !== undefined){
            sociomantic.sonar.adv['docdoc-ru'].clear();
            window.lead = {transaction: req_id};
            sociomantic.sonar.adv['docdoc-ru'].track();
        }
    });
});
