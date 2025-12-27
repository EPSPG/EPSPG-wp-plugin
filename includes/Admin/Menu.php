<?php

namespace MCoder\EPS\Admin;

/**
 * The Menu handler class
 */
class Menu {

	public $settings;
    /**
     * Initialize the class
     */
    function __construct($settings) {
		$this->settings = $settings;
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );
    }

    /**
     * Register our menu page
     *
     * @return void
     */

    public function admin_menu() {
		global $submenu;

		$parent_slug = 'mc-eps';
		$capability  = 'manage_options';

		$hook = add_menu_page( __( 'eps', 'mc-eps' ), __( 'EPS Payment', 'mc-eps' ), $capability, $parent_slug, [ $this, 'transection_page' ], EPS_ASSETS . '/images/eps.png' );

		if ( current_user_can( $capability ) ) {

		add_submenu_page( $parent_slug, __( 'Transactions', 'mc-eps' ), __( 'Transactions', 'mc-eps' ), $capability, $parent_slug, [ $this, 'transection_page' ] );
        add_submenu_page( $parent_slug, __( 'Settings', 'mc-eps' ), __( 'Settings', 'mc-eps' ), $capability, 'eps-settings', [ $this->settings, 'plugin_page' ] );

		}

        add_action( 'load-' . $hook, [ $this, 'init_hooks' ] );
	}

    /**
	 * Initialize our hooks for the admin page
	 *
	 *
	 * @return void
	 */
	public function init_hooks() {

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

    }
    
    /**
     * Handel the main page
     *
     * @return void
     */
    public function plugin_page() {
		echo "hello Transection";
	}

	public function transection_page() {

		$transection = new Transection();
        $transection->plugin_page();
	}

    /**
	 * Load scripts and styles for the app
	 *
	 *
	 * @return void
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'eps-script' );
		wp_enqueue_style( 'eps-admin-style' );
    }
	
    /**
	 * Make submenu admin url from slug
	 *
	 * @param string $slug Slug for menu.
	 * @param string $parent_slug Parent slug.
	 *
	 * @since 0.0.1
	 *
	 * @return string
	 */
	private function get_submenu_url( $slug = '', $parent_slug = 'mc-eps' ) {
		return 'admin.php?page=' . $parent_slug . '#/' . $slug;
	}

    /**
     * Handles the settings page
     *
     * @return void
     */
    public function settings_page() {

	    $setting = new Settings();
        $setting->plugin_page();
	}
	

}
