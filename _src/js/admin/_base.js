/*global jQuery:false */
/*global UIkit:false */
/*global $:false */
/*global window:false */
/*global document:false */
/*global hammock:false */

jQuery(function($) {
	$(".hammock-date-filter").datepicker({
		dateFormat:'yy-mm-dd',
		maxDate : "0"
	});

	hammock.helper.select2();

	hammock.helper.bind_date_range();

	/**
	 * Form submit
	 */
	$('body').on('submit', 'form.hammock-ajax-form', function(e){
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
					hammock.helper.notify(response.data.message, 'success');
					if ( typeof response.data.modal !== 'undefined' ) {
						var $modal = $(''+response.data.modal);
						UIkit.modal($modal).hide();
					}
					if ( typeof response.data.reload !== 'undefined' ) {
						window.location.reload();
					}
					
				} else {
					hammock.helper.notify(response.data, 'success');
				}
			} else {
				hammock.helper.notify(response.data, 'warning');
			}
		}).fail(function(xhr, status, error) {
			$button.removeAttr('disabled');
			$button.html($btn_txt);
			hammock.helper.notify(hammock.error, 'error');
		});
		return false;
	});


	/**
	 * Enable/disable module
	 */
	$('body').on('click', '.hammock-ajax-toggle', function(e){
		var $elem = $(this),
			modal_id = $elem.attr('data-id'),
			$nonce = $elem.attr('data-nonce'),
			$action = $elem.attr('data-action');
		$.post(
			window.ajaxurl,
			{ 'module' : modal_id, '_wpnonce' : $nonce, 'action' : $action }
		).done( function( response ) {
			if ( response.success === true ) {
				hammock.helper.notify(response.data, 'success');
			} else {
				if ($elem.is(':checked')) {
					$elem.attr('checked', false);
				}else{
					$elem.attr('checked', true);
				}
				hammock.helper.notify(response.data, 'warning');
			}
		}).fail(function(xhr, status, error) {
			if ($elem.is(':checked')) {
				$elem.attr('checked', false);
			}else{
				$elem.attr('checked', true);
			}
			hammock.helper.notify(hammock.error, 'error');
		});
	});

	/**
	 * Simple ajax link clicks
	 */
	$('body').on('click', '.hammock-ajax-click', function(e){
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
				hammock.helper.notify(response.data, 'success', function(){
					window.location.reload();
				});
			} else {
				hammock.helper.notify(response.data, 'warning');
			}
		}).fail(function(xhr, status, error) {
			$button.removeAttr('disabled');
			$button.html($btn_txt);
			hammock.helper.notify(hammock.error, 'error');
		});
	});

	//Check boxes
	$('body').on('click', '.hammock-top-checkbox', function(e){
		$('input:checkbox').not(this).prop('checked', this.checked);
	});

	$('body').on('click', '.hammock-bottom-checkbox', function(e){
		$('input:checkbox').not(this).prop('checked', this.checked);
	});

	/**
	 * Drop down gateway mode on change
	 */
	$('body').on('change', '.hammock-mode-select', function(e){
		var $elem = $(this),
			$val = $elem.val(),
			$target = $elem.attr('data-target');
		$('.hammock-' + $target).hide();
		$('.' + $target + '-' + $val ).show();
	});
});