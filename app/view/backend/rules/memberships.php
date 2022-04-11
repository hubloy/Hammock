<?php
namespace HubloyMembership\View\Backend\Rules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use HubloyMembership\Base\View;

/**
 * Memberships view
 * Manages rule membership settings.
 *
 * @since 1.0.0
 */
class Memberships extends View {

	/**
	 * Builds template and return it as string.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function to_html() {
		$rule        = $this->data['rule'];
		$memberships = $this->data['memberships'];
		ob_start();
		?>
		<select name="memberships" data-placeholder="<?php esc_html_e( 'Select Membership', 'memberships-by-hubloy' ); ?>" multiple="multiple" class="uk-select hubloy_membership-select2" style="width: 100%">
			<?php
				$rule_memberships = ( $rule && is_array( $rule->memberships ) ) ? $rule->memberships : array();
			foreach ( $memberships as $id => $name ) {
				$selected = in_array( $id, $rule_memberships, true );
				?>
					<option value="<?php echo esc_attr( $id ); ?>" <?php echo $selected ? 'selected="selected"' : ''; ?>><?php echo esc_html( $name ); ?></option>
					<?php
			}
			?>
		</select>
		<?php
		return ob_get_clean();
	}
}
