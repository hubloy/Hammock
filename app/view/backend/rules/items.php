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
		$items = $this->data['items'];
		ob_start();
		?>
		<select data-placeholder="<?php esc_html_e( 'Select Item', 'hammock' ); ?>" class="hammock-select2">
			<?php
				$rule_item_id = $rule ? $rule->object_id : '';
				foreach ( $items as $id => $name ) {
					?><option value="<?php echo esc_attr( $id ); ?>" <?php selected( $rule_item_id, $id ); ?>><?php echo esc_html( $name ); ?></option><?php
				}
			?>
		</select>
		<?php
		return ob_get_clean();
	}
}
