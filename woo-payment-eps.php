<?php
/**
 * Plugin Name: EPS - Easy Payment System
 * Description: An eCommerce payment method that helps you sell anything beautifully.
 * Plugin URI: https://www.eps.com.bd
 * Author: EPS
 * Author URI: https://www.eps.com.bd
 * Version: 0.0.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * The main plugin class
 */
final class MCoder_EPS {

    /**
     * Plugin version
     *
     * @var string
     */
    const version = '0.0.3';
	/**
	 * Holds various class instances.
	 *
	 * @since 0.0.1
	 * @var array
	 */
	private $container = [];
    /**
     * Class construcotr
     */
    private function __construct() {
        $this->define_constants();

        register_activation_hook( __FILE__, [ $this, 'activate' ] );
		register_deactivation_hook( __FILE__, [ $this, 'deactivate' ] );

        add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
        
    }

    /**
     * Initializes a singleton instance
     *
     * @return \MCoder_EPS
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new self();
        }

        return $instance;
    }
	/**
	 * Magic getter to bypass referencing plugin.
	 *
	 * @param mixed $prop Properties to find.
	 *
	 *
	 * @return mixed
	 */
	public function __get( $prop ) {
		if ( array_key_exists( $prop, $this->container ) ) {
			return $this->container[ $prop ];
		}

		return $this->{$prop};
    }
    /**
	 * Magic isset to bypass referencing plugin.
	 *
	 * @param mixed $prop Properties to find.
	 *
	 *
	 * @return mixed
	 */
	public function __isset( $prop ) {
		return isset( $this->{$prop} ) || isset( $this->container[ $prop ] );
	}
    /**
     * Define the required plugin constants
     *
     * @return void
     */
    public function define_constants() {

		define( 'EPS_VERSION', self::version );
        define( 'EPS_FILE', __FILE__ );
        define( 'EPS_PATH', __DIR__ );
        define( 'EPS_URL', plugins_url( '', EPS_FILE ) );
        define( 'EPS_ASSETS', EPS_URL . '/assets' );
    }

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public function init_plugin() {

        $this->includes();
		$this->init_hooks();

    }

    /**
	 * Include the required files
	 *
	 *
	 * @return void
	 */
	public function includes() {

		if ( is_admin() ) {
			$this->container['admin'] = new MCoder\EPS\Admin();
		}

		else {
			$this->container['frontend'] = new MCoder\EPS\Frontend();
		}
    }
    /**
	 * Initialize the hooks
	 *
	 *
	 * @return void
	 */
	public function init_hooks() {
		add_action( 'init', [ $this, 'init_classes' ] );

    }

    /**
	 * Instantiate the required classes
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function init_classes() {

		$this->container['assets']   = new  MCoder\EPS\Assets();
		$this->container['gateway']  = new MCoder\EPS\Gateway\Manager();

		$this->container = apply_filters( 'mc_eps_get_class_container', $this->container );
    }
    
	/**
	 * Placeholder for activation function
	 *
	 * Nothing being called here yet.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function activate() {
		//$installer = new MCoder\Mlajan\Installer();
		$installer = new MCoder\EPS\Installer();

		$installer->run();
	}

	/**
	 * Placeholder for deactivation function
	 *
	 * Nothing being called here yet.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function deactivate() {

	}

    /**
	 * Get DB version key
	 *
	 * @since 0.0.1
	 *
	 * @return string
	 */
	public function get_db_version_key() {

		//return 'mc_mlajan_version';
		return 'mc_eps_version';
    }
    
    /**
	 * Check woocommerce is exists or not
	 *
	 *
	 * @return bool
	 */
	public function has_woocommerce() {
		return class_exists( 'WooCommerce' );
	}
}
/**
 * Custom function to declare compatibility with cart_checkout_blocks feature
 */
function eps_cart_checkout_blocks_compatibility() {
    // Check if the required class exists
    if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
        // Declare compatibility for 'cart_checkout_blocks'
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__, true);
    }
}
// Hook the custom function to the 'before_woocommerce_init' action
add_action('before_woocommerce_init', 'eps_cart_checkout_blocks_compatibility');

// Hook the custom function to the 'woocommerce_blocks_loaded' action
add_action( 'woocommerce_blocks_loaded', 'eps_register_order_approval_payment_method_type' );
add_filter( 'woocommerce_gateway_icon', function ( $icon, $gateway_id ) {
    if ( $gateway_id === 'eps' ) {
        $icon = '<img src="https://eps.com.bd/images/logo.png" 
                   alt="EPS Logo" 
                   style="height:24px; margin-left:8px;">';
    }
    return $icon;
}, 10, 2 );


/**
 * Custom function to register a payment method type

 */
function eps_register_order_approval_payment_method_type() {
    // Check if the required class exists
    if ( ! class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
        return;
    }

    // Include the custom Blocks Checkout class
    require_once plugin_dir_path(__FILE__) . 'class-block.php';

    // Hook the registration function to the 'woocommerce_blocks_payment_method_type_registration' action
    add_action(
        'woocommerce_blocks_payment_method_type_registration',
        function( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
            // Register an instance of My_Custom_Gateway_Blocks
            $payment_method_registry->register( new Eps_Gateway_Blocks );
        }
    );
    
}
/**
 * Initializes the main plugin
 *
 * @return \MCoder_EPS
 */
function dc_eps() {
    return MCoder_EPS::init();
}

// kick-off the plugin
dc_eps();
