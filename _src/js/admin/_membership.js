/*global jQuery:false */
/*global UIkit:false */
/*global $:false */
/*global window:false */
/*global document:false */
/*global hubloy_membership:false */

jQuery(function($) {

	//Show hide date range on modal
	$('body').on('change', '.hubloy_membership-membership-type', function(e){
		var $val = $(this).val();
		$('.hubloy_membership-membership-date').hide();
		$('.hubloy_membership-membership-recurring').hide();
		if ( $val === 'date-range' ) {
			$('.hubloy_membership-membership-date').show();
		} else if ( $val === 'recurring' ) {
			$('.hubloy_membership-membership-recurring').show();
		}
	});

	$('body').on('click', '.hubloy_membership-trial_enabled', function(e){
		$('.hubloy_membership-membership-trial').hide();
		if ( this.checked ) {
			$('.hubloy_membership-membership-trial').show();
		}
	});

	$('body').on('click', '.hubloy_membership-limit_spaces', function(e){
		$('.hubloy_membership-membership-limited').hide();
		if ( this.checked ) {
			$('.hubloy_membership-membership-limited').show();
		}
	});
});