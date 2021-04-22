/*! Hammock  - v1.0.0
 * https://www.hubloy.com
 * Copyright (c) 2021; * Licensed GPLv2+ */
/*global UIkit:false */
/*global $:false */
/*global window:false */
/*global document:false */
/*global hammock:false */
/*global Swal:false */

window.hammock = window.hammock || {};

hammock.helper = {

	notify : function(message, type, callback){
		Swal.fire({
			type: type,
			title: message,
			confirmButtonText: hammock.common.buttons.ok
		}).then(function(result) {
			if (result.value) {
				if(typeof callback !== 'undefined'){
					callback();
				}
			}
		});
	},

	confirm : function(message, type, callback){
		Swal.fire({
			type: type,
			title: message,
			showCancelButton: true,
			confirmButtonText: hammock.common.buttons.ok,
			cancelButtonText: hammock.common.buttons.cancel
		}).then(function(result) {
			if (result.value) {
				if(typeof callback !== 'undefined'){
					callback();
				}
			}
		});
	},

	alert : function(title, message, type, position, callback) {
		if(typeof callback === 'undefined'){
			callback = false;
		}
		if(typeof position === 'undefined'){
			position = 'nfc-top-right';
		}
		window.createNotification({
			closeOnClick: true,
			onclick: callback,
			displayCloseButton: true,
			positionClass: position,
			theme: type
		})({
			title: title,
			message: message
		});
	},
	
	/**
	 * Progress Loader
	 */
	loader : function(container_class,elem){
		jQuery("<div class='"+container_class+"'><img src='"+hammock.assets.spinner+"' class='hammock-spinner-center'/></div>").css({
			position: "absolute",
			width: "100%",
			height: "100%",
			top: 0,
			left: 0,
			background: "#ecebea",
			textAlign : 'center'
		}).appendTo(elem.css("position", "relative"));
	},

	bind_date_range : function() {
		if ( jQuery(".hammock-from-date").length ) {
			var $hammockFromDate = jQuery(".hammock-from-date").datepicker({
					dateFormat:'yy-mm-dd',
					minDate : "0",
					changeMonth: true,
					changeYear: true,
					numberOfMonths: 1
				}),
				$hammockToDate = jQuery(".hammock-to-date").datepicker({
					dateFormat:'yy-mm-dd',
					changeMonth: true,
					changeYear: true,
					numberOfMonths: 1
				});
			$hammockFromDate.on( "change", function() {
				$hammockToDate.datepicker( "option", "minDate", hammock.helper.get_date( this ) );
			});
			$hammockToDate.on( "change", function() {
				$hammockFromDate.datepicker( "option", "maxDate", hammock.helper.get_date( this ) );
			});
		}
	},

	get_date : function( element ) {
		var date;
		try {
			date = jQuery.datepicker.parseDate( 'yy-mm-dd', element.value );
		} catch( error ) {
			date = null;
		}

		return date;
	}
};
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

	$(".hammock-chosen-select").chosen({ no_results_text: hammock.no_results, width: "95%" });

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
/*global UIkit:false */
/*global $:false */
/*global window:false */
/*global document:false */
/*global hammock:false */

jQuery(function($) {

	//Show hide date range on modal
	$('body').on('change', '.hammock-membership-type', function(e){
		var $val = $(this).val();
		$('.hammock-membership-date').hide();
		$('.hammock-membership-recurring').hide();
		if ( $val === 'date-range' ) {
			$('.hammock-membership-date').show();
		} else if ( $val === 'recurring' ) {
			$('.hammock-membership-recurring').show();
		}
	});

	$('body').on('click', '.hammock-trial_enabled', function(e){
		$('.hammock-membership-trial').hide();
		if ( this.checked ) {
			$('.hammock-membership-trial').show();
		}
	});

	$('body').on('click', '.hammock-limit_spaces', function(e){
		$('.hammock-membership-limited').hide();
		if ( this.checked ) {
			$('.hammock-membership-limited').show();
		}
	});
});
/*global UIkit:false */
/*global $:false */
/*global window:false */
/*global document:false */
/*global hammock:false */

jQuery(function($) {


	$(document).on('click','.hammock-addon-toggle-setting', function() {
		var $item = $(this),
			$id = $item.attr('data-id'),
			$name = $item.attr('data-name'),
			$nonce = $item.attr('data-nonce'),
			$canvas = $('#addons-settings'),
			$content = $canvas.find('.hammock-canvas-content'),
			$title = $content.find('.addon-title'),
			$form = $content.find('.addon-content'),
			$id_field = $content.find('.addon_id');
		if ( UIkit.offcanvas($canvas).isToggled() ) {
			UIkit.offcanvas($canvas).close();
		}
		$id_field.val($id);
		$title.html($name);
		$form.html('<span uk-spinner="ratio: 4.5"></span>');

		UIkit.offcanvas($canvas).show();

		$.post(
			window.ajaxurl,
			{ 'id' : $id, '_wpnonce' : $nonce, 'action' : 'hammock_addon_settings' }
		).done( function( response ) {
			if(response.success){
				$form.html(response.data.view);
			} else {
				$form.html(response.data);
			}
		}).fail(function(xhr, status, error) {
			hammock.helper.notify(hammock.error, 'error');
		});
	});
});