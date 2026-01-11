<?php
/**
 * API Class
 *
 *
 * @package MCoder\EPS
 */

namespace MCoder\EPS;

use MCoder\EPS\API\Payment;


/**
 * Class API
 */
class API {

	/**
	 * Holds the api classes.
	 *
	 * @var array
	 */
	private $classes;

	/**
	 * Initialize the class
	 *
	 *
	 * @return void
	 */
	public function __construct() {
		$this->classes = [
			Payment::class
		];

		add_action( 'rest_api_init', [ $this, 'register_api' ] );
	}

	/**
	 * Register the API
	 *
	 *
	 * @return void
	 */
	public function register_api() {
		foreach ( $this->classes as $class ) {
			$object = new $class();
			$object->register_routes();
		}
	}
}
