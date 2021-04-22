<?php
namespace Hammock\View\Backend;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Base\View;

class Header extends View {

	/**
	 * Builds template and return it as string.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function to_html() {
		$url = is_multisite() ? network_admin_url( 'admin.php?page=hammock' ) : admin_url( 'admin.php?page=hammock' );
		ob_start();
		$this->ui->render( 'header', array( 'url' => $url ) );
		return ob_get_clean();
	}
}

?>