<?php
namespace Hammock\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Template helper
 * Helper for plugin template files
 *
 * @since 1.0.0
 */
class Template {

	/**
	 * Template path
	 * The path to check in the theme and plugin
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function template_path() {
		return trailingslashit( self::template_directory() );
	}

	/**
	 * Template directory
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function template_directory() {
		return apply_filters( 'hammock_template_directory', 'hammock' );
	}

	/**
	 * Get the template file in the current theme.
	 *
	 * @param  string $template Template name.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_theme_template_file( $template ) {
		return get_stylesheet_directory() . '/' . apply_filters( 'hammock_template_directory', 'hammock', $template ) . '/' . $template;
	}

	/**
	 * Save a template
	 *
	 * @param string $template_code - Template contents.
	 * @param string $template_path - Template path.
	 *
	 * @since 1.0.0
	 *
	 * @return bool;
	 */
	public static function save_template( $template_code, $template_path ) {
		$saved = false;
		if ( ! empty( $template_code ) && ! empty( $template_path ) ) {
			$file 			= self::get_theme_template_file( $template_path );
			$code 			= wp_unslash( $template_code );
			$file_helper 	= new File();
			$saved 			= $file_helper->create_file( $file, $code );
		}
		return $saved;
	}

	/**
	 * Copy template to theme
	 *
	 * @param string $template - the relative template path in the templates directory
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function copy_to_theme( $template ) {
		$theme_file = self::get_theme_template_file( $template );
		if ( wp_mkdir_p( dirname( $theme_file ) ) && ! file_exists( $theme_file ) ) {
			$core_file = HAMMOCK_TEMPLATE_DIR . '/' . $template;
			// Copy template file.
			copy( $core_file, $theme_file );

			return true;
		}
		return false;
	}

	/**
	 * Remove theme template to reset
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function remove_template( $template ) {
		$theme_file = self::get_theme_template_file( $template );
		if ( file_exists( $theme_file ) ) {
			unlink( $theme_file );
			return true;
		}
		return false;
	}

	/**
	 * Get template part (for templates like the shop-loop).
	 *
	 * @param mixed  $slug Template slug.
	 * @param string $name Template name (default: '').
	 *
	 * @since 1.0.0
	 */
	public static function get_template_part( $slug, $name = '' ) {
		$cache_key = sanitize_key( implode( '-', array( 'template-part', $slug, $name, HAMMOCK_VERSION ) ) );
		$template  = (string) wp_cache_get( $cache_key, 'hammock' );

		if ( ! $template ) {
			if ( $name ) {
				$template = locate_template(
					array(
						"{$slug}-{$name}.php",
						self::template_path() . "{$slug}-{$name}.php",
					)
				);

				if ( ! $template ) {
					$fallback = HAMMOCK_TEMPLATE_DIR . "/{$slug}-{$name}.php";
					$template = file_exists( $fallback ) ? $fallback : '';
				}
			}

			if ( ! $template ) {
				// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/hammock/slug.php.
				$template = locate_template(
					array(
						"{$slug}.php",
						self::template_path() . "{$slug}.php",
					)
				);
			}

			wp_cache_set( $cache_key, $template, 'hammock' );
		}

		// Allow 3rd party plugins to filter template file from their plugin.
		$template = apply_filters( 'hammock_get_template_part', $template, $slug, $name );

		if ( $template ) {
			load_template( $template, false );
		}
	}

	/**
	 * Get other templates (e.g. product attributes) passing attributes and including the file.
	 *
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments. (default: array).
	 * @param string $template_path Template path. (default: '').
	 * @param string $default_path  Default path. (default: '').
	 */
	public static function get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		$cache_key = sanitize_key( implode( '-', array( 'template', $template_name, $template_path, $default_path, HAMMOCK_VERSION ) ) );
		$template  = (string) wp_cache_get( $cache_key, 'hammock' );

		if ( ! $template ) {
			$template = self::locate_template( $template_name, $template_path, $default_path );
			wp_cache_set( $cache_key, $template, 'hammock' );
		}

		// Allow 3rd party plugin filter template file from their plugin.
		$filter_template = apply_filters( 'hammock_get_template', $template, $template_name, $args, $template_path, $default_path );

		if ( $filter_template !== $template ) {
			if ( ! file_exists( $filter_template ) ) {
				_doing_it_wrong( __METHOD__, sprintf( __( '%s does not exist.', 'hammock' ), '<code>' . $template . '</code>' ), '2.1' );
				return;
			}
			$template = $filter_template;
		}

		$action_args = array(
			'template_name' => $template_name,
			'template_path' => $template_path,
			'located'       => $template,
			'args'          => $args,
		);

		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args );
		}

		do_action( 'hammock_before_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );

		include $action_args['located'];

		do_action( 'hammock_after_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );
	}

	/**
	 * Like wc_get_template, but returns the HTML instead of outputting.
	 *
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments. (default: array).
	 * @param string $template_path Template path. (default: '').
	 * @param string $default_path  Default path. (default: '').
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		ob_start();
		self::get_template( $template_name, $args, $template_path, $default_path );
		return ob_get_clean();
	}

	/**
	 * Locate a template and return the path for inclusion.
	 *
	 * This is the load order:
	 *
	 * yourtheme/$template_path/$template_name
	 * yourtheme/$template_name
	 * $default_path/$template_name
	 *
	 * @param string $template_name Template name.
	 * @param string $template_path Template path. (default: '').
	 * @param string $default_path  Default path. (default: '').
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function locate_template( $template_name, $template_path = '', $default_path = '' ) {
		if ( ! $template_path ) {
			$template_path = self::template_path();
		}

		if ( ! $default_path ) {
			$default_path = HAMMOCK_TEMPLATE_DIR . '/';
		}

		// Look within passed path within the theme - this is priority.
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name,
			)
		);

		// Get default template/.
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}

		// Return what we found.
		return apply_filters( 'hammock_locate_template', $template, $template_name, $template_path );
	}
}

