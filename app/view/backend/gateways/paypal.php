<?php
namespace HubloyMembership\View\Backend\Gateways;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use HubloyMembership\Base\View;

class Paypal extends View {

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
			<label class="uk-form-label" for="form-stacked-text"><?php esc_html_e( 'Mode', 'hubloy-membership' ); ?></label>
			<div class="uk-form-controls">
				<?php
					$this->ui->render(
						'dropdown',
						array(
							'name'       => 'paypal_mode',
							'selected'   => $mode,
							'class'      => 'hubloy_membership-mode-select',
							'attributes' => array(
								'data-target' => 'paypal',
							),
							'values'     => array(
								'live' => __( 'Live', 'hubloy-membership' ),
								'test' => __( 'Sandbox', 'hubloy-membership' ),
							),
						)
					);
				?>
			</div>
		</div>
		<div class="uk-margin hubloy_membership-paypal paypal-live" <?php echo $mode != 'live' ? 'style="display:none"' : ''; ?>>
			<label class="uk-form-label" for="form-stacked-select"><?php esc_html_e( 'Live Business Email', 'hubloy-membership' ); ?></label>
			<div class="uk-form-controls">
				<?php
					$this->ui->render(
						'input',
						array(
							'name'        => 'paypal_email',
							'class'       => 'uk-input',
							'value'       => isset( $settings['paypal_email'] ) ? $settings['paypal_email'] : '',
							'placeholder' => '',
						)
					);
				?>
			</div>
		</div>
		<div class="uk-margin hubloy_membership-paypal paypal-live" <?php echo $mode != 'live' ? 'style="display:none"' : ''; ?>>
			<label class="uk-form-label" for="form-stacked-select"><?php esc_html_e( 'Live Merchant ID', 'hubloy-membership' ); ?></label>
			<div class="uk-form-controls">
				<?php
					$this->ui->render(
						'input',
						array(
							'name'        => 'paypal_merchant_id',
							'class'       => 'uk-input',
							'type'        => 'password',
							'value'       => isset( $settings['paypal_merchant_id'] ) ? $settings['paypal_merchant_id'] : '',
							'placeholder' => '',
						)
					);
				?>
			</div>
		</div>
		<div class="uk-margin hubloy_membership-paypal paypal-test" <?php echo $mode != 'test' ? 'style="display:none"' : ''; ?>>
			<label class="uk-form-label" for="form-stacked-select"><?php esc_html_e( 'Sandbox Business Email', 'hubloy-membership' ); ?></label>
			<div class="uk-form-controls">
				<?php
					$this->ui->render(
						'input',
						array(
							'name'        => 'test_paypal_email',
							'class'       => 'uk-input',
							'value'       => isset( $settings['test_paypal_email'] ) ? $settings['test_paypal_email'] : '',
							'placeholder' => '',
						)
					);
				?>
			</div>
		</div>
		<div class="uk-margin hubloy_membership-paypal paypal-test" <?php echo $mode != 'test' ? 'style="display:none"' : ''; ?>>
			<label class="uk-form-label" for="form-stacked-select"><?php esc_html_e( 'Sandbox Merchant ID', 'hubloy-membership' ); ?></label>
			<div class="uk-form-controls">
				<?php
					$this->ui->render(
						'input',
						array(
							'name'        => 'test_paypal_merchant_id',
							'class'       => 'uk-input',
							'type'        => 'password',
							'value'       => isset( $settings['test_paypal_merchant_id'] ) ? $settings['test_paypal_merchant_id'] : '',
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
