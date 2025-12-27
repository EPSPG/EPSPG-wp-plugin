<?php
/**
 * Class Settings
 *
 * @since 0.0.1
 *
 * @package MCoder\EPS\Admin
 *
 * @author Md Mokbul Hossain
 */

namespace MCoder\EPS\Admin;


/**
 * Class Settings
 */
class Settings {

    public $errors = [];
    public $successes = [];
	/**
	 * Option key to hold the settings in database
	 */
	const OPTION_KEY = 'mc_eps_settings';

	/**
	 * Get settings field
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */

    public function plugin_page() {
       
        $data = get_option('mc_eps_settings'); 
        $values = array(
          'api_base_url' =>  "",
          'module_val' =>  "",
          'merchent_code' =>  "",
          'password' =>  "",
          'redirect_url' =>  "",
          'mode' => 'sandbox'
      );
        if ( $data ) {
          $data = unserialize($data);
          $values = array(
              'api_base_url' =>  $data['api_base_url'],
              'module_val' =>  $data['module_val'],
              'merchent_code' =>  $data['merchent_code'],
              'password' =>  "",
              'redirect_url' =>  $data['redirect_url'],
              'mode' => $data['mode']
          );
       }

        include __DIR__ . '/views/setting.php';
    }

        /**
     * Handle the form
     *
     * @return void
     */
    public function form_handler() {

        $error_msg = 'Invalid credentials !';
        $success_msg = 'success';

        if ( ! isset( $_POST['submit_mc_eps_setting'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( $_POST['_wpnonce'], 's_mc_eps_setting' ) ) {
            wp_die( 'Are you cheating?' );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Are you cheating?' );
        }

        $module_val = $_POST['module_val'] ?? "";
        $merchent_code = $_POST['merchent_code'] ?? "";
        $password = $_POST['password'] ?? null;
        $mode = $_POST['mode'] ?? "sandbox";
        $plugin_key = "";

        $data = get_option('mc_eps_settings'); 

        if($data) {
            $data = unserialize($data);
            $plugin_key = $data['plugin_key'];
        }

        if($password) {

            $processor = dc_eps()->gateway->processor();
            // $get_key_response = $processor->get_key( $merchent_code, $password, $module_val, $mode );
            $get_key_response = $processor->get_token( null, $password, $module_val,$merchent_code,$mode);

            if($get_key_response && isset($get_key_response['token']) && $get_key_response['token'] != null ) {

                $this->successes['success_message'] = __( $success_msg, 'mc-eps' );
            }

            else {

                $this->errors['error_message'] = __( 'Unauthorized!', 'mc-eps' );
                return;
            }

            $plugin_key =  $password;

        }

        $dataArray = array(
            'api_base_url' => $_POST['api_base_url'] ?? "",
            'module_val' => $_POST['module_val'] ?? "",
            'merchent_code' => $_POST['merchent_code'] ?? "",
            'plugin_key' => $plugin_key,
            'redirect_url' => $_POST['redirect_url'] ?? "",
            'mode' => $_POST['mode'] ?? "sandbox"
        );

        update_option( 'mc_eps_settings', serialize($dataArray) );

        //var_dump( $_POST );
        //exit;
    }
}