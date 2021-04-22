<?php
namespace Hammock\View\Backend\Wizard;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Base\View;

/**
 * Wizard header
 */
class Header extends View {


	/**
	 * Builds template and return it as string.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function to_html() {
		$wp_version_class = 'branch-' . str_replace( array( '.', ',' ), '-', floatval( get_bloginfo( 'version' ) ) );
		ob_start();
		set_current_screen();
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width" />
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title><?php esc_html_e( 'JengaPress &rsaquo; Setup Wizard', 'hammock' ); ?></title>
			<?php do_action( 'admin_enqueue_scripts' ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_head' ); ?>
		</head>
		<body class="hammock-setup wp-core-ui <?php echo esc_attr( $wp_version_class ); ?>">
			<div class="uk-panel uk-padding-small uk-text-center uk-background-secondary uk-light">
				<a class="uk-logo" href="#" target="_blank"><?php esc_html_e( 'JengaPress', 'hammock' ); ?></a>
			</div>
			<div class="hammock-wizard">
				<ol>
					<li class="">Step 1</li>
					<li class="current">Step 2</li>
					<li class="">Step 3</li>
					<li class="">Ready to go!</li>
				</ol>
			</div>
			<div id="hammock-setup-wizard"></div>
		<?php
		return ob_get_clean();
	}
}
?>