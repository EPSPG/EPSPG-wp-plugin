<?php
/**
 * Class Payment
 *
 *
 * @package MCoder\EPS\API
 */

namespace MCoder\EPS\API;

use MCoder\EPS\Gateway\Processor;
use WP_Error;
use WP_Http;
use WP_REST_Server;

/**
 * Class Payment
 */
class Payment extends EPSBaseRestController {

	/**
	 * Initialize the class
	 */
	public function __construct() {
		$this->rest_base = 'payment';
	}

	public function debug_to_console($data) {
		$output = $data;
		if (is_array($output))
			$output = implode(',', $output);
	
		echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 *
	 * @return void
	 */
	public function register_routes() {

		$this->debug_to_console('sandbox');
		register_rest_route(
			$this->get_namespace(),
			sprintf( '/%s/create-payment', $this->rest_base ),
			[
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'create_payment' ],
					'permission_callback' => [ $this, 'admin_permissions_check' ],
				],
				'schema' => [ $this, 'get_item_schema' ],
			]
		);

	}


	/**
	 * Create Payment data.
	 * Returning with payment data and request header.
	 *
	 * @param object $request Request Object.
	 *
	 *
	 * @return WP_Error|\WP_REST_Response
	 */
	public function create_payment( $request ) {

		$get_amount      = $request->get_param( 'amount' );
		$mc_processor = Processor::get_instance();
		$amount          = $get_amount ? $get_amount : wp_rand( 10, 100 );
		$invoice_id      = sprintf( 'TBP%s', str_pad( wp_rand( 10, 999 ), 5, 0, STR_PAD_LEFT ) );
		$create_payment  = $mc_processor->create_payment( (float) $amount, $invoice_id );

		if ( is_wp_error( $create_payment ) ) {
			return new WP_Error(
				'mc_eps_rest_api_payment_create_payment_error',
				__( $create_payment->get_error_message(), 'mc-eps' ), //phpcs:ignore
				[ 'status' => WP_Http::BAD_REQUEST ]
			);
		}

		$request_params = [
			'headers'     => $mc_processor->get_authorization_header()['headers'],
			'body_params' => [
				'amount'                => $amount,
				'currency'              => 'BDT',
				'intent'                => 'sale',
				'merchantInvoiceNumber' => $invoice_id,
			],
		];

		$response = [
			'title'          => __( 'Create Payment', 'mc-eps' ),
			'data'           => $create_payment,
			'request_params' => $request_params,
			'request_url'    => $mc_processor->payment_create_url(),
		];

		return rest_ensure_response( $response );
	}


	/**
	 * Registering single route which will have a id as a argument.
	 *
	 * @param string $path            Route Path.
	 * @param array  $callback_method Callback function to serve.
	 *
	 *
	 * @return void
	 */
	private function register_single_route( $path, array $callback_method ) {
		register_rest_route(
			$this->get_namespace(),
			sprintf( '/%s/%s', $this->rest_base, $path ),
			[
				'args'   => [
					'id' => [
						'description' => __( 'Unique identifier for the payment.', 'mc-eps' ),
						'type'        => 'string',
					],
				],
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => $callback_method,
					'permission_callback' => [ $this, 'admin_permissions_check' ],
					'args'                => $this->get_collection_params(),
				],
				'schema' => [ $this, 'get_item_schema' ],
			]
		);
	}
}
