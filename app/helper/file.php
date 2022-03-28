<?php
namespace Hammock\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * File helper
 *
 * @since 1.0.0
 */
class File {

	/**
	 * The directory permission
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $chmod_dir = 0755;

	/**
	 * The file permission
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $chmod_file = 0644;

	/**
	 * Check if current action can be performed
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	private $has_permission = false;

	public function __construct() {
		$this->chmod_dir  = defined( 'FS_CHMOD_DIR' ) ? FS_CHMOD_DIR : ( fileperms( ABSPATH ) & 0777 | 0755 );
		$this->chmod_file = defined( 'FS_CHMOD_FILE' ) ? FS_CHMOD_FILE : ( fileperms( ABSPATH . 'index.php' ) & 0777 | 0644 );

		$this->check_permission();
	}

	/**
	 * Create a file
	 *
	 * @param string $file The full file path.
	 * @param string $file_content The file contents.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function create_file( $file, $file_content ) {
		if ( $this->has_permission ) {
			global $wp_filesystem;
			return $wp_filesystem->put_contents( $file, $file_content, $this->chmod_file );
		}
		return false;
	}

	/**
	 * Read a file
	 *
	 * @param string $file The file
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function read_file( $file ) {
		if ( $this->has_permission ) {
			global $wp_filesystem;
			return $wp_filesystem->get_contents( $file );
		}
		return '';
	}

	/**
	 * Create directory
	 *
	 * @param string $directory The full directory path.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function create_directory( $directory ) {
		if ( $this->has_permission ) {
			global $wp_filesystem;
			if ( ! $wp_filesystem->exists( $directory ) ) {
				$wp_filesystem->mkdir( $directory, $this->chmod_dir );
			}
			$index_php_path  = trailingslashit( $directory ) . 'index.php';
			$index_html_path = trailingslashit( $directory ) . 'index.html';
			$htaccess_file   = trailingslashit( $directory ) . '.htaccess';
			$dirs_exist      = $wp_filesystem->is_dir( $directory );
			if ( $dirs_exist ) {
				$wp_filesystem->put_contents( $index_php_path, "<?php\n// Silence is golden.\n?>", $this->chmod_file );
				$wp_filesystem->put_contents( $index_html_path, '', $this->chmod_file );
				$wp_filesystem->put_contents( $htaccess_file, 'deny from all', $this->chmod_file );
			}
		}
		return false;
	}

	/**
	 * Check if a file or directory exists.
	 *
	 * @param string $file_or_dir The full file or directory path
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function exists( $file_or_dir ) {
		if ( $this->has_permission ) {
			global $wp_filesystem;
			return $wp_filesystem->exists( $file_or_dir );
		}
		return false;
	}

	/**
	 * Check if a directory is writable.
	 *
	 * @param string $directory The full directory path
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_writable( $directory ) {
		if ( $this->has_permission ) {
			global $wp_filesystem;
			return $wp_filesystem->is_writable( $directory );
		}
		return false;
	}

	/**
	 * Check permissions.
	 * This checks that the file system is ready to be written
	 *
	 * @since 1.0.0
	 */
	private function check_permission() {
		$creds                = $this->get_creds();
		$this->has_permission = true;
		if ( empty( $creds ) || ! WP_Filesystem( $creds ) ) {
			$this->has_permission = false;
		}
	}


	/**
	 * Get File access credentials
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_creds() {
		if ( ! function_exists( 'get_filesystem_method' ) ) {
			include_once ABSPATH . 'wp-admin/includes/file.php';
		}

		$access_type = get_filesystem_method();
		if ( $access_type === 'direct' ) {
			$creds = request_filesystem_credentials( site_url() . '/wp-admin/', '', false, false, array() );
		} else {
			$creds = $this->get_ftp_creds( $access_type );
		}

		return $creds;
	}

	/**
	 * Check for FTP credentials
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	private function get_ftp_creds( $type ) {
		$credentials = get_option(
			'ftp_credentials',
			array(
				'hostname' => '',
				'username' => '',
			)
		);

		$credentials['hostname'] = defined( 'FTP_HOST' ) ? FTP_HOST : $credentials['hostname'];
		$credentials['username'] = defined( 'FTP_USER' ) ? FTP_USER : $credentials['username'];
		$credentials['password'] = defined( 'FTP_PASS' ) ? FTP_PASS : '';

		// Check to see if we are setting the public/private keys for ssh
		$credentials['public_key']  = defined( 'FTP_PUBKEY' ) ? FTP_PUBKEY : '';
		$credentials['private_key'] = defined( 'FTP_PRIKEY' ) ? FTP_PRIKEY : '';

		// Sanitize the hostname, Some people might pass in odd-data:
		$credentials['hostname'] = preg_replace( '|\w+://|', '', $credentials['hostname'] ); // Strip any schemes off

		if ( strpos( $credentials['hostname'], ':' ) ) {
			list( $credentials['hostname'], $credentials['port'] ) = explode( ':', $credentials['hostname'], 2 );
			if ( ! is_numeric( $credentials['port'] ) ) {
				unset( $credentials['port'] );
			}
		} else {
			unset( $credentials['port'] );
		}

		if ( ( defined( 'FTP_SSH' ) && FTP_SSH ) || ( defined( 'FS_METHOD' ) && 'ssh2' == FS_METHOD ) ) {
			$credentials['connection_type'] = 'ssh';
		} elseif ( ( defined( 'FTP_SSL' ) && FTP_SSL ) && 'ftpext' == $type ) {
			// Only the FTP Extension understands SSL
			$credentials['connection_type'] = 'ftps';
		} elseif ( ! isset( $credentials['connection_type'] ) ) {
			// All else fails (And it's not defaulted to something else saved), Default to FTP
			$credentials['connection_type'] = 'ftp';
		}

		$has_creds = ( ! empty( $credentials['password'] ) && ! empty( $credentials['username'] ) && ! empty( $credentials['hostname'] ) );
		$can_ssh   = ( 'ssh' == $credentials['connection_type'] && ! empty( $credentials['public_key'] ) && ! empty( $credentials['private_key'] ) );
		if ( $has_creds || $can_ssh ) {
			$stored_credentials = $credentials;
			if ( ! empty( $stored_credentials['port'] ) ) {
				// save port as part of hostname to simplify above code.
				$stored_credentials['hostname'] .= ':' . $stored_credentials['port'];
			}

			unset( $stored_credentials['password'], $stored_credentials['port'], $stored_credentials['private_key'], $stored_credentials['public_key'] );

			return $credentials;
		}

		return false;
	}
}
