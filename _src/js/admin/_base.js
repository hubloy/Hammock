/*global jQuery:false */
/*global UIkit:false */
/*global $:false */
/*global window:false */
/*global document:false */
/*global hubloy_membership:false */

jQuery(function($) {
	$(".hubloy_membership-date-filter").datepicker({
		dateFormat:'yy-mm-dd',
		maxDate : "0"
	});

	hubloy_membership.helper.select2();

	hubloy_membership.helper.bind_date_range();

	/**
	 * Form submit
	 */
	$('body').on('submit', 'form.hubloy_membership-ajax-form', function(e){
		var $form = $(this),
			$button = $form.find('button'),
			$btn_txt = $button.text();
		$button.attr('disabled', 'disabled');
		$button.html("<div uk-spinner></div>");
		$.post(
			window.ajaxurl,
			$form.serialize()
		).done( function( response ) {
			$button.removeAttr('disabled');
			$button.html($btn_txt);
			if ( response.success === true ) {
				$form.trigger("reset");
				if ( typeof response.data.message !== 'undefined' ) {
					hubloy_membership.helper.notify(response.data.message, 'success');
					if ( typeof response.data.modal !== 'undefined' ) {
						var $modal = $(''+response.data.modal);
						UIkit.modal($modal).hide();
					}
					if ( typeof response.data.reload !== 'undefined' ) {
						window.location.reload();
					}
					
				} else {
					hubloy_membership.helper.notify(response.data, 'success');
				}
			} else {
				hubloy_membership.helper.notify(response.data, 'warning');
			}
		}).fail(function(xhr, status, error) {
			$button.removeAttr('disabled');
			$button.html($btn_txt);
			hubloy_membership.helper.notify(hubloy_membership.error, 'error');
		});
		return false;
	});


	/**
	 * Enable/disable module
	 */
	$('body').on('click', '.hubloy_membership-ajax-toggle', function(e){
		var $elem = $(this),
			modal_id = $elem.attr('data-id'),
			$nonce = $elem.attr('data-nonce'),
			$action = $elem.attr('data-action');
		$.post(
			window.ajaxurl,
			{ 'module' : modal_id, '_wpnonce' : $nonce, 'action' : $action }
		).done( function( response ) {
			if ( response.success === true ) {
				hubloy_membership.helper.notify(response.data, 'success');
			} else {
				if ($elem.is(':checked')) {
					$elem.attr('checked', false);
				}else{
					$elem.attr('checked', true);
				}
				hubloy_membership.helper.notify(response.data, 'warning');
			}
		}).fail(function(xhr, status, error) {
			if ($elem.is(':checked')) {
				$elem.attr('checked', false);
			}else{
				$elem.attr('checked', true);
			}
			hubloy_membership.helper.notify(hubloy_membership.error, 'error');
		});
	});

	/**
	 * Simple ajax link clicks
	 */
	$('body').on('click', '.hubloy_membership-ajax-click', function(e){
		e.preventDefault();
		var $button = $(this),
			$id = $button.attr('data-id'),
			$nonce = $button.attr('data-nonce'),
			$action = $button.attr('data-action'),
			$btn_txt = $button.text();
		$button.attr('disabled', 'disabled');
		$button.html("<div uk-spinner></div>");
		$.post(
			window.ajaxurl,
			{ 'id' : $id, '_wpnonce' : $nonce, 'action' : $action }
		).done( function( response ) {
			$button.removeAttr('disabled');
			$button.html($btn_txt);
			if ( response.success === true ) {
				hubloy_membership.helper.notify(response.data, 'success', function(){
					window.location.reload();
				});
			} else {
				hubloy_membership.helper.notify(response.data, 'warning');
			}
		}).fail(function(xhr, status, error) {
			$button.removeAttr('disabled');
			$button.html($btn_txt);
			hubloy_membership.helper.notify(hubloy_membership.error, 'error');
		});
	});

	//Check boxes
	$('body').on('click', '.hubloy_membership-top-checkbox', function(e){
		$('input:checkbox').not(this).prop('checked', this.checked);
	});

	$('body').on('click', '.hubloy_membership-bottom-checkbox', function(e){
		$('input:checkbox').not(this).prop('checked', this.checked);
	});

	/**
	 * Drop down gateway mode on change
	 */
	$('body').on('change', '.hubloy_membership-mode-select', function(e){
		var $elem = $(this),
			$val = $elem.val(),
			$target = $elem.attr('data-target');
		$('.hubloy_membership-' + $target).hide();
		$('.' + $target + '-' + $val ).show();
	});

	/**
	 * Update rule
	 */
	$('body').on('click', '.hubloy_membership-rule-update', function(e){
		e.preventDefault();
		var $button = $(this),
			$btn_txt = $button.text(),
			$container = $button.parent(),
			$id = $container.attr('data-id'),
			$item = $container.attr('data-item'),
			$selected = $container.find( '.hubloy_membership-chosen-select' ).val();

		$button.attr('disabled', 'disabled');
		$button.html("<div uk-spinner></div>");
		$.post(
			window.ajaxurl,
			{ 'id' : $id, 'item' : $item, 'selected' : $selected, '_wpnonce' : hubloy_membership.ajax_nonce, 'action' : 'hubloy_membership_update_rule' }
		).done( function( response ) {
			$button.removeAttr('disabled');
			$button.html($btn_txt);
			if ( response.success === true ) {
				hubloy_membership.helper.notify(response.data, 'success');
			} else {
				hubloy_membership.helper.notify(response.data, 'warning');
			}
		}).fail(function(xhr, status, error) {
			$button.removeAttr('disabled');
			$button.html($btn_txt);
			hubloy_membership.helper.notify(hubloy_membership.error, 'error');
		});
	});
});