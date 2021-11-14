<?php
namespace Hammock\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Helper\Template;

/**
 * Email base class
 *
 * @since 1.0.0
 */
class Email extends Component {

	/**
	 * The email id
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $id = '';

	/**
	 * If the email is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected $enabled;

	/**
	 * Default heading.
	 *
	 * Supported for backwards compatibility but we recommend overloading the
	 * get_default_x methods instead so localization can be done when needed.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $heading = '';

	/**
	 * Default subject.
	 *
	 * Supported for backwards compatibility but we recommend overloading the
	 * get_default_x methods instead so localization can be done when needed.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $subject = '';

	/**
	 * HTML template path.
	 *
	 * @var string
	 */
	public $template_html;

	/**
	 * Recipients for the email.
	 *
	 * @var string
	 */
	public $recipient = '';

	/**
	 * Object this email is for, for example a customer, membership, or email.
	 *
	 * @var object|bool
	 */
	public $object;

	/**
	 * The config object
	 * This holds the email object
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private $config = null;

	/**
	 * The email settings
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $settings = '';

	/**
	 * Strings to find/replace in subjects/headings.
	 *
	 * @var array
	 */
	protected $placeholders = array();

	/**
	 * Strings to find in subjects/headings.
	 *
	 * @var array
	 */
	public $find = array();

	/**
	 * Strings to replace in subjects/headings.
	 *
	 * @var array
	 */
	public $replace = array();

	/**
	 * Sending status
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	public $sending = false;

	/**
	 * If the email type is for admin only
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected $is_admin = false;

	/**
	 * Email Constuctor
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		$this->config = new \Hammock\Model\Email();
		$this->init();

		$this->settings = $this->config->get_setting( $this->id );

		// Find/replace.
		$this->placeholders = array_merge(
			array(
				'{site_title}'   => $this->get_blogname(),
				'{site_address}' => wp_parse_url( home_url(), PHP_URL_HOST ),
			),
			$this->placeholders
		);

		$this->enabled   = $this->get_setting( 'enabled', false );
		$this->heading   = $this->get_setting( 'heading' );
		$this->subject   = $this->get_setting( 'subject' );
		$this->recipient = $this->get_setting( 'recipient' );

		$this->add_action( 'hammock_email_copy_theme_' . $this->id, 'copy_theme' );
		$this->add_action( 'hammock_email_delete_theme_' . $this->id, 'delete_theme' );

		$this->add_filter( 'hammock_get_email_senders', 'register' );
		$this->add_filter( 'hammock_email_sender_' . $this->id . '_get_setting_form', 'setting_form' );
		$this->add_action( 'hammock_email_sender_' . $this->id . '_enabled_sender', 'enable_sender' );
		$this->add_action( 'hammock_email_sender_' . $this->id . '_update_settings', 'update_setting' );
		$this->add_action( 'hammock_send_email_' . $this->id, 'send_email', 10, 5 );
	}

	/**
	 * Initialise the email
	 * Called in the __construct method
	 *
	 * @since 1.0.0
	 */
	public function init() {

	}


	/**
	 * Get email types
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function email_types() {
		$types = \Hammock\Services\Emails::email_types();
		return $types;
	}


	/**
	 * Register email senders
	 * Register a key value pair of email
	 *
	 * @param array $senders - the current list of senders
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function register( $senders = array(
		'admin'  => array(),
		'member' => array(),
	) ) {
		if ( $this->is_admin ) {
			if ( ! isset( $senders['admin'][ $this->id ] ) ) {
				$senders['admin'][ $this->id ] = array(
					'id'            => $this->id,
					'settings'      => $this->get_parameters(),
					'place_holders' => array_keys( $this->placeholders ),
				);
			}
		} else {
			if ( ! isset( $senders['member'][ $this->id ] ) ) {
				$senders['member'][ $this->id ] = array(
					'id'            => $this->id,
					'settings'      => $this->get_parameters(),
					'place_holders' => array_keys( $this->placeholders ),
				);
			}
		}
		return $senders;
	}

	/**
	 * Copy theme
	 * This copies a template file to the theme
	 *
	 * @since 1.0.0
	 *
	 * @return application/json
	 */
	public function copy_theme() {
		if ( ! current_user_can( 'edit_themes' ) ) {
			wp_send_json_error( __( "You don't have permission to do this.", 'hammock' ) );
		}
		$success = Template::copy_to_theme( $this->template_html );
		if ( $success ) {
			wp_send_json_success( __( 'Template file copied to theme', 'hammock' ) );
		}
	}

	/**
	 * Delete theme
	 * This deletes the template file from the theme
	 *
	 * @since 1.0.0
	 *
	 * @return application/json
	 */
	public function delete_theme() {
		if ( ! current_user_can( 'edit_themes' ) ) {
			wp_send_json_error( __( "You don't have permission to do this.", 'hammock' ) );
		}
		$success = Template::remove_template( $this->template_html );
		if ( $success ) {
			wp_send_json_success( __( 'Template file deleted from theme', 'hammock' ) );
		}
	}

	/**
	 * Get the settings form
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function setting_form( $form ) {
		$view       = new \Hammock\View\Backend\Email\Setting();
		$params     = $this->get_parameters();
		$view->data = array(
			'params'        => $params,
			'template'      => $this->template_html,
			'is_admin'      => $this->is_admin,
			'id'            => $this->id,
			'place_holders' => array_keys( $this->placeholders ),
		);
		return array(
			'id'    => $this->id,
			'title' => $params['title'],
			'form'  => $view->render( true ),
		);
	}

	/**
	 * Enable the sender
	 *
	 * @param bool $enabled - set to true or false to enable the sender
	 *
	 * @since 1.0.0
	 */
	public function enable_sender( $enabled ) {
		$settings = $this->config;
		$setting  = $this->get_settings();

		$setting['enabled'] = $enabled;

		$settings->set_setting( $this->id, $setting );
		$settings->save();
		$this->settings = $setting;

		$this->enabled = $setting['enabled'];
	}

	/**
	 * Update email provider settings
	 *
	 * @param array $response
	 * @param array $data - the post data
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function update_setting( $data ) {
		$settings = $this->config;
		$setting  = $this->get_settings();

		$setting['enabled'] = isset( $data['enabled'] );
		$setting['subject'] = sanitize_text_field( $data['subject'] );
		$setting['heading'] = sanitize_text_field( $data['heading'] );
		if ( $this->is_admin ) {
			$setting['recipient'] = sanitize_email( $data['recipient'] );
			$this->recipient      = $setting['recipient'];
		}
		$settings->set_setting( $this->id, $setting );
		$settings->save();
		$this->settings = $setting;

		$this->enabled = $setting['enabled'];
		$this->heading = $setting['heading'];
		$this->subject = $setting['subject'];
	}

	/**
	 * Get settings
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_settings() {
		return $this->settings;
	}

	/**
	 * Get setting
	 *
	 * @param string $key - the setting key
	 * @param object $default - the optional default value
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_setting( $key, $default = '' ) {
		$settings = $this->get_settings();
		if ( isset( $settings[ $key ] ) ) {
			return $settings[ $key ];
		}
		return $default;
	}

	/**
	 * Get WordPress blog name.
	 *
	 * @return string
	 */
	public function get_blogname() {
		return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}

	/**
	 * Register defaults
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function register_defaults() {
		return array(
			'title'       => '',
			'description' => '',
			'heading'     => '',
			'subject'     => '',
			'recipient'   => '',
			'enabled'     => false,
		);
	}

	/**
	 * Get the parameters
	 * This checks the default and saved parameters and returns the settings
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_parameters() {
		$defaults = $this->register_defaults();
		if ( ! empty( $this->heading ) ) {
			$params = array(
				'heading'   => $this->heading,
				'subject'   => $this->subject,
				'enabled'   => $this->enabled,
				'recipient' => $this->recipient,
			);

			return array_merge( $defaults, $params );
		} else {
			return $defaults;
		}
	}

	/**
	 * Email from address
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function email_from() {
		$admin_email = get_option( 'admin_email' );
		return apply_filters( 'hammock_email_from_email', $admin_email, $this->id );
	}

	/**
	 * Email from name
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function email_from_name() {
		return apply_filters( 'hammock_email_from_name', $this->get_blogname(), $this->id );
	}

	/**
	 * Format email string.
	 *
	 * @param array  $placeholders - the placeholders. This is a key value representation
	 * @param string $string - Text to replace placeholders in.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function format_string( $placeholders, $string ) {

		$find    = array_keys( $placeholders );
		$replace = array_values( $placeholders );

		$find[]    = '{blogname}';
		$replace[] = $this->get_blogname();

		$string = str_replace( $find, $replace, $string );

		return apply_filters( 'hammock_email_format_string', $string, $this->id );
	}

	/**
	 * Default headers
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function default_headers() {
		return apply_filters(
			'hammock_email_default_headers',
			array(
				'From: ' . $this->email_from() . ' <' . $this->email_from_name() . '>',
				'Content-Type: text/html; charset=UTF-8',
			),
			$this->id
		);
	}

	/**
	 * Default multipart headers
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function default_multipart_headers() {
		return apply_filters(
			'hammock_email_default_multipart_headers',
			array(
				'From: ' . $this->email_from() . ' <' . $this->email_from_name() . '>',
				'Content-Type: multipart/alternative; charset=UTF-8',
			),
			$this->id
		);
	}
	/**
	 * Get Content
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_content() {
		return Template::get_template_html(
			$this->template_html,
			array(
				'object'      => $this->object,
				'heading'     => $this->heading,
				'description' => $this->description,
				'title'       => $this->title,
				'blog_name'   => $this->get_blogname(),
				'email'       => $this,
			)
		);
	}

	/**
	 * Send email
	 *
	 * @param array  $placeholders - the placeholders
	 * @param string $to - recipient of email.
	 * @param array  $attachments - Email attachments.
	 * @param array  $cc - Email copy
	 * @param array  $headers - Email headers.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function send( $placeholders, $to = '', $attachments = array(), $cc = array(), $headers = array() ) {
		$params = $this->get_parameters();
		if ( ! $params['enabled'] ) {
			return false;
		}

		if ( ! is_array( $to ) && empty( $to ) ) {
			$to = $this->recipient;
		}

		if ( ! is_array( $to ) && ! filter_var( $to, FILTER_VALIDATE_EMAIL ) ) {
			return false;
		}

		$content = $this->get_content();
		if ( empty( $content ) ) {
			return false;
		}

		$headers = array_merge( $this->default_headers(), $headers );

		if ( ! empty( $attachments ) ) {
			$headers = array_merge( $this->default_multipart_headers(), $headers );
		}

		if ( ! empty( $this->cc ) ) {
			foreach ( $this->cc as $cc ) {
				$headers[] = 'Cc: ' . $cc;
			}
		}

		$subject = $this->format_string( $placeholders, $params['subject'] );
		$content = $this->format_string( $placeholders, $content );
		$content = stripslashes( $content );
		$content = wpautop( $content );

		$sent = wp_mail( $to, $subject, $content, $headers, $attachments );

		return $sent;
	}

	/**
	 * Action to send email
	 *
	 * @param array        $placeholders - the placeholders
	 * @param object       $object
	 * @param string|array $to
	 * @param array        $attachments
	 * @param array        $cc - Email copy
	 *
	 * @since 1.0.0
	 */
	function send_email( $placeholders, $object, $to, $attachments = array(), $cc = array() ) {
		$this->object     = $object;
		$new_placeholders = array_merge( $this->placeholders, $placeholders );
		$this->send( $new_placeholders, $to, $attachments, $cc );
	}
}

