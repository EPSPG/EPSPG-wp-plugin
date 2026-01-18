<?php
/**
 * Class EPS
 *
 * @package MCoder\EPS\Gateway
 */

namespace MCoder\EPS\Gateway;

class EPS extends \WC_Payment_Gateway {

    /**
     * EPS constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->init();
        $this->init_settings();

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ]);
        add_action('woocommerce_thankyou_' . $this->id, [ $this, 'thank_you_page' ]);
        $this->id           = 'eps';
    $this->has_fields   = false;
    $this->method_title = __('EPS', 'mc-eps');
    $this->title        = __('Visa/Mastercard/MFS', 'mc-eps');
    $this->description  = '<img src="https://eps.com.bd/images/banner.png" 
             alt="EPS Banner" 
             style="max-width:100%; margin-top:8px;">';
    $this->icon         = 'https://eps.com.bd/images/logo.png';

   // parent::__construct(); // ✅ REQUIRED

    }

    /**
     * Init gateway settings
     */
    public function init() {
        $this->id           = 'eps';
        $this->icon         = 'Visa/Mastercard/MFS'; // External EPS logo
        $this->has_fields   = true;
        $this->method_title = __('EPS', 'mc-eps');
        $this->title        = __('EPS (Easy Payment System)', 'mc-eps');
        $this->description  = __('',
    'mc-eps'
);
        
    }

    /**
     * Show EPS logo and label on checkout page under payment method
     */
    public function payment_fields() {
        echo '<div style="margin-bottom:10px; display:flex; align-items:center;">
            <img src="https://eps.com.bd/images/logo.png" style="height: 24px; margin-right: 10px;" alt="EPS Logo" />
            <strong style="color: black;">Visa/Mastercard/MFS</strong>
        </div>';
    }

    /**
     * Admin options with settings link
     */
    public function admin_options() {
        parent::admin_options();

        $eps_settings_url = admin_url('admin.php?page=mc-eps');

        printf(
            esc_html__('%1$sYou will get %2$s setting options in %3$s here %4$s.%5$s', 'mc-eps'),
            '<p>',
            esc_html($this->method_title),
            wp_kses_post(sprintf('<a href="%s">', $eps_settings_url)),
            '</a>',
            '</p>'
        );
    }

    /**
     * Handle payment process
     */
    public function process_payment($order_id) {
        $order = wc_get_order($order_id);
        $processor = dc_eps()->gateway->processor();

        $url = $this->get_return_url($order);
        $split_info = explode("order-received", $url);
        $r_url = count($split_info) ? $split_info[0] : $order->get_cancel_endpoint();
        $invoice_id = time();

        $create_payment_data = $processor->create_payment(
            (float) $order->get_total(),
            $invoice_id,
            $r_url,
            $this->get_return_url($order),
            $order
        );

        if (!$create_payment_data) {
            wc_add_notice(__('Payment error: ', 'mc-eps') . "Try again", 'error');
            return;
        }

        if (!isset($create_payment_data['RedirectURL']) || $create_payment_data['RedirectURL'] == null) {
            $err_msg = $create_payment_data['FailedReason'] ?? 'Error';
            wc_add_notice(__('Payment error: ', 'mc-eps') . $err_msg, 'error');
            return;
        }
        
        return [
            'result'              => 'success',
            'order_number'        => $order_id,
            'amount'              => (float) $order->get_total(),
            'checkout_order_pay'  => $order->get_checkout_payment_url(),
            'redirect'            => $create_payment_data['RedirectURL'],
            'create_payment_data' => $create_payment_data,
        ];
    }

    /**
     * Handle thank you page actions
     */
    public function thank_you_page($order_id) {
        $order = wc_get_order($order_id);

        if (!$order || 'eps' !== $order->get_payment_method()) {
            return;
        }

        $processor = dc_eps()->gateway->processor();
        global $wp;
        $current_url = add_query_arg($_SERVER['QUERY_STRING'], '', home_url($wp->request));
        $url_components = parse_url($current_url);
        parse_str($url_components['query'] ?? '', $params);

        $check_payment_data = $processor->check_payment($params['MerchantTransactionId'] ?? '');
        $status = 'Fail';
        $transection_id = $params['EPSTransactionId'] ?? null;

        if (!$check_payment_data) {
            $status = 'Payment Status Check Fail';
        } else {
            $status = $check_payment_data['Status'] ?? $check_payment_data['ErrorMessage'];
            $transection_id = $check_payment_data['FinancialEntity'];
        }

        $args = [
            'order_id'             => $order_id,
            'amount'               => (float) $order->get_total(),
            'transection_id'       => $transection_id,
            'response_description' => $status,
            'customer_account'     => $params['MerchantTransactionId'] ?? '',
        ];

        insertTransectionInfo($args);

        $order->payment_complete();
    }
}
