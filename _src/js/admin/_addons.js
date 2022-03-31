/*global jQuery:false */
/*global UIkit:false */
/*global $:false */
/*global window:false */
/*global document:false */
/*global hubloy_membership:false */

jQuery(function($) {


	$(document).on('click','.hubloy_membership-addon-toggle-setting', function() {
		var $item = $(this),
			$id = $item.attr('data-id'),
			$name = $item.attr('data-name'),
			$nonce = $item.attr('data-nonce'),
			$canvas = $('#addons-settings'),
			$content = $canvas.find('.hubloy_membership-canvas-content'),
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
			{ 'id' : $id, '_wpnonce' : $nonce, 'action' : 'hubloy_membership_addon_settings' }
		).done( function( response ) {
			if(response.success){
				$form.html(response.data.view);
			} else {
				$form.html(response.data);
			}
		}).fail(function(xhr, status, error) {
			hubloy_membership.helper.notify(hubloy_membership.error, 'error');
		});
	});
});