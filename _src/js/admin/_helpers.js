/*global jQuery:false */
/*global UIkit:false */
/*global $:false */
/*global window:false */
/*global document:false */
/*global hubloy_membership:false */
/*global Swal:false */

window.hubloy_membership = window.hubloy_membership || {};

hubloy_membership.helper = {

	notify : function(message, type, callback){
		Swal.fire({
			type: type,
			title: message,
			confirmButtonText: hubloy_membership.common.buttons.ok
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
			confirmButtonText: hubloy_membership.common.buttons.ok,
			cancelButtonText: hubloy_membership.common.buttons.cancel
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
		jQuery("<div class='"+container_class+"'><img src='"+hubloy_membership.assets.spinner+"' class='hubloy_membership-spinner-center'/></div>").css({
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
		if ( jQuery(".hubloy_membership-from-date").length ) {
			var $hubloy_membershipFromDate = jQuery(".hubloy_membership-from-date").datepicker({
					dateFormat:'yy-mm-dd',
					minDate : "0",
					changeMonth: true,
					changeYear: true,
					numberOfMonths: 1
				}),
				$hubloy_membershipToDate = jQuery(".hubloy_membership-to-date").datepicker({
					dateFormat:'yy-mm-dd',
					changeMonth: true,
					changeYear: true,
					numberOfMonths: 1
				});
			$hubloy_membershipFromDate.on( "change", function() {
				$hubloy_membershipToDate.datepicker( "option", "minDate", hubloy_membership.helper.get_date( this ) );
			});
			$hubloy_membershipToDate.on( "change", function() {
				$hubloy_membershipFromDate.datepicker( "option", "maxDate", hubloy_membership.helper.get_date( this ) );
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
		jQuery( '.hubloy_membership-select2' ).select2();
		var container = jQuery( '.hubloy_membership-select2-ajax' );
		if ( container.length ) {
			var url = container.attr( 'data-url' );
			jQuery( '.hubloy_membership-select2-ajax' ).select2({
				ajax: {
					url: url,
					dataType: 'json',
					processResults: function (data) {
						return {
							results: data.data
						};
					}
				}
			});
		}
	}
};