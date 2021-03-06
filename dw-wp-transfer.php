<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.dasdware.de
 * @since             1.0.0
 * @package           DW_WP_Transfer
 *
 * @wordpress-plugin
 * Plugin Name:       dasd.ware WordPress Transfer
 * Plugin URI:        http://www.dasdware.de
 * Description:       Data Transfer for Wordpress. Transfers data in json format and keeps integrity of foreign keys between post types.
 * Version:           1.0.0
 * Author:            dasd.ware
 * Author URI:        http://www.dasdware.de
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       dw-wp-transfer
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'DW_WP_TRANSFER_VERSION', '1.0.0' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-dw-wp-transfer.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_dw_wp_transfer() {
	ob_start();
	$plugin = new DW_WP_Transfer();
	$plugin->run();
}
run_dw_wp_transfer();
