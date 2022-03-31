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
				$( document.body ).trigger( 'hubloy_membership_checkout_success', [ response ] );
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
			$button.removeAttr( 'disabled' );
			$button.html( $btn_txt );
		}).fail(function (xhr, status, error) {
			$button.removeAttr('disabled');
			$button.html($btn_txt);
			hubloy_membership.helper.notify(hubloy_membership.error, 'error');
		});
		return false;
	});
});