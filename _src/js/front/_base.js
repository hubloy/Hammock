/*global jQuery:false */
/*global UIkit:false */
/*global $:false */
/*global window:false */
/*global document:false */
/*global hubloy_membership:false */

jQuery(function ($) {

	/**
	 * Form submit
	 */
	$('body').on('submit', 'form.hubloy_membership-ajax-form', function (e) {
		var $form = $(this),
			$button = $form.find('button'),
			$btn_txt = $button.text();
		$button.attr('disabled', 'disabled');
		$button.html("<img src='" + hubloy_membership.assets.spinner + "' />");
		$.post(
			hubloy_membership.ajax_url,
			$form.serialize()
		).done(function (response) {
			$button.removeAttr('disabled');
			$button.html($btn_txt);
			if (response.success === true) {
				$form.trigger("reset");
				if (typeof response.data.message !== 'undefined') {
					hubloy_membership.helper.notify(response.data.message, 'success');
					if (typeof response.data.url !== 'undefined') {
						window.location.href = response.data.url;
					}
					if (typeof response.data.reload !== 'undefined') {
						window.location.reload();
					}

				} else {
					hubloy_membership.helper.notify(response.data, 'success');
				}
			} else {
				hubloy_membership.helper.notify(response.data, 'warning');
			}
		}).fail(function (xhr, status, error) {
			$button.removeAttr('disabled');
			$button.html($btn_txt);
			hubloy_membership.helper.notify(hubloy_membership.error, 'error');
		});
		return false;
	});

	//Switch
	$('body').on('click', '.hubloy_membership-link-switch', function (e) {
		e.preventDefault();
		var $elem = $(this),
			$target = $elem.attr('data-target'),
			$container = $($elem.attr('data-container')),
			$item = $container.find($target);

		$container.children('div').each(function (i, obj) {
			$(obj).addClass('hubloy_membership-hidden');
		});
		$item.removeClass('hubloy_membership-hidden');
	});

	$('body').on('submit', 'form.hubloy_membership-checkout-form', function (e) {
		var $form = $(this),
			$button = $form.find('button'),
			$btn_txt = $button.text();
		$button.attr('disabled', 'disabled');
		$button.html("<img src='" + hubloy_membership.assets.spinner + "' />");
		$.post(
			hubloy_membership.ajax_url,
			$form.serialize()
		).done(function (response) {
			if (response.success === true) {
				$(document.body).trigger('hubloy_membership_checkout_success', [response]);
				if (typeof response.data.message !== 'undefined') {
					hubloy_membership.helper.notify(response.data.message, 'success');
					if (typeof response.data.url !== 'undefined') {
						window.location.href = response.data.url;
					}
				} else {
					hubloy_membership.helper.notify(response.data, 'success');
				}
			} else {
				hubloy_membership.helper.notify(response.data, 'warning');
			}
			$button.removeAttr('disabled');
			$button.html($btn_txt);
		}).fail(function (xhr, status, error) {
			$button.removeAttr('disabled');
			$button.html($btn_txt);
			hubloy_membership.helper.notify(hubloy_membership.error, 'error');
		});
		return false;
	});

	// Coupon code verification
	$('body').on('click', 'a[name="apply_coupon"]', function (e) {
		e.preventDefault();
		var $button = $(this),
			$btn_txt = $button.text(),
			$invoice = $button.attr('data-invoice'),
			$nonce = $button.attr('data-nonce'),
			$code_input = $('input[name="coupon_code"]'),
			$code = $code_input.val(),
			$amount = $('.hubloy-membership-invoice-amount');

		if ( ! $code ) {
			$code_input.focus();
		} else {
			$.post(
				window.ajaxurl,
				{ 'code' : code, 'invoice' : $invoice, '_wpnonce' : $nonce, 'action' : 'hubloy_membership_validate_coupon_code' }
			).done( function( response ) {
				$button.removeAttr('disabled');
				$button.html( $btn_txt );
				if ( response.success === true ) {
					hubloy_membership.helper.notify( response.data.message, 'success');
					$amount.html( response.data.total );
				} else {
					hubloy_membership.helper.notify(response.data, 'warning');
				}
			}).fail(function(xhr, status, error) {
				$button.removeAttr('disabled');
				$button.html( $btn_txt );
				hubloy_membership.helper.notify(hubloy_membership.error, 'error');
			});
		}
	});
});