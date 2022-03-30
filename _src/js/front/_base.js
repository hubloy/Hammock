/*global jQuery:false */
/*global UIkit:false */
/*global $:false */
/*global window:false */
/*global document:false */
/*global hammock:false */

jQuery(function ($) {

	/**
	 * Form submit
	 */
	$('body').on('submit', 'form.hammock-ajax-form', function (e) {
		var $form = $(this),
			$button = $form.find('button'),
			$btn_txt = $button.text();
		$button.attr('disabled', 'disabled');
		$button.html("<img src='" + hammock.assets.spinner + "' />");
		$.post(
			hammock.ajax_url,
			$form.serialize()
		).done(function (response) {
			$button.removeAttr('disabled');
			$button.html($btn_txt);
			if (response.success === true) {
				$form.trigger("reset");
				if (typeof response.data.message !== 'undefined') {
					hammock.helper.notify(response.data.message, 'success');
					if (typeof response.data.url !== 'undefined') {
						window.location.href = response.data.url;
					}
					if (typeof response.data.reload !== 'undefined') {
						window.location.reload();
					}

				} else {
					hammock.helper.notify(response.data, 'success');
				}
			} else {
				hammock.helper.notify(response.data, 'warning');
			}
		}).fail(function (xhr, status, error) {
			$button.removeAttr('disabled');
			$button.html($btn_txt);
			hammock.helper.notify(hammock.error, 'error');
		});
		return false;
	});

	//Switch
	$('body').on('click', '.hammock-link-switch', function (e) {
		e.preventDefault();
		var $elem = $(this),
			$target = $elem.attr('data-target'),
			$container = $($elem.attr('data-container')),
			$item = $container.find($target);

		$container.children('div').each(function (i, obj) {
			$(obj).addClass('hammock-hidden');
		});
		$item.removeClass('hammock-hidden');
	});

	$('body').on('submit', 'form.hammock-checkout-form', function (e) {
		var $form = $(this),
			$button = $form.find('button'),
			$btn_txt = $button.text();
		$button.attr('disabled', 'disabled');
		$button.html("<img src='" + hammock.assets.spinner + "' />");
		$.post(
			hammock.ajax_url,
			$form.serialize()
		).done(function (response) {
			if (response.success === true) {
				$( document.body ).trigger( 'hammock_checkout_success', [ response ] );
				if (typeof response.data.message !== 'undefined') {
					hammock.helper.notify(response.data.message, 'success');
					if (typeof response.data.link !== 'undefined') {
						window.location.href = response.data.link;
					}
				} else {
					hammock.helper.notify(response.data, 'success');
				}
			} else {
				hammock.helper.notify(response.data, 'warning');
			}
			$button.removeAttr( 'disabled' );
			$button.html( $btn_txt );
		}).fail(function (xhr, status, error) {
			$button.removeAttr('disabled');
			$button.html($btn_txt);
			hammock.helper.notify(hammock.error, 'error');
		});
		return false;
	});
});