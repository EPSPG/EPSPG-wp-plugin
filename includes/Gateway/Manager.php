<?php
/**
 * Class Manager.
 *
 *
 * @package MCoder\EPS
 */

namespace MCoder\EPS\Gateway;

/**
 * Class Manager
 */

class Manager {
    /**
	 * Hold instance of Mlajan
	 *
	 * @var Object
	 *
	 * @since 0.0.1
	 */
	public $eps_instance;
    /**
	 * Manager constructor.
	 *
	 *
	 * @return void
	 */
	public function __construct() {
        $this->setup_hooks();
    }
    
    /**
	 * Setup Hooks
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	private function setup_hooks() {

		add_filter( 'woocommerce_payment_gateways', [ $this, 'register_gateway' ] );
		
	}

		
    /**
	 * Add payment class to the container
	 *
	 * @since 0.0.1
	 *
	 * @return EPS
	 */
	public function eps() {
		$this->eps_instance = false;

		if ( ! $this->eps_instance ) {
			$this->eps_instance = new EPS();
		}

		return $this->eps_instance;
	}

	/**
	 * Register WooCommerce Payment Gateway
	 *
	 * @param array $gateways All Gateways.
	 *
	 * @return array
	 */
	public function register_gateway( $gateways ) {
		$gateways[] = $this->eps();

		return $gateways;
    }
    
    /**
	 * Get EPS processor class instance
	 *
	 *
	 * @return Processor
	 */
	public function processor() {
		return Processor::get_instance();
	}
}