/*! Hammock  - v1.0.0
 * https://www.hammock-membership.com
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
			html: message,
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
			html: message,
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
	}
};
/*global UIkit:false */
/*global $:false */
/*global window:false */
/*global document:false */
/*global hammock:false */

jQuery(function($) {

	/**
	 * Form submit
	 */
	$('body').on('submit', 'form.hammock-ajax-form', function(e){
		var $form = $(this),
			$button = $form.find('button'),
			$btn_txt = $button.text();
		$button.attr('disabled', 'disabled');
		$button.html("<img src='"+hammock.assets.spinner+"' />");
		$.post(
			hammock.ajax_url,
			$form.serialize()
		).done( function( response ) {
			$button.removeAttr('disabled');
			$button.html($btn_txt);
			if ( response.success === true ) {
				$form.trigger("reset");
				if ( typeof response.data.message !== 'undefined' ) {
					hammock.helper.notify(response.data.message, 'success');
					if ( typeof response.data.link !== 'undefined' ) {
						window.location.href = response.data.link;
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

	//Switch
	$('body').on('click', '.hammock-link-switch', function(e){
		e.preventDefault();
		var $elem = $(this),
			$target = $elem.attr('data-target'),
			$container = $($elem.attr('data-container')),
			$item = $container.find($target);

		$container.children( 'div' ).each(function(i, obj) {
			$(obj).addClass('hammock-hidden');
		});
		$item.removeClass('hammock-hidden');
	});
});