<?php
namespace Hammock\Addon\Category;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Base\Addon;
use Hammock\Services\Memberships;

class Category extends Addon {

	/**
	 * The membership service
	 * 
	 * @since 1.0.0
	 * 
	 * @var object
	 */
	private $membership_service = null;

	/**
	 * Singletone instance of the addon.
	 *
	 * @since  1.0.0
	 */
	private static $instance = null;

	/**
	 * Returns singleton instance of the addon.
	 *
	 * @since  1.0.0
	 *
	 * @static
	 * @access public
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Addon init
	 */
	public function init() {
		$this->membership_service = new Memberships();
		$this->id = 'category';
	}

	/**
	 * Register addon
	 * Register a key value pair of addons
	 *
	 * @param array $addons - the current list of addons
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function register( $addons ) {
		if ( ! isset( $addons['category'] ) ) {
			$addons['category'] = array(
				'name'        => __( 'Category Protection', 'hammock' ),
				'description' => __( 'Protect your posts by category.', 'hammock' ),
				'icon'        => 'dashicons dashicons-category',
				'settings'    => true,
			);
		}
		return $addons;
	}

	/**
	 * Initialize the addon action
	 *
	 * @since 1.0.0
	 */
	public function init_addon() {
		$settings  = $this->settings();
		$protected = isset( $settings['protected'] ) ? $settings['protected'] : array();
		foreach ( $protected as $slug ) {
			add_filter( "manage_edit-{$slug}_columns", array( $this, 'protection_column' ), 10 );
			add_filter( "manage_{$slug}_custom_column", array( $this, 'protection_column_content' ), 10, 3);
			add_action( "{$slug}_edit_form_fields", array( $this, 'term_protection_fields' ), 10, 2 );
			add_action( "edited_{$slug}", array( $this, 'term_protection_update' ), 10, 2 );
		}
	}

	/**
	 * Add custom column to taxonomy selected
	 * 
	 * @param array $columns - the current columns
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function protection_column( $columns ) {
		$columns['hammock'] = __( 'Access', 'hammock' ); 
  		return $columns;
	}

	/**
	 * Custom column content
	 * 
	 * @param $value - the default value
	 * @param string $column_name - the current column name
	 * @param int $tax_id - the current taxonomy id
	 * 
	 * @since 1.0.0
	 * 
	 * @return string
	 */
	public function protection_column_content( $value, $column_name, $tax_id ) {
		if ( $column_name === 'hammock' ) {
			$access = get_term_meta( $tax_id, '_hammock_mebership_access', true );
			if ( !is_array( $access ) ) {
				$access = array();
			}
			return empty( $access ) ? __( 'All', 'hammock' ) : sprintf( __( '%d membership(s)', 'hammock' ), count( $access ) );
		}
		return $value;
	}

	/**
	 * Edit taxonomy field
	 * 
	 * @param WP_Term $tag -Current taxonomy term object.
	 * @param string  $taxonomy - Current taxonomy slug.
	 * 
	 * @since 1.0.0
	 * 
	 * @return string
	 */
	public function term_protection_fields( $tag, $taxonomy ) {
		$access 		= get_term_meta( $tag->term_id, '_hammock_mebership_access', true );
		$memberships 	= $this->membership_service->list_simple_memberships( 0, false );
		if ( !is_array( $access ) ) {
			$access = array();
		}
		?>
		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="membership"><?php _e( 'Membership Access', 'hammock' ); ?></label>
			</th>
			<td>
				<select name="hammock_membership[]" data-placeholder="<?php _e( 'Select Memberships', 'hammock' ); ?>" multiple class="hammock-multi-select">
					<?php 
						foreach ( $memberships as $id => $name ) {
							?>
							<option value="<?php echo $id; ?>" <?php echo in_array( $id, $access ) ? 'selected' : ''; ?>><?php echo $name; ?></option>
							<?php
						}
					?>
					
				</select>
				<p class='description'><?php _e( 'Membership access to items under this category', 'hammock' ); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Handle term update
	 * 
	 * @param int $term_id Term ID.
     * @param int $tt_id   Term taxonomy ID.
	 * 
	 * @since 1.0.0
	 */
	public function term_protection_update( $term_id, $tt_id ) {
		if ( isset( $_POST['hammock_membership'] ) ) {
			$memberships = array_map( 'sanitize_text_field', wp_unslash( $_POST['hammock_membership'] ) );
			update_term_meta( $term_id, '_hammock_mebership_access', $memberships );
		} else {
			update_term_meta( $term_id, '_hammock_mebership_access', array() );
		}
	}

	/**
	 * Addon settings
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function settings_page() {
		$view       = new \Hammock\View\Backend\Addons\Category();
		$settings   = $this->settings();
		$view->data = array(
			'settings' => $settings,
		);
		return $view->render( true );
	}


	/**
	 * Update addon settings
	 *
	 * @param array $response
	 * @param array $data - the post data
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function update_settings( $response = array(), $data ) {
		$protected = $data['protected'];
		$protected = array_map( 'sanitize_text_field', wp_unslash( $protected ) );
		$settings  = $this->settings();
		$settings['protected'] = $protected;
		$this->settings->set_addon_setting( $this->id, $settings );
		$this->settings->save();
		return array(
			'status' => true,
			'message'	=> __( 'Category Setting updated', 'hammock' )
		);
	}


	/**
	 * Check if protection setting is active
	 * 
	 * @since 1.0.0
	 * 
	 * @return bool
	 */
	public function plugin_active() {
		$is_active = $this->settings->get_general_setting( 'content_protection', 0 );
		return $is_active == 1 ? true : false;
	}
}

