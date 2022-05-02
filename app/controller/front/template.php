<?php
namespace HubloyMembership\Controller\Front;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use HubloyMembership\Base\Controller;

/**
 * Template controller
 * Handles custom template hooks
 *
 * @since 1.0.0
 */
class Template extends Controller {

	/**
	 * Singletone instance of the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @var Controller
	 */
	private static $instance = null;

	/**
	 * Returns singleton instance of the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return Controller
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Initialize controller
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$this->add_action( 'hubloy_membership_account_pay_single_transaction_after', 'add_submit_button' );
	}

	/**
	 * Add the submit button
	 * 
	 * @param \HubloyMembership\Model\Codes\Invoice $invoice The invoice.
	 * 
	 * @since 1.1.0
	 * 
	 * @return string
	 */
	public function add_submit_button( $invoice ) {
		$membership = $invoice->get_plan()->get_membership();
		$disabled   = ( ! $membership->is_code_isted( $invoice->get_invite_code_id() ) ) ? 'disabled="disabled"' : '';
		?>
		<button type="submit" class="button alt" name="hubloy_membership_checkout" <?php esc_attr( $disabled ); ?> id="checkout" value="<?php esc_attr_e( 'Complete Order', 'memberships-by-hubloy' ) ?>"><?php esc_html_e( 'Complete Order', 'memberships-by-hubloy' ); ?></button>
		<?php
	}
}
