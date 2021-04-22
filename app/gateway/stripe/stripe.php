<?php
namespace Hammock\Gateway\Stripe;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Hammock\Base\Gateway;
use Hammock\Model\Invoice;
use Hammock\Model\Membership;
use Hammock\Helper\Currency;
use Hammock\Services\Members;
use Hammock\Services\Memberships;
use Hammock\Services\Transactions;
/**
 * Stripe gateway
 *
 * @since 1.0.0
 */
class Stripe extends Gateway {

	/**
	 * The member service
	 * 
	 * @since 1.0.0
	 * 
	 * @var object
	 */
	private $member_service = null;


	/**
	 * The membership service
	 * 
	 * @since 1.0.0
	 * 
	 * @var object
	 */
	private $membership_service = null;

	/**
	 * The transaction service
	 * 
	 * @since 1.0.0
	 * 
	 * @var object
	 */
	private $transaction_service = null;

	/**
	 * The Stripe public key
	 * 
	 * @since 1.0.0
	 * 
	 * @var string
	 */
	private $public_key = '';

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
		$this->member_service = new Members();
		$this->membership_service = new Memberships();
		$this->transaction_service = new Transactions();
		$this->id = 'stripe';
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
		if ( ! isset( $gateways['stripe'] ) ) {
			$gateways['stripe'] = array(
				'name' => __( 'Stripe Gateway', 'hammock' ),
				'logo' => HAMMOCK_ASSETS_URL . '/img/gateways/stripe.png',
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
		if ( ! class_exists( 'Stripe\Stripe' ) ) {
			require_once( HAMMOCK_LIB_DIR . 'stripe/init.php' );
		}
		$settings = $this->settings->get_gateway_setting( $this->id );
		if ( $settings['enabled'] ) {
			if ( $settings['mode'] === 'live' ) {
				$this->public_key = $settings['publishable_key'];
				\Stripe\Stripe::setApiKey( $settings['secret_key'] );
			} else {
				$this->public_key = $settings['test_publishable_key'];
				\Stripe\Stripe::setApiKey( $settings['test_secret_key'] );
			}
			\Stripe\Stripe::setAPIVersion( '2020-03-02' );
		}
	}
	

	/**
	 * Gateway settings
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function settings( $data = '' ) {
		$view       = new \Hammock\View\Backend\Gateways\Stripe();
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
		$settings                         = $this->settings->get_gateway_setting( $this->id );
		$settings['enabled']              = isset( $data[ $this->id ] ) ? true : false;
		$settings['mode']                 = sanitize_text_field( $data['stripe_mode'] );
		$settings['publishable_key']      = sanitize_text_field( $data['publishable_key'] );
		$settings['secret_key']           = sanitize_text_field( $data['secret_key'] );
		$settings['test_publishable_key'] = sanitize_text_field( $data['test_publishable_key'] );
		$settings['test_secret_key']      = sanitize_text_field( $data['test_secret_key'] );
		$this->settings->set_gateway_setting( $this->id, $settings );
		$this->settings->save();
		return $settings;
	}

	/**
	 * Format stripe amount
	 * This returns the cent value of the amount
	 * 
	 * @param int $amount - the current amount
	 * 
	 * @since 1.0.0
	 * 
	 * @return int
	 */
	private function stripe_amount( $amount ) {
		return intval( $amount * 100 );
	}

	/**
	 * Register script used for the gateway
	 * 
	 * @since 1.0.0
	 */
	public function register_scripts() {
		wp_register_script(
			'hammock-stripe-checkout-js',
			HAMMOCK_PLUGIN_URL . 'app/gateway/stripe/js/stripe.js',
			array(),
			HAMMOCK_VERSION,
			true
		);
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
		$membership = new Membership( $membership_id );
		if ( $membership->is_recurring() ) {
			try {
				$plan_data = array(
					'amount' 	=> $this->stripe_amount( $membership->price ),
					'currency' 	=> $this->get_currency(),
					'interval' 	=> $membership->duration,
					'product' 	=> array( 'name' => $membership->name ),
					'active'	=> $membership->enabled
				);
				if ( $membership->trial_enabled ) {
					$plan_data['trial_period_days'] = $membership->get_trial_period_days();
				}

				$stripe_plan = \Stripe\Plan::create( $plan_data );

				$this->logger->log( $stripe_plan );


				if ( $stripe_plan ) {
					$this->membership_service->save_meta( $membership_id, 'stripe_plan_id', $stripe_plan->id );
				}
			} catch ( \Throwable $e ) {
				$this->logger->log( $e );
				//assume no customer found
			} catch ( \Exception $e ) {
				//assume no customer found
				$this->logger->log( $e );
			}
		}
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
		$membership = new Membership( $membership_id );
		if ( $membership->is_recurring() ) {
			$stripe_plan_id = $membership->get_meta_value( 'stripe_plan_id' );
			if ( $stripe_plan_id ) {
				try {
					$plan_data = array(
						'active' => $membership->enabled
					);

					if ( $membership->trial_enabled ) {
						$plan_data['trial_period_days'] = $membership->get_trial_period_days();
					} else {
						$plan_data['trial_period_days'] = 0;
					}
					$updated_plan = \Stripe\Plan::update( $stripe_plan_id, $plan_data );

				} catch ( \Throwable $e ) {
					$this->logger->log( $e );
					//assume no customer found
				} catch ( \Exception $e ) {
					//assume no customer found
					$this->logger->log( $e );
				}
			} else {
				//Create if not exist
				$this->membership_created_sync( $membership_id );
			}
		}
	}

	/**
	 * Handle the ipn callbacks
	 * 
	 * @since 1.0.0
	 */
	public function ipn_notify() {
		$event_id = '';
		if ( empty( $_REQUEST['event_id'] ) ) {
			$body = @file_get_contents( 'php://input' );
			$post_event = json_decode( $body );
			if ( !empty( $post_event ) ) {
				$event_id = sanitize_text_field( $post_event->id );
			}
		} else {
			$event_id = sanitize_text_field( $_REQUEST['event_id'] );
		}
		if ( !empty( $event_id ) ) {
			try {
				$stripe_event = \Stripe\Event::retrieve( $event_id );
				if ( $stripe_event && !empty( $stripe_event->id ) ) {
					switch ( $stripe_event->type ) {
						case 'invoice.payment_succeeded' :
							$stripe_invoice = $event->data->object;
							if ( $stripe_invoice->amount_due > 0 ) {
								$invoice_id 		= $stripe_invoice->id;
								$invoice 			= $this->transaction_service->get_invoice( $invoice_id );
								$invoice_amount 	= $stripe_invoice->total / 100.0;
								$invoice_subtotal 	= $stripe_invoice->subtotal / 100.0;
								if ( !$invoice ) {
									$stripe_customer 	= \Stripe\Customer::retrieve( $stripe_invoice->customer );
									if ( $stripe_customer ) {
										$notes		= array();
										$invoice 	= new Invoice();
										$email 		= $stripe_customer->email;
										if ( !function_exists( 'get_user_by' ) ) {
											include_once( ABSPATH . 'wp-includes/pluggable.php' );
										}
										$notes[] 	= sprintf( __( 'Payment event %s on subscription %s', 'hammock' ), $stripe_event->id, $stripe_invoice->subscription );
										$member 	= false;
										$user 		= get_user_by( 'email', $email );
										if ( $user ) {
											$member = $this->member_service->get_member_by_user_id( $user->ID );
										} else {
											$notes[] = __( 'Could not find member for transaction', 'hammock' );
										}
										
										$plan = false;
										if ( $member ) {
											$plan = $this->get_subscription_plan( $stripe_invoice->lines->data, $member->get_plans() );
										}
										if ( !$plan ) {
											$notes[] = __( 'Error getting plan', 'hammock' );
										}

										$custom_data = array(
											'transaction_id' 				=> $stripe_invoice->id,
											'subscription_transaction_id' 	=> $stripe_invoice->subscription
										);

										$invoice->user_id				= $user ? $user->ID : 0;
										$invoice->gateway 				= $this->id;
										$invoice->status 				= Transactions::STATUS_PAID;
										$invoice->member_id 			= $member ? $member->id : 0;
										$invoice->plan_id 				= $plan ? $plan->id : 0;
										$invoice->amount 				= Currency::format_price( $invoice_subtotal );
										if ( isset( $stripe_invoice->tax_percent ) ) {
											$invoice->tax_rate			= $stripe_invoice->tax_percent;
										}
										$invoice->gateway_identifier 	= $stripe_invoice->id;
										$invoice->custom_data  			= $custom_data;
										$invoice->notes 				= $notes;
										$invoice->save();
									}
								} else {
									$invoice->add_note( sprintf( __( 'Order %s already paid for : %s', 'hammock' ), $stripe_invoice->id, $stripe_event->id ) );
									$invoice->save();
								}
							}
						break;
					}
				}
			} catch ( \Exception $e ) {
				$this->logger->log( $e );
			}
		}
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
		$member 			= $this->member_service->get_member_by_id( $plan->member_id );
		$stripe_customer 	= $this->get_stripe_customer( $member );
		if ( $stripe_customer ) {
			ob_start();
			wp_localize_script( 'hammock-stripe-checkout-js', 'hammock_stripe', array(
				'publisher_key' => $this->public_key,
				'locale'        => get_locale(),
				'email'         => $member->get_user_info( 'email' ),
				'image'         => get_site_icon_url( 512, '', 1 ),
				'name'          => $member->get_user_info( 'name' ),
				'description'   => __( 'Update Card Information', 'hammock' ),
			) );
		}
	}

	/**
	 * Process Payment
	 * 
	 * @param \Hammock\Model\Invoice $invoice - the invoice model
	 * 
	 * @since 1.0.0
	 */
	public function process_payment( $invoice ) {
		$member 			= $this->member_service->get_member_by_id( $invoice->member_id );
		$stripe_customer 	= $this->get_stripe_customer( $member );
		if ( $stripe_customer ) {
			$payment_intent 	= false;
			$payment_intent_id 	= $invoice->get_custom_data( 'stripe_payment_intent_id' );
			if ( $payment_intent_id ) {
				try {
					$payment_intent = \Stripe\PaymentIntent::retrieve( $payment_intent_id );
				} catch ( \Stripe\Error\Base $e ) {
					$invoice->set_custom_data( 'error', array(
						'message' => $e->getMessage()
					) );
					$this->logger->log( $e );
				} catch ( \Throwable $e ) {
					$invoice->set_custom_data( 'error', array(
						'message' => $e->getMessage()
					) );
					$this->logger->log( $e );
				} catch ( \Exception $e ) {
					$invoice->set_custom_data( 'error', array(
						'message' => $e->getMessage()
					) );
					$this->logger->log( $e );
				}
				
			} else {
				$payment_intent = $this->create_payment_intent( $stripe_customer->id, $invoice );
			}
			if ( $payment_intent ) {
				$invoice->set_custom_data( 'stripe_payment_intent_id', $payment_intent->id );
				try {
					$params = array(
						'expand' => array(
							'payment_method',
						),
					);
					$payment_intent->confirm( $params );
				} catch ( \Stripe\Error\Base $e ) {
					$invoice->set_custom_data( 'error', array(
						'message' => $e->getMessage()
					) );
					$this->logger->log( $e );
				} catch ( \Throwable $e ) {
					$invoice->set_custom_data( 'error', array(
						'message' => $e->getMessage()
					) );
					$this->logger->log( $e );
				} catch ( \Exception $e ) {
					$invoice->set_custom_data( 'error', array(
						'message' => $e->getMessage()
					) );
					$this->logger->log( $e );
				}

				if ( 'requires_action' == $payment_intent->status ) {
					$invoice->set_custom_data( 'error', array(
						'message' => __( 'Customer authentication is required to complete this transaction. Please complete the verification steps issued by your payment provider.', 'hammock' )
					) );
				} else {
					$invoice->status 	= Transactions::STATUS_PAID;
				}
			}

			$invoice->save();
		}
		
	}

	/**
	 * Create payment intent
	 * 
	 * @param string $customer_id - the stripe customer id
	 * @param \Hammock\Model\Invoice $invoice - the invoice model
	 * 
	 * @since 1.0.0
	 * 
	 * @return 
	 */
	private function create_payment_intent( $customer_id, $invoice ) {
		try {
			$description	= sprintf( __( '%s : Order # %s', 'hammock' ), get_bloginfo( 'name' ), $invoice->invoice_id );
			$stripe_intent 	= \Stripe\PaymentIntent::create( array(
				'customer'            	=> $customer_id,
				'amount' 				=> $this->stripe_amount( $invoice->amount ),
				'currency' 				=> $this->get_currency(),
				'payment_method_types' 	=> array( 'card' ),
				'description'			=> $description,
				'setup_future_usage'  	=> 'off_session',
				'confirmation_method' 	=> 'manual',
			) );
			return $stripe_intent;
		} catch ( \Throwable $e ) {
			$this->logger->log( $e );
			//assume no customer found
		} catch ( \Exception $e ) {
			//assume no customer found
			$this->logger->log( $e );
		}
		return false;
	}

	private function confirm_payment_intent( $payment_intent, $invoice ) {
		try {
			$params = array(
				'expand' => array(
					'payment_method',
				),
			);
			$payment_intent->confirm( $params );
		} catch ( \Stripe\Error\Base $e ) {
			$order->error = $e->getMessage();
			$this->logger->log( $e );
		} catch ( \Throwable $e ) {
			$order->error = $e->getMessage();
			$this->logger->log( $e );
		} catch ( \Exception $e ) {
			$order->error = $e->getMessage();
			$this->logger->log( $e );
		}

		if ( 'requires_action' == $payment_intent->status ) {
			//pass errors to user in a better way with an action?
			$order->errorcode = true;
			$order->error = __( 'Customer authentication is required to complete this transaction. Please complete the verification steps issued by your payment provider.', 'hammock' );
			return false;
		}
	}

	/**
	 * Process Refund
	 * 
	 * @param \Hammock\Model\Invoice $invoice - the invoice model
	 * @param \Hammock\Model\Plan $plan - the plan model
	 * @param double $amount - the amount
	 * 
	 * @since 1.0.0
	 */
	public function process_refund( $invoice, $plan, $amount ) {
		$stripe_invoice_id = $invoice->get_custom_data( 'stripe_invoice_id' );
		if ( $stripe_invoice_id ) {
			try {
				$stripe_invoice = \Stripe\Invoice::retrieve( $stripe_invoice_id );
				$this->logger->log( $stripe_invoice );
				if ( ! empty( $stripe_invoice ) && ! empty( $stripe_invoice->charge ) ) {
					
					$transaction_id = $stripe_invoice->charge;

					//Get charge to process refund
					$charge = \Stripe\Charge::retrieve( $transaction_id );

					$this->logger->log( $charge );

					if ( $charge && $charge->id ) {
						$refund =  \Stripe\Refund::create([
							'charge' 	=> $charge->id,
							'amount'	=> $this->stripe_amount( $amount )
						]);
						$this->logger->log( $refund );
						if ( $refund->status == "succeeded" ) {
							$invoice->status = Transactions::STATUS_REFUNDED;
							$invoice->save();
						}
					}
				}
			} catch ( \Throwable $e ) {
				$this->logger->log( $e );
				//assume no customer found
			} catch ( \Exception $e ) {
				//assume no customer found
				$this->logger->log( $e );
			}
		}
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
		$stripe_subscription_id = $plan->get_meta_value( 'stripe_subscription_id' );
		if ( $stripe_subscription_id  ) {
			try {
				$subscription 	= \Stripe\Subscription::retrieve( $stripe_subscription_id );
				$response 		=  $subscription->delete();
				$this->logger->log( $response );
				if ( $response->id ) {
					$plan->status = Members::STATUS_CANCELED;
					$plan->save();
				}
			} catch ( \Throwable $e ) {
				$this->logger->log( $e );
				//assume no customer found
			} catch ( \Exception $e ) {
				//assume no customer found
				$this->logger->log( $e );
			}
		}
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
		$stripe_subscription_id = $plan->get_meta_value( 'stripe_subscription_id' );
		if ( $stripe_subscription_id  ) {
			try {
				$response = \Stripe\Subscription::update(
					$stripe_subscription_id,
					array(
						'pause_collection' => array( 'behavior' => 'void' ),
					)
				);
				$this->logger->log( $response );
				if ( $response->id ) {
					$plan->status = Members::STATUS_PAUSED;
					$plan->save();
				}
			} catch ( \Throwable $e ) {
				$this->logger->log( $e );
				//assume no customer found
			} catch ( \Exception $e ) {
				//assume no customer found
				$this->logger->log( $e );
			}
		}
		return false;
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
		$stripe_subscription_id = $plan->get_meta_value( 'stripe_subscription_id' );
		if ( $stripe_subscription_id  ) {
			try {
				$response = \Stripe\Subscription::update(
					$stripe_subscription_id,
					array(
						'pause_collection' => '',
					)
				);
				$this->logger->log( $response );
				if ( $response->id ) {
					$plan->status = Members::STATUS_ACTIVE;
					$plan->save();
				}
			} catch ( \Throwable $e ) {
				$this->logger->log( $e );
				//assume no customer found
			} catch ( \Exception $e ) {
				//assume no customer found
				$this->logger->log( $e );
			}
		}
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
		$this->delete_stripe_customer( $member );
	}

	/**
	 * Get Stripe customer
	 * If the stripe customer exists, return the object, else return false
	 * 
	 * @param int|object $member - the member or the id
	 * 
	 * @since 1.0.0
	 * 
	 * @return mixed
	 */
	protected function get_stripe_customer( $member ) {
		if ( is_int( $member ) ) {
			$member = $this->member_service->get_member_by_id( $member );
		}
		if ( $member->exists() ) {
			$stripe_customer_id = $member->get_meta_value( 'stripe_customer_id' );
			if ( $stripe_customer_id ) {
				try {
					$customer = \Stripe\Customer::retrieve( $stripe_customer_id );
					return $customer;
				} catch ( \Throwable $e ) {
					$this->logger->log( $e );
					//assume no customer found
				} catch ( \Exception $e ) {
					//assume no customer found
					$this->logger->log( $e );
				}
			} else {
				return $this->create_stripe_customer( $member );
			}
		}
		return false;
	}

	/**
	 * Set Stripe customer
	 * Saves a stripe customer
	 * 
	 * @param int|object $member - the member or the id
	 * 
	 * @since 1.0.0
	 * 
	 * @return mixed
	 */
	protected function create_stripe_customer( $member ) {
		if ( is_int( $member ) ) {
			$member = $this->member_service->get_member_by_id( $member );
		}
		if ( $member->exists() ) {
			$user_info = $member->user_info;
			try {
				$customer = \Stripe\Customer::create( array(
					'description' 	=> get_bloginfo( 'name' ) . ' (' . $user_info['email'] . ')',
					'email'       	=> $user_info['email'],
					'name'       	=> $user_info['name'],
				) );

				if ( $customer->id ) {
					$this->member_service->update_meta( $member->id, 'stripe_customer_id', $customer->id );
					$member->refresh_meta();
				}
				return $customer;
			} catch ( \Stripe\Error $e ) {
				$this->logger->log( $e );
			} catch ( \Throwable $e ) {
				$this->logger->log( $e );
			} catch ( \Exception $e ) {
				$this->logger->log( $e );
			}
		}
		return false;
	}

	/**
	 * Delete Stripe customer
	 * 
	 * @param int|object $member - the member or the id
	 * 
	 * @since 1.0.0
	 */
	protected function delete_stripe_customer( $member ) {
		if ( is_int( $member ) ) {
			$member = $this->member_service->get_member_by_id( $member );
		}
		if ( $member->exists() ) {
			$stripe_customer_id = $member->get_meta_value( 'stripe_customer_id' );
			if ( $stripe_customer_id ) {
				try {
					$customer 	= \Stripe\Customer::retrieve( $stripe_customer_id );
					$deleted 	= $customer->delete();
					if ( $deleted->deleted ) {
						$this->member_service->delete_meta( $member->id, 'stripe_customer_id' );
						$member->refresh_meta();
					}
				} catch ( \Throwable $e ) {
					$this->logger->log( $e );
					//assume no customer found
				} catch ( \Exception $e ) {
					//assume no customer found
					$this->logger->log( $e );
				}
			}
		}
	}

	/**
	 * Get subscription plan
	 * 
	 * @param object $subscription_data - the stripe subscription data
	 * @param array $plans - the member plans
	 * 
	 * @since 1.0.0
	 * 
	 * @return mixed
	 */
	private function get_subscription_plan( $subscription_data, $plans ) {
		$subscription 	= false;
		foreach ( $subscription_data as $sub ) {
			foreach ( $plans as $plan ) {
				$membership = $plan->get_memebership();
				if ( $sub->plan->id == $membership->membership_id ) {
					$subscription = $sub;
				}
			}
		}
		return $subscription;
	}
}

