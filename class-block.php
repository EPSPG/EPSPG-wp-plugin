<?php

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

final class Eps_Gateway_Blocks extends AbstractPaymentMethodType {

    private $gateway;
    protected $name = 'eps';// your payment gateway name

    public function initialize() {
        $this->settings = get_option( 'woocommerce_eps_gateway_settings', [] );
        $this->gateway = new MCoder\EPS\Gateway\EPS();
    }

    public function is_active() {
        return $this->gateway->is_available();
    }

    public function get_payment_method_script_handles() {

        wp_register_script(
            'eps-blocks-integration',
            plugin_dir_url(__FILE__) . 'checkout.js',
            [
                'wc-blocks-registry',
                'wc-settings',
                'wp-element',
                'wp-html-entities',
                'wp-i18n',
            ],
            null,
            true
        );
        if( function_exists( 'wp_set_script_translations' ) ) {            
            wp_set_script_translations( 'eps-blocks-integration');
            
        }
        return [ 'eps-blocks-integration' ];
    }

    /*public function get_payment_method_data() {
        return [
            'title' => $this->gateway->title,
            'icon' => $this->gateway->icon,
        ];
    }*/
    public function get_payment_method_data() {
       
    return [
        'title'       => $this->gateway->title,
        'icon'        => esc_url( $this->gateway->icon ),
        'description' => wp_kses_post( $this->gateway->description ),
    ];
}

}
?>