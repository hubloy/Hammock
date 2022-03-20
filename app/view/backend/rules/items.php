<?php
namespace Hammock\View\Backend\Rules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Base\View;

/**
 * Items view
 * Manages rule item settings.
 * 
 * @since 1.0.0
 */
class Items extends View {

	/**
	 * Builds template and return it as string.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function to_html() {
		$rule  = $this->data['rule'];
		$id    = $this->data['id'];
		ob_start();
		?>
		<select data-placeholder="<?php esc_html_e( 'Select Item', 'hammock' ); ?>" class="hammock-select2-ajax" data-url="">
			<?php
				if ( $rule && $id  ) {
					$content = $rule->get_protected_item( $id );
					?><option value="<?php echo esc_attr( $content['id'] ); ?>" <?php selected( $rule->object_id, $content['id'] ); ?>><?php echo esc_html( $content['name'] ); ?></option><?php
				}
			?>
		</select>
		<?php
		return ob_get_clean();
	}
}
