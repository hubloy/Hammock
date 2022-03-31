<?php
namespace HubloyMembership\View\Backend\Addons;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use HubloyMembership\Base\View;

/**
 * Media Settings view
 *
 * @since 1.0.0
 */
class Media extends View {


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

