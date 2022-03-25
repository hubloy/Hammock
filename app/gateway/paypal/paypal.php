<?php
namespace Hammock\Gateway\Paypal;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Base\Gateway;
use Hammock\Helper\Duration;

class PayPal extends Gateway {

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
	 * Gateway init
	 * Used to load any required classes
	 */
	public function init() {
		$this->id = 'paypal';
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
		if ( ! isset( $gateways['paypal'] ) ) {
			$gateways['paypal'] = array(
				'name' => __( 'PayPal Standard Gateway', 'hammock' ),
				'logo' => HAMMOCK_ASSETS_URL . '/img/gateways/paypal.png',
			);
		}
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
	 * Gateway settings
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function settings( $data = '' ) {
		$view       = new \Hammock\View\Backend\Gateways\PayPal();
		$settings   = $this->settings->get_gateway_setting( $this->id );
		$view->data = array(
			'settings' => $settings,
		);
		return $view->render( true );
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
		$settings                     		 = $this->settings->get_gateway_setting( $this->id );
		$settings['enabled']          		 = isset( $data[ $this->id ] ) ? true : false;
		$settings['mode']             		 = sanitize_text_field( $data['paypal_mode'] );
		$settings['paypal_email']  			 = sanitize_text_field( $data['paypal_email'] );
		$settings['paypal_merchant_id']  	 = sanitize_text_field( $data['paypal_merchant_id'] );
		$settings['test_paypal_email']  	 = sanitize_text_field( $data['test_paypal_email'] );
		$settings['test_paypal_merchant_id'] = sanitize_text_field( $data['test_paypal_merchant_id'] );
		$this->settings->set_gateway_setting( $this->id, $settings );
		$this->settings->save();
		return $settings;
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
		return in_array(
			$currency,
			array(
				'AUD', 'BRL', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MYR', 'MXN', 'NOK',
				'NZD', 'PHP', 'PLN', 'GBP', 'RUB', 'SGD', 'SEK', 'CHF', 'TWD', 'THB', 'TRY', 'USD',
			)
		);
	}

	/**
	 * Get the credentials
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	public function get_credentials() {
		$settings = $this->settings->get_gateway_setting( $this->id );
		if ( 'test' === $settings['mode'] ) {
			return array(
				'url' 			=> 'https://www.sandbox.paypal.com/cgi-bin/webscr?test_ipn=1&',
				'email' 		=> $settings['test_paypal_email'],
				'merchant_id' 	=> $settings['test_paypal_merchant_id']
			);
		} else {
			return array(
				'url' 			=> 'https://www.paypal.com/cgi-bin/webscr?',
				'email' 		=> $settings['paypal_email'],
				'merchant_id' 	=> $settings['paypal_merchant_id']
			);
		}
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
		$credentials = $this->get_credentials();
		$plan        = $invoice->get_plan();
		$membership  = $plan->get_memebership();
		if ( $membership->is_recurring() ) {
			return $this->process_recurring( $invoice, $plan, $membership, $credentials );
		}
		$user   = $invoice->get_user_details();
		$paypal = array(
			'cmd'           => '_cart',
			'upload'        => '1',
			'business'      => $credentials['email'],
			'email'         => $user['email'],
			'first_name'    => $user['fname'],
			'last_name'     => $user['lname'],
			'invoice'       => $invoice->invoice_id,
			'no_shipping'   => '1',
			'shipping'      => '0',
			'currency_code' => hammock_get_currency_symbol(),
			'charset'       => 'utf-8',
			'no_note'       => '1',
			'custom'        => json_encode( array( 'payment_id' => $invoice->invoice_id, 'plan_id' => $plan->id ) ),
			'rm'            => '2',
			'return'        => add_query_arg( 'gateway', $this->get_id(), $this->get_invoice_page( $invoice ) ),
			'cancel_return' => $this->get_cancel_page( $invoice ),
			'notify_url'    => $this->get_listener_url(),
			'cbt'           => get_bloginfo( 'name' ),
			'item_name_1'   => $membership->name,
			'quantity_1'    => '1',
			'amount_1'      => number_format( $invoice->amount, 2, '.', '' )
		);

		$url = $credentials['url'];
		$url .= http_build_query( array_filter( $paypal ), '', '&' );
		$url = str_replace( '&amp;', '&', $url );

		return array( 'result' => 'success', 'redirect' => $url );
	}

	/**
	 * Process recurring payment
	 * 
	 * @param \Hammock\Model\Invoice $invoice The current invoice.
	 * @param \Hammock\Model\Plan $plan The current plan.
	 * @param \Hammock\Model\Membership $membership The plan membership.
	 * @param array $credentials The gateway credentials
	 * 
	 * @since 1.0.0
	 */
	private function process_recurring( $invoice, $plan, $membership, $credentials ) {
		$total = $payment->amount;
		list( $t_n, $p_n ) = $this->get_subscription_period_vars( $membership->duration );

		$paypal = array(
			'business'      => $credentials['email'],
			'email'         => $user['email'],
			'first_name'    => $user['fname'],
			'last_name'     => $user['lname'],
			'invoice'       => $invoice->invoice_id,
			'no_shipping'   => '1',
			'shipping'      => '0',
			'no_note'       => '1',
			'currency_code' => hammock_get_currency_symbol(),
			'charset'       => 'utf-8',
			'custom'        => json_encode( array( 'payment_id' => $invoice->invoice_id, 'plan_id' => $plan->id ) ),
			'rm'            => '2',
			'return'        => add_query_arg( 'gateway', $this->get_id(), $this->get_invoice_page( $invoice ) ),
			'cancel_return' => $this->get_cancel_page( $invoice ),
			'notify_url'    => $this->get_listener_url(),
			'cbt'           => get_bloginfo( 'name' ),
			'sra'           => '1',
			'src'           => '1',
			'cmd'           => '_xclick-subscriptions',
			'item_name'     => $membership->name
		);

		// Regular subscription price and interval.
		$paypal = array_merge( $paypal, array(
			'a3' => $invoice->amount,
			'p3' => $p_n,
			't3' => $t_n
		) );

		// If there was a discount, apply it as a "trial" period.
		if ( $membership->trial_enabled ) {
			list( $t_n, $p_n ) = $this->get_subscription_period_vars( $membership->trial_duration, $membership->trial_period );
			$paypal = array_merge( $paypal, array(
				'a1' => $membership->trial_price,
				'p1' => $p_n,
				't1' => $t_n
			) );
		}

		$url = $credentials['url'];
		$url .= http_build_query( $paypal );
		$url = str_replace( '&amp;', '&', $url );

		return array( 'result' => 'success', 'redirect' => $url );
	}

	/**
	 * Get subscription period variables
	 * 
	 * @param string $duration
	 * 
	 * @since 1.0.0
	 * 
	 * @return array
	 */
	private function get_subscription_period_vars( $duration, $period = 1 ) {

		$periods = array(
			'D' => array( 'days' => 1, 'limit' => 90 ),
			'W' => array( 'days' => 7, 'limit' => 52 ),
			'M' => array( 'days' => 30, 'limit' => 24 ),
			'Y' => array( 'days' => 365, 'limit' => 5 )
		);

		$best_match = false;

		$days = Duration::get_period_in_days( $period, $duration );

		foreach ( $periods as $period => $_ ) {
			$days_in_period = $_['days'];

			$r = $days % $days_in_period;
			$d = round( $days / $days_in_period, 0 );

			if ( $d > $_['limit'] ) {
				continue;
			}

			if ( 0 == $r ) {
				$best_match = array( $period, $d );
				break;
			}

			if ( ! $best_match ) {
				$best_match = array( $period, $d );
			} else {
				$d1 = $periods[ $best_match[0] ]['days'] * $best_match[1];
				$d2 = $d * $days_in_period;

				if ( abs( $days - $d1 ) > abs( $days -$d2 ) ) {
					$best_match = array( $period, $d );
				}
			}
		}

		if ( ! $best_match ) {
			wp_die( __( 'Can not create a valid PayPal subscription configuration from plan.', 'hammock' ) );
		}

		return $best_match;
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

