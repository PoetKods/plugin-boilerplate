<?php
/**
 * PK
 *
 * @package   pk
 * @author    PoetKods <david@poetkods.com>
 * @copyright 2023 PK
 * @license   MIT
 * @link      https://poetkods.com
 *
 * Plugin Name:     PK
 * Plugin URI:      https://poetkods.com
 * Description:     Poet Kods Plugin Core Boilerplate
 * Version:         1.0.0
 * Author:          PoetKods
 * Author URI:      https://poetkods.com
 * Text Domain:     pk
 * Domain Path:     /languages
 * Requires PHP:    7.1
 * Requires WP:     5.5.0
 * Namespace:       Pk
 */

declare( strict_types = 1 );

/**
 * Define the default root file of the plugin
 *
 * @since 1.0.0
 */
const PK_PLUGIN_FILE = __FILE__;

/**
 * Load PSR4 autoloader
 *
 * @since 1.0.0
 */
$pk_autoloader = require plugin_dir_path( PK_PLUGIN_FILE ) . 'vendor/autoload.php';

/**
 * Setup hooks (activation, deactivation, uninstall)
 *
 * @since 1.0.0
 */
register_activation_hook( __FILE__, [ 'Pk\Config\Setup', 'activation' ] );
register_deactivation_hook( __FILE__, [ 'Pk\Config\Setup', 'deactivation' ] );
register_uninstall_hook( __FILE__, [ 'Pk\Config\Setup', 'uninstall' ] );

/**
 * Bootstrap the plugin
 *
 * @since 1.0.0
 */
if ( ! class_exists( '\Pk\Bootstrap' ) ) {
	wp_die( __( 'PK is unable to find the Bootstrap class.', 'pk' ) );
}
add_action(
	'plugins_loaded',
	static function () use ( $pk_autoloader ) {
		/**
		 * @see \Pk\Bootstrap
		 */
		try {
			new \Pk\Bootstrap( $pk_autoloader );
		} catch ( Exception $e ) {
			wp_die( __( 'PK is unable to run the Bootstrap class.', 'pk' ) );
		}
	}
);

/**
 * Create a main function for external uses
 *
 * @return \Pk\Common\Functions
 * @since 1.0.0
 */
function pk(): \Pk\Common\Functions {
	return new \Pk\Common\Functions();
}
