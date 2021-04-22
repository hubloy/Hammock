<?php
namespace Hammock\View\Backend\Gateways;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Base\View;

class Stripe extends View {

	/**
	 * Builds template and return it as string.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function to_html() {
		$settings = $this->data['settings'];
		$mode     = isset( $settings['mode'] ) ? $settings['mode'] : 'live';
		ob_start();
		?>
		<div class="uk-margin">
			<label class="uk-form-label" for="form-stacked-text"><?php _e( 'Mode', 'hammock' ); ?></label>
			<div class="uk-form-controls">
				<?php
					$this->ui->render(
						'dropdown',
						array(
							'name'       => 'stripe_mode',
							'selected'   => $mode,
							'class'      => 'hammock-mode-select',
							'attributes' => array(
								'data-target' => 'stripe',
							),
							'values'     => array(
								'live' => __( 'Live', 'hammock' ),
								'test' => __( 'Sandbox', 'hammock' ),
							),
						)
					);
				?>
				<p class="uk-text-meta">
					<?php
					echo sprintf(
						__( 'You can find your Stripe API Keys in your %1$sAccount Settings%2$s.', 'hammock' ),
						'<a href="https://dashboard.stripe.com/account/apikeys" target="_blank">',
						'</a>'
					);
					?>
				</p>
			</div>
		</div>

		<div class="uk-margin hammock-stripe stripe-live" <?php echo $mode != 'live' ? 'style="display:none"' : ''; ?>>
			<label class="uk-form-label" for="form-stacked-select"><?php _e( 'Live Publishable Key', 'hammock' ); ?></label>
			<div class="uk-form-controls">
				<?php
					$this->ui->render(
						'input',
						array(
							'name'        => 'publishable_key',
							'class'       => 'uk-input',
							'value'       => isset( $settings['publishable_key'] ) ? $settings['publishable_key'] : '',
							'placeholder' => '',
						)
					);
				?>
			</div>
		</div>

		<div class="uk-margin hammock-stripe stripe-live" <?php echo $mode != 'live' ? 'style="display:none"' : ''; ?>>
			<label class="uk-form-label" for="form-stacked-select"><?php _e( 'Live Secret Key', 'hammock' ); ?></label>
			<div class="uk-form-controls">
				<?php
					$this->ui->render(
						'input',
						array(
							'name'        => 'secret_key',
							'class'       => 'uk-input',
							'value'       => isset( $settings['secret_key'] ) ? $settings['secret_key'] : '',
							'placeholder' => '',
						)
					);
				?>
			</div>
		</div>

		<div class="uk-margin hammock-stripe stripe-test" <?php echo $mode != 'test' ? 'style="display:none"' : ''; ?>>
			<label class="uk-form-label" for="form-stacked-select"><?php _e( 'Test Publishable Key', 'hammock' ); ?></label>
			<div class="uk-form-controls">
				<?php
					$this->ui->render(
						'input',
						array(
							'name'        => 'test_publishable_key',
							'class'       => 'uk-input',
							'value'       => isset( $settings['test_publishable_key'] ) ? $settings['test_publishable_key'] : '',
							'placeholder' => '',
						)
					);
				?>
			</div>
		</div>

		<div class="uk-margin hammock-stripe stripe-test" <?php echo $mode != 'test' ? 'style="display:none"' : ''; ?>>
			<label class="uk-form-label" for="form-stacked-select"><?php _e( 'Test Secret Key', 'hammock' ); ?></label>
			<div class="uk-form-controls">
				<?php
					$this->ui->render(
						'input',
						array(
							'name'        => 'test_secret_key',
							'class'       => 'uk-input',
							'value'       => isset( $settings['test_secret_key'] ) ? $settings['test_secret_key'] : '',
							'placeholder' => '',
						)
					);
				?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}

?>
