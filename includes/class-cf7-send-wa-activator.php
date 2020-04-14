<?php

/**
 * Fired during plugin activation
 *
 * @link       https://solusipress.com
 * @since      0.3.0
 *
 * @package    Cf7_Send_Wa
 * @subpackage Cf7_Send_Wa/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.3.0
 * @package    Cf7_Send_Wa
 * @subpackage Cf7_Send_Wa/includes
 * @author     Yerie Piscesa <yerie@solusipress.com>
 */
class Cf7_Send_Wa_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    0.3.0
	 */
	public static function activate() {
        $upload = wp_upload_dir();
        $upload_dir = $upload['basedir'];
        $upload_dir = $upload_dir . '/cf7sendwa';
        if ( ! is_dir( $upload_dir ) ) {
            mkdir( $upload_dir, 0755 );   
        }
	    
	    $index = $upload_dir . '/index.php';
        if( !file_exists( $index ) ) {
        	$content = '<?php //silence is golden';
        	file_put_contents( $index, $content );
        }
	}

}
