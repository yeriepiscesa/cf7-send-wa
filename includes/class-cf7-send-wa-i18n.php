<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://solusipress.com
 * @since      0.3.0
 *
 * @package    Cf7_Send_Wa
 * @subpackage Cf7_Send_Wa/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      0.3.0
 * @package    Cf7_Send_Wa
 * @subpackage Cf7_Send_Wa/includes
 * @author     Yerie Piscesa <yerie@solusipress.com>
 */
class Cf7_Send_Wa_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.3.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'cf7-send-wa',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
