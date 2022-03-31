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
			html: message,
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
			html: message,
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
	}
};