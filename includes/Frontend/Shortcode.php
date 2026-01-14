<?php

namespace MCoder\EPS\Frontend;

/**
 * Shortcode handler class
 */
class Shortcode {

    /**
     * Initializes the class
     */
    function __construct() {
        add_shortcode( 'payment_gateway_eps_for_wc', [ $this, 'render_frontend' ] );
    }

	/**
	 * Render frontend app
	 *
	 * @param array  $atts Attributes.
	 * @param string $content Content for shortcode.
	 *
	 * @since 0.0.1
	 *
	 * @return string
	 */
	public function render_frontend( $atts, $content = '' ) {
		$content .= 'Hello World!';

		return $content;
	}
}
