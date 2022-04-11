<?php
namespace HubloyMembership\View\Backend\Gateways;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use HubloyMembership\Base\View;

class Manual extends View {

	/**
	 * Builds template and return it as string.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function to_html() {
		$settings = $this->data['settings'];
		ob_start();
		?>
		<div class="uk-margin">
			<label class="uk-form-label" for="form-stacked-select"><?php esc_html_e( 'Payment Title', 'memberships-by-hubloy' ); ?></label>
			<div class="uk-form-controls">
				<?php
					$this->ui->render(
						'input',
						array(
							'name'        => 'manual_title',
							'class'       => 'uk-input',
							'value'       => isset( $settings['manual_title'] ) ? $settings['manual_title'] : '',
							'placeholder' => '',
						)
					);
				?>
				<p class="uk-text-meta">
					<?php esc_html_e( 'All transactions with this gateway will be manually approved', 'memberships-by-hubloy' ); ?>
				</p>
			</div>
			
		</div>
		<div class="uk-margin">
			<label class="uk-form-label" for="form-stacked-select"><?php esc_html_e( 'Payment Instructions', 'memberships-by-hubloy' ); ?></label>
			<div class="uk-form-controls">
				<textarea class="uk-textarea" name="manual_instructions"><?php echo isset( $settings['manual_instructions'] ) ? esc_html( $settings['manual_instructions'] ) : ''; ?></textarea>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}

?>
