<?php
namespace Hammock\View\Backend\Gateways;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Base\View;

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
			<label class="uk-form-label" for="form-stacked-select"><?php _e( 'Payment Title', 'hammock' ); ?></label>
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
					<?php _e( 'All transactions with this gateway will be manually approved', 'hammock' ); ?>
				</p>
			</div>
			
		</div>
		<div class="uk-margin">
			<label class="uk-form-label" for="form-stacked-select"><?php _e( 'Payment Instructions', 'hammock' ); ?></label>
			<div class="uk-form-controls">
				<textarea class="uk-textarea" name="manual_instructions"><?php echo isset( $settings['manual_instructions'] ) ? $settings['manual_instructions'] : ''; ?></textarea>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}

?>
