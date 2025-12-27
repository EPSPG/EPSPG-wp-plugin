<?php
/**
 * Class EPSBaseRestController
 *
 *
 * @package MCoder\EPS\API
 *
 */

namespace MCoder\EPS\API;

use WP_Http;
use WP_REST_Controller;

/**
 * Class EPSBaseRestController
 */
class EPSBaseRestController extends WP_REST_Controller {

	/**
	 * Namespace.
	 *
	 * @var string Namespace.
	 */
	public $namespace = 'mc-eps';

	/**
	 * Version.
	 *
	 * @var string version.
	 */
	public $version = 'v1';

	/**
	 * Permission check
	 *
	 * @param \WP_REST_Request $request WP Rest Request.
	 *
	 *
	 * @return \WP_Error|bool
	 */
	public function admin_permissions_check( $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new \WP_Error(
				'mc_eps_permission_error',
				__( 'You have no permission to do that', 'mc-eps' ),
				[ 'status' => WP_Http::BAD_REQUEST ]
			);
		}

		return true;
	}

	/**
	 * Get full namespace
	 *
	 *
	 * @return string
	 */
	public function get_namespace() {
		return $this->namespace . '/' . $this->version;
	}
}
