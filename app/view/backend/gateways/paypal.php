<?php
namespace Hammock\View\Backend\Gateways;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Base\View;

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
			<label class="uk-form-label" for="form-stacked-text"><?php esc_html_e( 'Mode', 'hammock' ); ?></label>
			<div class="uk-form-controls">
				<?php
					$this->ui->render(
						'dropdown',
						array(
							'name'     	=> 'paypal_mode',
							'selected' 	=> $mode,
							'class'     => 'hammock-mode-select',
							'attributes'=> array(
								'data-target' => 'paypal',
							),
							'values'   => array(
								'live' => __( 'Live', 'hammock' ),
								'test' => __( 'Sandbox', 'hammock' ),
							),
						)
					);
				?>
			</div>
		</div>
		<div class="uk-margin hammock-paypal paypal-live" <?php echo $mode != 'live' ? 'style="display:none"' : ''; ?>>
			<label class="uk-form-label" for="form-stacked-select"><?php esc_html_e( 'Live API Username', 'hammock-paypal' ); ?></label>
			<div class="uk-form-controls">
				<?php
					$this->ui->render(
						'input',
						array(
							'name'        => 'paypal_username',
							'class'       => 'uk-input',
							'value'       => isset( $settings['paypal_username'] ) ? $settings['paypal_username'] : '',
							'placeholder' => '',
						)
					);
				?>
			</div>
		</div>
		<div class="uk-margin hammock-paypal paypal-live" <?php echo $mode != 'live' ? 'style="display:none"' : ''; ?>>
			<label class="uk-form-label" for="form-stacked-select"><?php esc_html_e( 'Live API Password', 'hammock-paypal' ); ?></label>
			<div class="uk-form-controls">
				<?php
					$this->ui->render(
						'input',
						array(
							'name'        => 'paypal_password',
							'class'       => 'uk-input',
							'type'        => 'password',
							'value'       => isset( $settings['paypal_password'] ) ? $settings['paypal_password'] : '',
							'placeholder' => '',
						)
					);
				?>
			</div>
		</div>
		<div class="uk-margin hammock-paypal paypal-live" <?php echo $mode != 'live' ? 'style="display:none"' : ''; ?>>
			<label class="uk-form-label" for="form-stacked-select"><?php esc_html_e( 'Live API Signature', 'hammock-paypal' ); ?></label>
			<div class="uk-form-controls">
				<?php
					$this->ui->render(
						'input',
						array(
							'name'        => 'paypal_signature',
							'class'       => 'uk-input',
							'value'       => isset( $settings['paypal_signature'] ) ? $settings['paypal_signature'] : '',
							'placeholder' => '',
						)
					);
				?>
			</div>
		</div>
		<div class="uk-margin hammock-paypal paypal-test" <?php echo $mode != 'test' ? 'style="display:none"' : ''; ?>>
			<label class="uk-form-label" for="form-stacked-select"><?php esc_html_e( 'Sandbox API Username', 'hammock-paypal' ); ?></label>
			<div class="uk-form-controls">
				<?php
					$this->ui->render(
						'input',
						array(
							'name'        => 'test_paypal_username',
							'class'       => 'uk-input',
							'value'       => isset( $settings['test_paypal_username'] ) ? $settings['test_paypal_username'] : '',
							'placeholder' => '',
						)
					);
				?>
			</div>
		</div>
		<div class="uk-margin hammock-paypal paypal-test" <?php echo $mode != 'test' ? 'style="display:none"' : ''; ?>>
			<label class="uk-form-label" for="form-stacked-select"><?php esc_html_e( 'Sandbox API Password', 'hammock-paypal' ); ?></label>
			<div class="uk-form-controls">
				<?php
					$this->ui->render(
						'input',
						array(
							'name'        => 'test_paypal_password',
							'class'       => 'uk-input',
							'type'        => 'password',
							'value'       => isset( $settings['test_paypal_password'] ) ? $settings['test_paypal_password'] : '',
							'placeholder' => '',
						)
					);
				?>
			</div>
		</div>
		<div class="uk-margin hammock-paypal paypal-test" <?php echo $mode != 'test' ? 'style="display:none"' : ''; ?>>
			<label class="uk-form-label" for="form-stacked-select"><?php esc_html_e( 'Sandbox API Signature', 'hammock-paypal' ); ?></label>
			<div class="uk-form-controls">
				<?php
					$this->ui->render(
						'input',
						array(
							'name'        => 'test_paypal_signature',
							'class'       => 'uk-input',
							'value'       => isset( $settings['test_paypal_signature'] ) ? $settings['test_paypal_signature'] : '',
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
