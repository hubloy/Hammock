/*global jQuery:false */
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
	},

	select2 : function() {
		jQuery(".hammock-chosen-select").chosen({ no_results_text: hammock.no_results, width: "95%" });
	}
};