<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://solusipress.com
 * @since             0.3.0
 * @package           Cf7_Send_Wa
 *
 * @wordpress-plugin
 * Plugin Name:       Contact Form 7 Send WhatsApp
 * Plugin URI:        https://solusipress.com/download/cf7-send-wa
 * Description:       Send contact form 7 input into WhatsApp message.
 * Version:           0.13.4
 * Author:            Yerie Piscesa
 * Author URI:        https://solusipress.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cf7-send-wa
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 0.3.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CF7_SEND_WA_VERSION', '0.13.4' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cf7-send-wa-activator.php
 */
function activate_cf7_send_wa() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cf7-send-wa-activator.php';
	Cf7_Send_Wa_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cf7-send-wa-deactivator.php
 */
function deactivate_cf7_send_wa() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cf7-send-wa-deactivator.php';
	Cf7_Send_Wa_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cf7_send_wa' );
register_deactivation_hook( __FILE__, 'deactivate_cf7_send_wa' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cf7-send-wa.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.3.0
 */
function run_cf7_send_wa() {

	$plugin = new Cf7_Send_Wa();
	$plugin->run();

}
run_cf7_send_wa();