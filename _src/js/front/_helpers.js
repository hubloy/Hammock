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