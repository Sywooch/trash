
$(document).ready(function() {

	function affiliateReset($form) {
		$form.data('disable', false);

		$('.btn', $form).show();
		$('.success-msg', $form).hide();
		$('.error-msg', $form).hide();
	}

	function affiliateHide($form) {
		$form.data('disable', true);

		$('.success-msg', $form).hide();
		$('.error-msg', $form).hide();
	}

	function affiliateMessage($form, error) {
		$form.data('disable', true);

		$('.btn', $form).hide();
		if (error) {
			$('.error-msg', $form).html(error).show();
		} else {
			$('.success-msg', $form).show();
		}
	}

	$('a.parallax').click(function(event) {
		event.preventDefault();
		$('html, body').animate({
			scrollTop: $($(this).data('parallax')).offset().top - 30
		}, 500);
	});

	$(window).scroll(function(event) {
		var topOfWindow = $(window).scrollTop();
		var item = $('nav.menu a:first');
		$('nav.menu a').removeClass('active');
		$('.block').each(function(index, el) {
			if (topOfWindow + 50 > $(this).offset().top) {
				item = el;
			}
		});
		$('nav.menu a[data-parallax=".' + $(item).data('block') + '"]').addClass('active');
	});

	$('.AffiliateRegister input.contacts').keyup(function(event) {
		if (event.keyCode != 13) {
			affiliateReset($(this).closest('form.AffiliateRegister'));
		}
	});

	$('.AffiliateRegister input[name=offer]').change(function(event) {
		affiliateReset($(this).closest('form.AffiliateRegister'));
	});

	$('form.AffiliateRegister').submit(function(event) {
		var $form = $(this);

		if (!$form.data('disable') && $('input.contacts', $form).val()) {
			if ($('input[name=offer]:checked', $form).length > 0) {
				affiliateHide($form);

				$.ajax({
					url: this.action,
					method: 'post',
					data: $form.serialize(),
					dataType: 'json'
				})
					.done(function (json) {
						affiliateMessage($form, json.success ? false : json.error)
					})
					.error(function (json) {
						affiliateMessage($form, 'Произошла ошибка. Попробуйте еще раз');
					});
			} else {
				affiliateMessage($form, 'Для регистрации в партнерской программе согласитесь с условиями ниже');
			}
		}

		return false;
	});

});
