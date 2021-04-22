/*global jQuery:false */
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