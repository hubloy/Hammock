<?php
namespace HubloyMembership\Gateway\Paypal;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use HubloyMembership\Base\Gateway;
use HubloyMembership\Helper\Duration;
use HubloyMembership\Model\Invoice;
use HubloyMembership\Service\Transactions;

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
				'name' => __( 'PayPal Standard Gateway', 'hubloy-membership' ),
				'logo' => HUBMEMB_ASSETS_URL . '/img/gateways/paypal.png',
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
		$view       = new \HubloyMembership\View\Backend\Gateways\PayPal();
		$settings   = $this->settings->get_gateway_setting( $this->get_id() );
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
		$settings                            = $this->settings->get_gateway_setting( $this->get_id() );
		$settings['enabled']                 = isset( $data[ $this->id ] ) ? true : false;
		$settings['mode']                    = sanitize_text_field( $data['paypal_mode'] );
		$settings['paypal_email']            = sanitize_text_field( $data['paypal_email'] );
		$settings['paypal_merchant_id']      = sanitize_text_field( $data['paypal_merchant_id'] );
		$settings['test_paypal_email']       = sanitize_text_field( $data['test_paypal_email'] );
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
				'AUD',
				'BRL',
				'CAD',
				'CZK',
				'DKK',
				'EUR',
				'HKD',
				'HUF',
				'ILS',
				'JPY',
				'MYR',
				'MXN',
				'NOK',
				'NZD',
				'PHP',
				'PLN',
				'GBP',
				'RUB',
				'SGD',
				'SEK',
				'CHF',
				'TWD',
				'THB',
				'TRY',
				'USD',
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
		$settings = $this->settings->get_gateway_setting( $this->get_id() );
		if ( 'test' === $settings['mode'] ) {
			return array(
				'url'         => 'https://www.sandbox.paypal.com/cgi-bin/webscr?test_ipn=1&',
				'email'       => $settings['test_paypal_email'],
				'merchant_id' => $settings['test_paypal_merchant_id'],
			);
		} else {
			return array(
				'url'         => 'https://www.paypal.com/cgi-bin/webscr?',
				'email'       => $settings['paypal_email'],
				'merchant_id' => $settings['paypal_merchant_id'],
			);
		}
	}

	/**
	 * Handle the ipn callbacks
	 *
	 * @since 1.0.0
	 */
	public function ipn_notify() {
		if ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] != 'POST' ) {
			return;
		}

		if ( ! $this->validate_ipn() ) {
			return;
		}

		$invoice_id = ! empty( $_POST['invoice'] ) ? $_POST['invoice'] : 0;
		$invoice    = new Invoice();

		if ( ! $invoice_id && ! empty( $_POST['custom'] ) ) {
			$invoice_id = json_decode( $_POST['custom'] );

			if ( ! empty( $custom->payment_id ) ) {
				$invoice_id = absint( $custom->payment_id );
			}
		}

		if ( ! $invoice_id && ( ! empty( $_POST['txn_id'] ) || ! empty( $_POST['parent_txn_id'] ) ) ) {
			if ( ! empty( $_POST['parent_txn_id'] ) ) {
				$invoice->get_by_gateway_identifier( sanitize_text_field( $_POST['parent_txn_id'] ) );
			} else {
				$invoice->get_by_gateway_identifier( sanitize_text_field( $_POST['txn_id'] ) );
			}
		}

		if ( $invoice_id ) {
			$invoice->get_by_invoice_id( $payment_id );
		}

		if ( ! $invoice->is_valid() ) {
			return;
		}

		if ( ! $invoice->gateway ) {
			$invoice->gateway = 'paypal';
			$invoice->save();
		}

		if ( 'paypal' !== $invoice->gateway ) {
			return;
		}

		if ( ! $invoice->gateway_identifier && ! empty( $_POST['txn_id'] ) ) {
			$invoice->gateway_identifier = sanitize_text_field( $_POST['txn_id'] );
			$invoice->save(); // Save txn id.
		}

		$txn_type = ! empty( $_POST['txn_type'] ) ? sanitize_text_field( $_POST['txn_type'] ) : '';

		if ( method_exists( $this, 'process_postback_' . $txn_type ) ) {
			return call_user_func( array( $this, 'process_postback_' . $txn_type ), $invoice );
		} else {
			return $this->process_postback_default( $invoice );
		}
	}

	/**
	 * Default callback for non-supported actions
	 *
	 * @since 1.0.0
	 */
	private function process_postback_default( $invoice ) {
		$paypal_status = strtolower( sanitize_text_field( $_POST['payment_status'] ) );

		if ( in_array( $paypal_status, array( 'refunded', 'reversed' ) ) ) {
			return $this->process_postback_refund( $invoice );
		}

		if ( Transactions::STATUS_PAID === $invoice->status ) {
			return;
		}

		// Set customer data.
		$payer_data               = array();
		$payer_data['first_name'] = ! empty( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
		$payer_data['last_name']  = ! empty( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
		$payer_data['email']      = ! empty( $_POST['payer_email'] ) ? sanitize_email( $_POST['payer_email'] ) : '';
		$payer_data['address']    = ! empty( $_POST['address_street'] ) ? sanitize_text_field( $_POST['address_street'] ) : '';
		$payer_data['city']       = ! empty( $_POST['city'] ) ? sanitize_text_field( $_POST['city'] ) : '';
		$payer_data['state']      = ! empty( $_POST['address_state'] ) ? sanitize_text_field( $_POST['address_state'] ) : '';
		$payer_data['country']    = ! empty( $_POST['address_country_code'] ) ? sanitize_text_field( $_POST['address_country_code'] ) : '';
		$payer_data['zip']        = ! empty( $_POST['address_street'] ) ? sanitize_text_field( $_POST['address_street'] ) : '';

		$invoice->set_custom_data( 'payer_data', $payer_data );

		if ( 'completed' === $paypal_status ) {
			$invoice->status = Transactions::STATUS_PAID;
		} elseif ( 'pending' === $paypal_status && ! empty( $_POST['pending_reason'] ) ) {
			$invoice->status = Transactions::STATUS_PENDING;
			$invoice->add_note( sprintf( __( 'PayPal has the payment on hold. Reason given: %s', 'hubloy-membership' ), sanitize_text_field( $_POST['pending_reason'] ) ) );
		} else {
			$invoice->status = Transactions::STATUS_FAILED;
			$invoice->add_note( sprintf( __( 'PayPal rejected the payment. PayPal Status: %s', 'hubloy-membership' ), $paypal_status ) );
		}

		$invoice->save();
	}

	/**
	 * Process subscription sign up
	 *
	 * @since 1.0.0
	 */
	private function process_postback_subscr_signup( $invoice ) {
		if ( Transactions::STATUS_PAID === $invoice->status ) {
			return;
		}
		$subscr_id       = sanitize_text_field( $_POST['subscr_id'] );
		$invoice->status = Transactions::STATUS_PAID;
		$invoice->add_note( sprintf( __( 'PayPal Subscription ID: %s', 'hubloy-membership' ), $subscr_id ) );
		$invoice->save();

		$plan = $invoice->get_plan();
		if ( ! $plan ) {
			return;
		}
		// Register subscription.
		$plan->gateway_subscription_id = $subscr_id;
		$plan->record_payment( $invoice );
	}

	/**
	 * Process subscription payment return
	 *
	 * @since 1.0.0
	 */
	private function process_postback_subscr_payment( $invoice ) {
		$plan = $invoice->get_plan();
		if ( ! $plan ) {
			return;
		}
		$membership = $plan->get_membership();
		if ( ! $membership->is_recurring() ) {
			return;
		}

		$date1         = date( 'Y-n-d', strtotime( $invoice->date_created ) );
		$date2         = date( 'Y-n-d', strtotime( $_POST['payment_date'] ) );
		$same_day      = $date1 == $date2;
		$first_payment = $plan->gateway_subscription_id ? false : true;

		if ( $first_payment ) {
			if ( empty( $invoice->gateway_identifier ) ) {
				$invoice->gateway_identifier = sanitize_text_field( $_POST['txn_id'] );
				$invoice->save();
			}

			if ( Transactions::STATUS_PAID !== $invoice->status ) {
				$this->process_postback_subscr_signup( $payment );
				return;
			}
		}

		// Do not process the first payment twice.
		if ( $same_day ) {
			return;
		}

		$invoice->amount = sanitize_text_field( $_POST['mc_gross'] );

		$plan->record_payment( $invoice );
	}

	/**
	 * Process subscription cancel
	 *
	 * @since 1.0.0
	 */
	private function process_postback_subscr_cancel( $invoice ) {
		return $this->process_postback_subscr_eot( $invoice );
	}

	/**
	 * Process subscription cancel at the end of time.
	 *
	 * @since 1.0.0
	 */
	private function process_postback_subscr_eot( $invoice ) {
		$plan = $invoice->get_plan();
		if ( ! $plan ) {
			return;
		}
		// Use direct cancel action to prevent gateway loop.
		$plan->cancel();
	}

	private function process_postback_subscr_failed( $payment ) {
		// Do nothing for now.
	}

	/**
	 * Render the payment form
	 *
	 * @param \HubloyMembership\Model\Invoice $invoice - the invoice model
	 *
	 * @return string
	 */
	public function render_payment_form( $invoice ) {
		return '';
	}

	/**
	 * Render the subscription payment update form
	 *
	 * @param \HubloyMembership\Model\Plan $plan - the plan model
	 *
	 * @return string
	 */
	public function render_payment_update_form( $plan ) {
		return '';
	}

	/**
	 * Process Payment
	 *
	 * @param \HubloyMembership\Model\Invoice $invoice - the invoice model
	 *
	 * @since 1.0.0
	 */
	public function process_payment( $invoice ) {
		$credentials = $this->get_credentials();
		$plan        = $invoice->get_plan();
		$membership  = $plan->get_membership();
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
			'currency_code' => hubloy-membership_get_currency_symbol(),
			'charset'       => 'utf-8',
			'no_note'       => '1',
			'custom'        => json_encode(
				array(
					'payment_id' => $invoice->invoice_id,
					'plan_id'    => $plan->id,
				)
			),
			'rm'            => '2',
			'return'        => add_query_arg( 'gateway', $this->get_id(), $this->get_return_url() ),
			'cancel_return' => $this->get_cancel_page( $invoice ),
			'notify_url'    => $this->get_listener_url(),
			'cbt'           => get_bloginfo( 'name' ),
			'item_name_1'   => $membership->name,
			'quantity_1'    => '1',
			'amount_1'      => number_format( $invoice->amount, 2, '.', '' ),
		);

		$url  = $credentials['url'];
		$url .= http_build_query( array_filter( $paypal ), '', '&' );
		$url  = str_replace( '&amp;', '&', $url );

		wp_send_json_success(
			array(
				'message' => __( 'Redirecting to PayPal', 'hubloy-membership' ),
				'url'     => $url,
			)
		);
	}

	/**
	 * Process recurring payment
	 *
	 * @param \HubloyMembership\Model\Invoice    $invoice The current invoice.
	 * @param \HubloyMembership\Model\Plan       $plan The current plan.
	 * @param \HubloyMembership\Model\Membership $membership The plan membership.
	 * @param array                     $credentials The gateway credentials
	 *
	 * @since 1.0.0
	 */
	private function process_recurring( $invoice, $plan, $membership, $credentials ) {
		$total             = $payment->amount;
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
			'currency_code' => hubloy-membership_get_currency_symbol(),
			'charset'       => 'utf-8',
			'custom'        => json_encode(
				array(
					'payment_id' => $invoice->invoice_id,
					'plan_id'    => $plan->id,
				)
			),
			'rm'            => '2',
			'return'        => add_query_arg( 'gateway', $this->get_id(), $this->get_return_url() ),
			'cancel_return' => $this->get_cancel_page( $invoice ),
			'notify_url'    => $this->get_listener_url(),
			'cbt'           => get_bloginfo( 'name' ),
			'sra'           => '1',
			'src'           => '1',
			'cmd'           => '_xclick-subscriptions',
			'item_name'     => $membership->name,
		);

		// Regular subscription price and interval.
		$paypal = array_merge(
			$paypal,
			array(
				'a3' => $invoice->amount,
				'p3' => $p_n,
				't3' => $t_n,
			)
		);

		// If there was a discount, apply it as a "trial" period.
		if ( $membership->trial_enabled ) {
			list( $t_n, $p_n ) = $this->get_subscription_period_vars( $membership->trial_duration, $membership->trial_period );
			$paypal            = array_merge(
				$paypal,
				array(
					'a1' => $membership->trial_price,
					'p1' => $p_n,
					't1' => $t_n,
				)
			);
		}

		$url  = $credentials['url'];
		$url .= http_build_query( $paypal );
		$url  = str_replace( '&amp;', '&', $url );

		wp_send_json_success(
			array(
				'message' => __( 'Redirecting to PayPal', 'hubloy-membership' ),
				'url'     => $url,
			)
		);
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
			'D' => array(
				'days'  => 1,
				'limit' => 90,
			),
			'W' => array(
				'days'  => 7,
				'limit' => 52,
			),
			'M' => array(
				'days'  => 30,
				'limit' => 24,
			),
			'Y' => array(
				'days'  => 365,
				'limit' => 5,
			),
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

				if ( abs( $days - $d1 ) > abs( $days - $d2 ) ) {
					$best_match = array( $period, $d );
				}
			}
		}

		if ( ! $best_match ) {
			wp_die( __( 'Can not create a valid PayPal subscription configuration from plan.', 'hubloy-membership' ) );
		}

		return $best_match;
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

	/**
	 * Validate the IP request
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function validate_ipn() {
		$post_data = array();

		if ( ini_get( 'allow_url_fopen' ) ) {
			$post_data = parse_str( file_get_contents( 'php://input' ) );

			if ( function_exists( 'get_magic_quotes_gpc' ) && get_magic_quotes_gpc() ) {
				$post_data = stripslashes_deep( $post_data );
			}
		}

		if ( ! $post_data ) {
			@ini_set( 'post_max_size', '12M' );
			$post_data = stripslashes_deep( $_POST );
		}

		if ( ! $post_data ) {
			return false;
		}

		$post_data_array['cmd'] = '_notify-validate';

		foreach ( $post_data as $key => $value ) {
			$post_data_array[ $key ] = $value;
		}

		$settings = $this->settings->get_gateway_setting( $this->get_id() );
		if ( 'test' === $settings['mode'] ) {
			$url = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';
		} else {
			$url = 'https://ipnpb.paypal.com/cgi-bin/webscr';
		}

		$request_args = array(
			'method'      => 'POST',
			'timeout'     => 60,
			'redirection' => 5,
			'httpversion' => '1.1',
			'blocking'    => true,
			'sslverify'   => false,
			'body'        => $post_data_array,
		);
		$response     = wp_remote_post( $url, $request_args );

		if ( is_wp_error( $response ) || ! $response ) {
			return false;
		}

		if ( 'VERIFIED' !== wp_remote_retrieve_body( $response ) ) {
			return false;
		}
		return true;
	}
}

