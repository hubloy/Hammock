<?php
namespace Hammock\View\Backend\Wizard;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Base\View;

/**
 * Wizard footer
 */
class Footer extends View {


	/**
	 * Builds template and return it as string.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function to_html() {
		ob_start();
		?>
			</body>
		</html>
		<?php
		return ob_get_clean();
	}
}
?>