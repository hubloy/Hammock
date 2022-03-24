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
		$rule = $this->data['rule'];
		$type = $this->data['type'];
		$url  = wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'hammock_rule_items',
					'type'   => $type,
				),
				admin_url( 'admin-ajax.php' )
			),
			'hammock_rule_items'
		);
		ob_start();
		?>
		<select name="item" data-placeholder="<?php esc_html_e( 'Select Item', 'hammock' ); ?>" class="uk-select hammock-select2-ajax" data-url="<?php echo esc_url( $url ); ?>" style="width: 100%">
			<?php
			if ( $rule ) {
				$content = $rule->to_html();
				?>
					<option value="<?php echo esc_attr( $content['object_id'] ); ?>" <?php selected( $rule->object_id, $content['object_id'] ); ?>><?php echo esc_html( wp_strip_all_tags( $content['title'] ) ); ?></option>
					<?php
			}
			?>
		</select>
		<?php
		return ob_get_clean();
	}
}
