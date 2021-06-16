<?php
namespace Hammock\View\Backend\Addons;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Base\View;

/**
 * Mailchimp Settings view
 *
 * @since 1.0.0
 */
class Mailchimp extends View {


	/**
	 * Builds template and return it as string.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function to_html() {
		$settings 		= $this->data['settings'];
		$optin 			= isset( $settings['optin'] ) ? $settings['optin'] : true;
		$apikey 		= isset( $settings['mailchimp_apikey'] ) ? $settings['mailchimp_apikey'] : '';
		$reg_list 		= isset( $settings['mailchimp_registered_list'] ) ? $settings['mailchimp_registered_list'] : '';
		$sub_list 		= isset( $settings['mailchimp_subscriber_list'] ) ? $settings['mailchimp_subscriber_list'] : '';
		$unsub_liet 	= isset( $settings['mailchimp_unsubscriber_list'] ) ? $settings['mailchimp_unsubscriber_list'] : '';
		ob_start();
		?>
		<div class="uk-margin">
			<label class="uk-form-label" for="apikey"><?php _e( 'MailChimp API Key', 'hammock' ); ?></label>
			<div class="uk-form-controls uk-grid-small" uk-grid>
				<div class="uk-width-3-4">
					<?php
						$this->ui->render(
							'input',
							array(
								'name'        => 'apikey',
								'class'       => 'uk-input uk-form-width-large',
								'value'       => $apikey,
								'placeholder' => '',
							)
						);
					?>
					<p class="uk-text-meta">
						<?php echo sprintf( __( 'Visit <a href="%s" target="_blank">your API dashboard</a> to create an API Key.', 'hammock' ), "http://admin.mailchimp.com/account/api" ); ?>
					</p>
				</div>
				<div class="uk-width-1-4">
					<a class="uk-button uk-button-secondary uk-button-small"><?php _e( 'Validate', 'hammock' ); ?></a>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}

