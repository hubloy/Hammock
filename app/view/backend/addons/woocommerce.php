<?php
namespace Hammock\View\Backend\Addons;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Base\View;

/**
 * Woocommerce Settings view
 *
 * @since 1.0.0
 */
class Woocommerce extends View {


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

		<?php
		return ob_get_clean();
	}
}

