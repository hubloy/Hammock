<?php
namespace HubloyMembership\View\Backend\Addons;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use HubloyMembership\Base\View;

/**
 * Category Settings view
 *
 * @since 1.0.0
 */
class Category extends View {


	/**
	 * Builds template and return it as string.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function to_html() {
		$settings   = $this->data['settings'];
		$protected  = isset( $settings['protected'] ) ? $settings['protected'] : array();
		$args       = array(
			'public'   => true,
			'_builtin' => false,
		);
		$taxonomies = get_taxonomies( $args, 'object' );
		$checked    = in_array( 'category', $protected );
		ob_start();
		?>
		<div class="uk-margin">
			<label class="uk-form-label" for="form-horizontal-text"><?php _e( 'Protect the following taxonomies', 'hubloy-membership' ); ?></label>
			<div class="uk-form-controls">
				<div class="uk-panel uk-panel-scrollable">
					<ul class="uk-list">
						<li><label><input class="uk-checkbox" name="protected[]" <?php echo $checked ? 'checked="checked"' : ''; ?> value="category" type="checkbox">&nbsp;&nbsp;<?php _e( 'Categories', 'hubloy-membership' ); ?></label></li>
						<?php
						if ( $taxonomies ) {
							?>
								<?php
								foreach ( $taxonomies as $taxonomy ) {
									$checked = in_array( $taxonomy->name, $protected );
									?>
										<li><label><input class="uk-checkbox" name="protected[]" <?php echo $checked ? 'checked="checked"' : ''; ?> value="<?php echo esc_attr( $taxonomy->name ); ?>" type="checkbox">&nbsp;&nbsp;<?php echo esc_html( $taxonomy->labels->name ); ?></label></li>
									<?php
								}
								?>
							<?php
						}
						?>
					</ul>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}

