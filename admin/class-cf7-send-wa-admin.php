<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://solusipress.com
 * @since      0.3.0
 *
 * @package    Cf7_Send_Wa
 * @subpackage Cf7_Send_Wa/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cf7_Send_Wa
 * @subpackage Cf7_Send_Wa/admin
 * @author     Yerie Piscesa <yerie@solusipress.com>
 */
class Cf7_Send_Wa_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.3.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.3.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.3.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    0.3.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cf7_Send_Wa_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cf7_Send_Wa_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cf7-send-wa-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    0.3.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cf7_Send_Wa_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cf7_Send_Wa_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cf7-send-wa-admin.js', array( 'jquery' ), $this->version, false );

	}
    
	public function create_menu() {
		add_submenu_page( 'wpcf7', 'WhatsApp Send', 'WhatsApp Send', 'manage_options', 'sp-cf7-send-wa', array( $this, 'wa_settings' ) );
    }    
    
    public function cf7_extended_shortcode( $shortcode, $args, $cf7 ) {
        $shortcode = str_replace( 'contact-form-7', 'contact-form-7-wa', $shortcode );
        return $shortcode;
    }

    public function wa_settings() {
        
        $settings_saved = false;
        if( isset( $_POST ) && !empty( $_POST ) ) {
            $settings_saved = true;
            update_option( 'cf7sendwa_number', $_POST['whatsapp_number'] );
            $disable_mail = $_POST['disable_send_mail'];
            if( $disable_mail != '1' ) $disable_mail = '0';
            update_option( 'cf7sendwa_disablemail', $disable_mail );

            $use_twilio = $_POST['use_twilio'];
            if( $use_twilio != '1' ) $use_twilio = '0';
            update_option( 'cf7sendwa_use_twilio', $_POST['use_twilio'] );
            update_option( 'cf7sendwa_twilio_sid', $_POST['twilio_sid'] );
            update_option( 'cf7sendwa_twilio_token', $_POST['twilio_token'] );
            update_option( 'cf7sendwa_twilio_from', $_POST['twilio_from'] );
        }
        $whatsapp_number = get_option( 'cf7sendwa_number', '628123456789' );
        $disable_mail = get_option( 'cf7sendwa_disablemail', '0' );
        
        $use_twilio = get_option( 'cf7sendwa_use_twilio', '0' );
        $twilio_sid = get_option( 'cf7sendwa_twilio_sid', '' );
        $twilio_token = get_option( 'cf7sendwa_twilio_token', '' );
        $twilio_from = get_option( 'cf7sendwa_twilio_from', '14155238886' );
        
        include 'partials/cf7-send-wa-admin-display.php';
        
    }
}
