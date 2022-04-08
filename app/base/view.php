<?php
namespace HubloyMembership\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Base view class
 * Used to render views
 *
 * @since 1.0.0
 *
 * @package JP
 */
class View extends Component {

	/**
	 * The storage of all data associated with this render.
	 *
	 * @since  1.0.0
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * UI object
	 * Used to render based on the UI class handling the elements
	 *
	 * @since  1.0.0
	 */
	protected $ui;

	/**
	 * Constructor.
	 *
	 * @since  1.0.0
	 *
	 * @param array $data The data what has to be associated with this render.
	 */
	public function __construct( $data = array() ) {

		$this->data = $data;
		$this->ui   = UI::instance();
	}

	/**
	 * Page header
	 *
	 * @since 1.0.0
	 *
	 * @return String
	 */
	protected function header() {
		return '';
	}


	/**
	 * Builds template and return it as string.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function to_html() {
		$content = $this->header();
		return apply_filters( 'hubloy_membership_base_view_to_html', $content );
	}

	/**
	 * Output the rendered template to the browser.
	 *
	 * @since  1.0.0
	 */
	public function render( $return = false ) {
		$html = $this->to_html();

		if ( $return ) {
			return apply_filters(
				'hubloy_membership_base_view_render',
				$html,
				$this
			);
		} else {
			echo esc_html(
				apply_filters(
					'hubloy_membership_base_view_render',
					$html,
					$this
				)
			);
		}

	}
}

