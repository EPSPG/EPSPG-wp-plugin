<?php

namespace MCoder\EPS;

/**
 * The admin class
 */
class Admin {

    /**
     * Initialize the class
     */
    function __construct() {

        $settings = new Admin\Settings();
        $this->dispatch_actions( $settings );

        new Admin\Menu($settings);
    }

    /**
     * Dispatch and bind actions
     *
     * @return void
     */
    public function dispatch_actions( $settings ) {

        add_action( 'admin_init', [ $settings, 'form_handler' ] );
    }
}
