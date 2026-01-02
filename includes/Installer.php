<?php
/**
 * Class Installer
 *
 *
 * @package  MCoder\EPS
 */

namespace MCoder\EPS;

/**
 * Class Installer
 */
class Installer {
    	/**
	 * Run the installer
	 *
	 *
	 * @return void
	 */
	public function run() {
		$this->add_version();
		$this->create_tables();
		$this->maybe_upgrade_tables();
    }
    
    /**
	 * Add time and version on DB
	 *
	 *
	 * @return void
	 */
	public function add_version() {
		$installed = get_option( 'mc_eps_installed' );

		if ( ! $installed ) {
			update_option( 'mc_eps_installed', time() );
		}

		update_option( dc_eps()->get_db_version_key(), EPS_VERSION );
	}

	/**
     * Create necessary database tables
     *
     * @return void
     */
    public function create_tables() {
        global $wpdb;
 
        $charset_collate = $wpdb->get_charset_collate();

        $schema = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}eps_token` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `order_id` bigint(20) unsigned NOT NULL,
          `token` text DEFAULT NULL,
          `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
		) $charset_collate";
		
		$schema2 = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}eps_transections` (
			`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			`order_id` text DEFAULT NULL,
			`amount` text DEFAULT NULL,
			`customer_account` text DEFAULT NULL,
			`transection_id` text DEFAULT NULL,
			`response_description` text DEFAULT NULL,
			`created_at` datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`)
		  ) $charset_collate";
		  $schema3 = "CREATE TABLE {$wpdb->prefix}eps_sandbox_transections (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            order_id text DEFAULT NULL,
            amount text DEFAULT NULL,
            customer_account text DEFAULT NULL,
            transection_id text DEFAULT NULL,
            response_description text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            product_status varchar(20) DEFAULT 'Pending',
            is_sync tinyint(1) DEFAULT 0,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate";

         
        if ( ! function_exists( 'dbDelta' ) ) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

		dbDelta( $schema );
		dbDelta( $schema2 );
		dbDelta( $schema3 );
		
    }
    public function maybe_upgrade_tables() {
        global $wpdb;
    
        $table = $wpdb->prefix . 'eps_transections';

        // Check if column exists
        $column_exists = $wpdb->get_results("SHOW COLUMNS FROM `$table` LIKE 'product_status'");
        $column_exists_sync = $wpdb->get_results("SHOW COLUMNS FROM `$table` LIKE 'is_sync'");
        $column_exists_updated_at = $wpdb->get_results("SHOW COLUMNS FROM `$table` LIKE 'updated_at'");
        if (empty($column_exists)) {
            $wpdb->query(
                "ALTER TABLE `$table` 
                 ADD `product_status` varchar(20) DEFAULT 'Pending'"
            );
        }
        if (empty($column_exists_sync)) {
            $wpdb->query(
                "ALTER TABLE `$table` 
                 ADD `is_sync` boolean DEFAULT 0"
            );
        }
        if (empty($column_exists_updated_at)) {
            $wpdb->query(
                "ALTER TABLE `$table`
                 ADD `updated_at` TIMESTAMP NULL
                 DEFAULT CURRENT_TIMESTAMP
                 ON UPDATE CURRENT_TIMESTAMP"
            );
        }
        
    }

}