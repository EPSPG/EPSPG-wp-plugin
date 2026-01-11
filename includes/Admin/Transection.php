<?php
/**
 * Class Transection
 *
 * @since 1.0.0
 *
 * @package MCoder\EPS\Admin
 *
 * @author Md Mokbul Hossain
 */

namespace MCoder\EPS\Admin;

/**
 * Class Transection
 */
class Transection {
	/**
	 * Option key to hold the Transection in database
	 */
	const OPTION_KEY = 'mc_eps_transections';

	/**
	 * Get settings field
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */

    public function plugin_page() {
       
        include __DIR__ . '/views/transection.php';
    }

}