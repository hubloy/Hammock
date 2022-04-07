<?php
namespace HubloyMembership\Shortcode\Membership;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use HubloyMembership\Base\Shortcode;
use HubloyMembership\Model\Membership;

/**
 * Membership SignUp Button
 *
 * @since 1.0.0
 */
class Button extends Shortcode {

	/**
	 * Singletone instance of the shortcode.
	 *
	 * @since  1.0.0
	 *
	 * @var object
	 */
	private static $instance = null;

	/**
	 * Returns singleton instance of the shortcode.
	 *
	 * @since  1.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return object
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Get the shortcode content output
	 *
	 * @param array  $atts - the shortcode attributes
	 * @param string $content The content wrapped in the shortcode
	 *
	 * @since 1.0.0
	 */
	public function output( $atts, $content = '' ) {
		if ( isset( $atts['id'] ) ) {
			$membership = new Membership( (int) $atts['id'] );
			if ( ! $membership->is_valid() ) {
				return '';
			}
			ob_start();
			?>
			<a class="button hubloy_membership-account-membership-plan--price--can-join" href="<?php echo esc_url( hubloy_membership_get_account_page_links( 'view-plan', $membership->membership_id ) ); ?>">
				<?php
				if ( $membership->trial_enabled ) {
					esc_html_e( 'Begin Trial', 'hubloy_membership' );
				} else {
					esc_html_e( 'Join Membership', 'hubloy_membership' );
				}
				?>
			</a>
			<?php
			return ob_get_clean();
		}
		return '';
	}
}
