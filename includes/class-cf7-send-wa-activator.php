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
	public static function activate( $network_wide ) {
	    if ( is_multisite() && $network_wide ) {
		    global $wpdb;
	        // Get all blogs in the network and activate plugin on each one
	        $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
	        foreach ( $blog_ids as $blog_id ) {
	            switch_to_blog( $blog_id );
				self::do_activation();		
	            restore_current_blog();
	        }
	    } else {
			self::do_activation();		
	    }
	}
    
    public static function do_activation() {
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
        $current_db_version = '1.0.0';
        $db_version = get_option( 'cf7sendwa_database_version' );             
        if( $db_version == '' ) {
	        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');        
            self::setup_database();
            update_option( 'cf7sendwa_database_version', $current_db_version );    
        }
    }    
    
    public static function setup_database() {
	    
        global $wpdb;
        
        $sql  = "";        
        
        $sql .= "CREATE TABLE `{$wpdb->prefix}cf7wa_contacts` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `first_name` varchar(50) COLLATE {$wpdb->collate} NOT NULL,
              `last_name` varchar(50) COLLATE {$wpdb->collate} DEFAULT NULL,
              `organization` varchar(200) COLLATE {$wpdb->collate} DEFAULT NULL,
              `email` varchar(200) COLLATE {$wpdb->collate} DEFAULT NULL,
              `wp_user_id` bigint(20) DEFAULT NULL,
              `whatsapp` varchar(15) COLLATE {$wpdb->collate} DEFAULT NULL,
              `instagram` varchar(100) COLLATE {$wpdb->collate} DEFAULT NULL,
              `twitter` varchar(100) COLLATE {$wpdb->collate} DEFAULT NULL,
              `facebook` varchar(100) COLLATE {$wpdb->collate} DEFAULT NULL,
              `linkedin` varchar(100) COLLATE {$wpdb->collate} DEFAULT NULL,
              `last_update` datetime DEFAULT NULL,
              PRIMARY KEY (`id`) 
            ) ENGINE=InnoDB COLLATE={$wpdb->collate}; ";
        
        $sql .= "CREATE TABLE `{$wpdb->prefix}cf7wa_contact_messages` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `cf7form_id` bigint(20) NOT NULL,
              `contact_id` bigint(20) DEFAULT NULL,
              `msg_date` datetime DEFAULT NULL,
              `msg_subject` varchar(150) COLLATE {$wpdb->collate} DEFAULT NULL,
              `msg_text` text COLLATE {$wpdb->collate},
              `dtm_read` datetime DEFAULT NULL,
              `dtm_followup` datetime DEFAULT NULL,
              `followup_by` bigint(20) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB COLLATE={$wpdb->collate}; ";
        
        dbDelta( $sql );    
        
	}
    
}