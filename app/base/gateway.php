<?php
namespace Hammock\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Hammock\Model\Settings;
use Hammock\Services\Transactions;
use Hammock\Helper\Logger;

/**
 * Base Gateway
 */
class Gateway extends Component {

	/**
	 * The gateway id
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $id = '';

	/**
	 * Setting object
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	protected $settings = null;

	/**
	 * Main plugin logger
	 *
	 * @since 1.0.0
	 *
	 * @var \Hammock\Helper\Logger
	 */
	public $logger = null;

	/**
	 * What type of transactions are supported
	 * This tells the frontend wht to show depending on the plan purchased
	 * Ovveride this in the child class
	 *
	 * recurring - this is for recurring payments
	 * single - single payments, non-recurring
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $supports = array(
		'recurring',
		'single',
	);

	/**
	 * Gateway Constuctor
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		$this->settings = new Settings();
		$this->logger   = new Logger();
		$this->init();
		$this->add_filter( 'hammock_register_gateways', 'register' );
		$this->add_action( 'hammock_init_gateway', 'init_gateway' );
		$this->add_filter( 'hammock_gateway_' . $this->id . '_is_active', 'is_active' );
		$this->add_filter( 'hammock_gateway_' . $this->id . '_settings', 'settings' );
		$this->add_action( 'hammock_gateway_' . $this->id . '_update_settings', 'update_settings', 10, 2 );

		if ( $this->is_active() ) {

			// Register front end scripts and styles.
			$this->add_action( 'wp_enqueue_scripts', 'register_scripts' );

			// Memberships. Need to sync the plan to the gateway
			$this->add_action( 'hammock_memberships_plan_created', 'membership_created_sync' );
			$this->add_action( 'hammock_memberships_updated', 'membership_updated_sync' );

			// Subscription actions
			$this->add_action( 'hammock_gateway_' . $this->id . '_ipn_notify', 'ipn_notify' );
			$this->add_action( 'hammock_gateway_' . $this->id . '_render_payment_form', 'render_payment_form' );
			$this->add_action( 'hammock_gateway_' . $this->id . '_render_payment_update_form', 'render_payment_update_form' );
			$this->add_action( 'hammock_gateway_' . $this->id . '_process_payment', 'process_payment' );
			$this->add_action( 'hammock_gateway_' . $this->id . '_process_refund', 'process_refund', 10, 3 );
			$this->add_action( 'hammock_gateway_' . $this->id . '_process_cancel', 'process_cancel' );
			$this->add_action( 'hammock_gateway_' . $this->id . '_process_pause', 'process_pause' );
			$this->add_action( 'hammock_gateway_' . $this->id . '_process_resume', 'process_resume' );
			$this->add_action( 'hammock_gateway_' . $this->id . '_handle_return', 'handle_return', 10, 2 );

			// Member delete
			$this->add_action( 'hammock_member_before_delete_member', 'handle_member_delete' );
		}
	}

	/**
	 * Initialise the gateway
	 * Called in the __construct method
	 *
	 * @since 1.0.0
	 */
	public function init() {

	}

	/**
	 * Get the gateway id
	 * 
	 * @since 1.0.0
	 * 
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}


	/**
	 * Register gateway
	 * Register a key value pair of gateways
	 *
	 * @param array $gateways - the current list of gateways
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function register( $gateways ) {

		return $gateways;
	}

	/**
	 * Init gateway
	 * Initialize the gateway
	 *
	 * @since 1.0.0
	 */
	public function init_gateway() {

	}


	/**
	 * Checks if a gateway is active
	 * This loads the setting and checks if its active
	 *
	 * @param bool $is_active - active status
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_active( $is_active = false ) {
		$settings = $this->settings->get_gateway_setting( $this->id );
		return $settings['enabled'];
	}

	/**
	 * Check if currency is supported
	 * 
	 * @param string $currency The current site currency code.
	 * 
	 * @since 1.0.0
	 * 
	 * @return bool
	 */
	public function is_currency_supported( $currency ) {
		return true;
	}


	/**
	 * Gateway settings
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function settings( $data = '' ) {
		return $data;
	}

	/**
	 * Update gateway settings
	 *
	 * @param array $response
	 * @param array $data - the post data
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function update_settings( $response = array(), $data ) {
		$settings = new Settings();
		$response = $settings->get_gateway_setting( $this->id );
		return $response;
	}

	/**
	 * Get the site currency symbol
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_currency() {
		$general  = $this->settings->get_general_settings();
		$currency = $general['currency'];
		return apply_filters( 'hammock_gateway_currency_' . $this->id, $currency );
	}

	/**
	 * Register script used for the gateway
	 *
	 * @since 1.0.0
	 */
	public function register_scripts() {

	}

	/**
	 * Get the invoice page url
	 * 
	 * @param \Hammock\Model\Invoice $invoice The current invoice
	 * 
	 * @since 1.0.0
	 * 
	 * @return string
	 */
	public function get_invoice_page( $invoice ) {
		return esc_url( hammock_get_account_page_links( 'view-transaction', $invoice->invoice_id ) );
	}

	/**
	 * Get the invoice cancel url
	 * 
	 * @param \Hammock\Model\Invoice $invoice The current invoice
	 * 
	 * @since 1.0.0
	 * 
	 * @return string
	 */
	public function get_cancel_page( $invoice ) {
		return esc_url( add_query_arg( 'cancel', 'cenceled', hammock_get_account_page_links( 'view-transaction', $invoice->invoice_id ) ) );
	}

	/**
	 * Get the payment return
	 * 
	 * @since 1.0.0
	 * 
	 * @return string
	 */
	public function get_return_url() {
		return esc_url( add_query_arg( array(
			'hm-api'  => 'handle_return',
			'gateway' => $this->get_id()
		), home_url( 'index.php' ) ) );
	}

	/**
	 * Get the IPN listener URL
	 * 
	 * @since 1.0.0
	 * 
	 * @return string
	 */
	public function get_listener_url() {
		return esc_url( add_query_arg( array(
			'hm-api'  => 'ipn_notify',
			'gateway' => $this->get_id()
		), home_url( 'index.php' ) ) );
	}

	/**
	 * Action called when a membership is created.
	 * This is mainly to sync to the payment gateway
	 * Most gateways require
	 *
	 * @param int $membership_id - the membership id
	 *
	 * @since 1.0.0
	 */
	public function membership_created_sync( $membership_id ) {

	}

	/**
	 * Action called when a membership is updated.
	 * This is mainly to sync to the payment gateway
	 * The plan will need to be updated on the gateway side
	 *
	 * @param int $membership_id - the membership id
	 *
	 * @since 1.0.0
	 */
	public function membership_updated_sync( $membership_id ) {

	}


	/**
	 * Handle the ipn callbacks
	 *
	 * @since 1.0.0
	 */
	public function ipn_notify() {

	}

	/**
	 * Render the payment form
	 *
	 * @param \Hammock\Model\Invoice $invoice - the invoice model
	 *
	 * @return string
	 */
	public function render_payment_form( $invoice ) {
		return '';
	}

	/**
	 * Render the subscription payment update form
	 *
	 * @param \Hammock\Model\Plan $plan - the plan model
	 *
	 * @return string
	 */
	public function render_payment_update_form( $plan ) {
		return '';
	}

	/**
	 * Process Payment
	 *
	 * @param \Hammock\Model\Invoice $invoice - the invoice model
	 *
	 * @since 1.0.0
	 */
	public function process_payment( $invoice ) {
		$invoice->status = Transactions::STATUS_PAID;
		$invoice->save();
		return array( 'result' => 'success', 'redirect' => $this->get_invoice_page( $invoice ) );
	}

	/**
	 * Process Refund
	 *
	 * @param \Hammock\Model\Invoice $invoice - the invoice model
	 * @param \Hammock\Model\Plan    $plan - the plan model
	 * @param double                 $amount - the amount
	 *
	 * @since 1.0.0
	 */
	public function process_refund( $invoice, $plan, $amount ) {

	}


	/**
	 * Process Cancel
	 * Called when a plan is cancelled
	 *
	 * @param \Hammock\Model\Plan $plan - the plan model
	 *
	 * @since 1.0.0
	 */
	public function process_cancel( $plan ) {

	}

	/**
	 * Process Pause
	 * Called when a plan is paused
	 *
	 * @param \Hammock\Model\Plan $plan - the plan model
	 *
	 * @since 1.0.0
	 */
	public function process_pause( $plan ) {

	}

	/**
	 * Process Resume
	 * Called when a plan is resumed
	 *
	 * @param \Hammock\Model\Plan $plan - the plan model
	 *
	 * @since 1.0.0
	 */
	public function process_resume( $plan ) {

	}

	/**
	 * Handle payment return
	 * This is called after a payment gateway redirects
	 *
	 * @param \Hammock\Model\Invoice $invoice - the invoice model
	 *
	 * @since 1.0.0
	 */
	public function handle_return( $invoice ) {

	}

	/**
	 * Handle member delete
	 *
	 * @param object $member - the current member
	 *
	 * @since 1.0.0
	 */
	public function handle_member_delete( $member ) {

	}
}

