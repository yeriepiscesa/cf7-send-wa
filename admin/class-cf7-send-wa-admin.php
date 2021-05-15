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
		wp_register_style( 'select2', plugin_dir_url( dirname( __FILE__ ) ) . 'includes/assets/css/select2.min.css' );

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
		wp_register_script( 'knockout', plugin_dir_url( dirname( __FILE__ ) ) . 'includes/assets/js/knockout.js' );
		wp_register_script( 'select2', plugin_dir_url( dirname( __FILE__ ) ) . 'includes/assets/js/select2.min.js' );

	}
    
	public function create_menu() {
		add_submenu_page( 'wpcf7', 'WhatsApp Send', 'WhatsApp Send', 'manage_options', 'sp-cf7-send-wa', array( $this, 'wa_settings' ) );
    }    
    
    public function cf7_extended_shortcode( $shortcode, $args, $cf7 ) {
        $shortcode = str_replace( 'contact-form-7', 'contact-form-7-wa', $shortcode );
        return $shortcode;
    }

    public function wa_settings() {
	    
	    wp_enqueue_style( 'select2' );
	    wp_enqueue_script( 'knockout' );
	    wp_enqueue_script( 'select2' );
        
        $settings_saved = false;
        if( isset( $_POST ) && !empty( $_POST ) ) {
            $settings_saved = true;
            update_option( 'cf7sendwa_number', $_POST['whatsapp_number'] );
            $disable_mail = '0';
            if( isset( $_POST['disable_send_mail'] ) ) {
	        	$disable_mail = $_POST['disable_send_mail'];    
				if( $disable_mail != '1' ) $disable_mail = '0';
            }
            update_option( 'cf7sendwa_disablemail', $disable_mail );
			update_option( 'cf7sendwa_country', $_POST['default_country'] );	
            
            $_global_form = '';
            if( isset( $_POST['cf7sendwa_global_form'] ) ) {
	            $_global_form = $_POST['cf7sendwa_global_form'];
            } 
	        update_option( 'cf7sendwa_global_form', $_global_form );

			$_global_position = '';
            if( isset( $_POST['cf7sendwa_global_position'] ) ) {
	            $_global_position = $_POST['cf7sendwa_global_position'];
	        }
	        update_option( 'cf7sendwa_global_position', $_global_position );

            update_option( 'cf7sendwa_global_tooltip', $_POST['cf7sendwa_global_tooltip'] );
            
            $cf7sendwa_fontawesome = '0';
            if( isset( $_POST['cf7sendwa_fontawesome'] ) ) {
	        	$cf7sendwa_fontawesome = $_POST['cf7sendwa_fontawesome'];    
				if( $cf7sendwa_fontawesome != '1' ) $cf7sendwa_fontawesome = '0';
            }
            update_option( 'cf7sendwa_fontawesome', $cf7sendwa_fontawesome );
            
            $cf7sendwa_channel = '';
            if( isset( $_POST['cf7sendwa_channel'] ) ) {
            	$cf7sendwa_channel = $_POST['cf7sendwa_channel'];
            }
	        update_option( 'cf7sendwa_channel', $cf7sendwa_channel );
            
            $_woo_checkout = '';
            if( isset( $_POST['woo_checkout'] ) ) {
	            $_woo_checkout = $_POST['woo_checkout'];
            }
            update_option( 'cf7sendwa_woo_checkout', $_woo_checkout );
            
            $_woo_single_product = '';
            if( isset( $_POST['woo_single_product'] ) ) {
	            $_woo_single_product = $_POST['woo_single_product'];
            }
            update_option( 'cf7sendwa_woo_single_product', $_woo_single_product ); 
            update_option( 'cf7sendwa_single_button', $_POST['single_button'] );         
            
            $single_button_wrap_div = '0';
            if( isset( $_POST['single_button_wrap_div'] ) ) {
	        	$single_button_wrap_div = $_POST['single_button_wrap_div'];    
				if( $single_button_wrap_div != '1' ) $single_button_wrap_div = '0';
            }
            update_option( 'cf7sendwa_single_button_wrap_div', $single_button_wrap_div );
            
            update_option( 'cf7sendwa_single_product_greet', $_POST['single_product_greet'] );  
            
 			if( isset( $_POST['cf7sendwa_single_hook'] ) ) {
	 			$button_hook = $_POST['cf7sendwa_single_hook'];
	 			update_option( 'cf7sendwa_single_button_hook', $button_hook );
 			}
            
            $cf7sendwa_remove_add_to_cart_loop = '0';
            if( isset( $_POST['cf7sendwa_remove_add_to_cart_loop'] ) ) {
	        	$cf7sendwa_remove_add_to_cart_loop = $_POST['cf7sendwa_remove_add_to_cart_loop'];    
				if( $cf7sendwa_remove_add_to_cart_loop != '1' ) $cf7sendwa_remove_add_to_cart_loop = '0';
            }
            update_option( 'cf7sendwa_remove_add_to_cart_loop', $cf7sendwa_remove_add_to_cart_loop );
            
            update_option( 'cf7sendwa_woo_order_redirect', $_POST['woo_order_redirect'] );

            $full_cart = '0'; 
            if( isset( $_POST['full_cart'] ) ) {
	            $full_cart = $_POST['full_cart'];
				if( $full_cart != '1' ) $full_cart = '0';
            }
            update_option( 'cf7sendwa_fullcart', $full_cart );
			
            $require_shipping = '0';
            if( isset( $_POST['require_shipping'] ) ) {
	        	$require_shipping = $_POST['require_shipping'];    
				if( $require_shipping != '1' ) $require_shipping = '0';
            }
            update_option( 'cf7sendwa_requireshipping', $require_shipping );

            $quickshop_excerpt = '0';
            if( isset( $_POST['quickshop_excerpt'] ) ) {
	        	$quickshop_excerpt = $_POST['quickshop_excerpt'];    
				if( $quickshop_excerpt != '1' ) $quickshop_excerpt = '0';
            }
            update_option( 'quickshop_excerpt', $quickshop_excerpt );

            $quickshop_sku = '0';
            if( isset( $_POST['quickshop_sku'] ) ) {
	        	$quickshop_sku = $_POST['quickshop_sku'];    
				if( $quickshop_sku != '1' ) $quickshop_sku = '0';
            }
            update_option( 'quickshop_sku', $quickshop_sku );

            $quickshop_outofstock = '0';
            if( isset( $_POST['quickshop_outofstock'] ) ) {
	        	$quickshop_outofstock = $_POST['quickshop_outofstock'];    
				if( $quickshop_outofstock != '1' ) $quickshop_outofstock = '0';
            }
            update_option( 'quickshop_outofstock', $quickshop_outofstock );

            update_option( 'cf7sendwa_provider', $_POST['provider'] );
            update_option( 'cf7sendwa_twilio_sid', $_POST['twilio_sid'] );
            update_option( 'cf7sendwa_twilio_token', $_POST['twilio_token'] );
            update_option( 'cf7sendwa_twilio_from', $_POST['twilio_from'] );
            update_option( 'cf7sendwa_fonnte_token', $_POST['fonnte_token'] );
            update_option( 'cf7sendwa_wablas_token', $_POST['wablas_token'] );
            update_option( 'cf7sendwa_wablas_domain', $_POST['wablas_domain'] );
            update_option( 'cf7sendwa_ruangwa_token', $_POST['ruangwa_token'] );
            
            do_action( 'cf7sendwa_custom_settings_save' );
        }
        
        $woo_single_product = get_option( 'cf7sendwa_woo_single_product', '' );
        $woo_button_hook = get_option( 'cf7sendwa_single_button_hook', '' );
        
	    wp_localize_script( $this->plugin_name, 'cf7sendwa', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
	        'security' => wp_create_nonce( 'cf7sendwa-settings' ),
	        'provider' => get_option( 'cf7sendwa_provider', '' ),
	        'single_product' => $woo_single_product,
	    ) );
	    wp_enqueue_script( 'underscore' );
	    wp_enqueue_script( $this->plugin_name );
        
        $whatsapp_number = get_option( 'cf7sendwa_number', '628123456789' );
		$default_country = get_option( 'cf7sendwa_country' );        
		$cf7sendwa_global_tooltip = get_option( 'cf7sendwa_global_tooltip', '' );
        $disable_mail = get_option( 'cf7sendwa_disablemail', '0' );
        $cf7sendwa_fontawesome = get_option( 'cf7sendwa_fontawesome', '0' );
        $cf7sendwa_channel = get_option( 'cf7sendwa_channel', '' );
        
        $full_cart = get_option( 'cf7sendwa_fullcart', '0' );
        $require_shipping = get_option( 'cf7sendwa_requireshipping', '0' );

        $quickshop_excerpt = get_option( 'quickshop_excerpt', '0' );
        $quickshop_sku = get_option( 'quickshop_sku', '0' );
        $quickshop_outofstock = get_option( 'quickshop_outofstock', '0' );
        
        $woo_checkout = get_option( 'cf7sendwa_woo_checkout', '' );
        $single_button = get_option( 'cf7sendwa_single_button', '' );
        $single_button_wrap_div = get_option( 'cf7sendwa_single_button_wrap_div', '0' );
        
        $single_product_greet = get_option( 'cf7sendwa_single_product_greet', '' );
        $woo_order_redirect = get_option( 'cf7sendwa_woo_order_redirect', '' );
        
        $cf7sendwa_remove_add_to_cart_loop = get_option( 'cf7sendwa_remove_add_to_cart_loop', '0' );
        
        $twilio_sid = get_option( 'cf7sendwa_twilio_sid', '' );
        $twilio_token = get_option( 'cf7sendwa_twilio_token', '' );
        $twilio_from = get_option( 'cf7sendwa_twilio_from', '14155238886' );

        $fonnte_token = get_option( 'cf7sendwa_fonnte_token', '' );
        
        $wablas_token = get_option( 'cf7sendwa_wablas_token', '' );
        $wablas_domain = get_option( 'cf7sendwa_wablas_domain', '' );

        $ruangwa_token = get_option( 'cf7sendwa_ruangwa_token', '' );

        include 'partials/cf7-send-wa-admin-display.php';        
    }
    
    /**
	 * Contact Forms Lookup
	 * @since 0.6.0
	 * @access public
	 *
	 */	   
    public function contact_forms_lookup() {
	    
        check_ajax_referer( 'cf7sendwa-settings', 'security' );
		$args = array(
			'post_type' => 'wpcf7_contact_form',
			'post_status' => 'publish',
			'posts_per_page' => -1,
 			'orderby' => 'title',
			'order' => 'ASC',
		);   
		if( $_POST['search'] != '' ) {
			$args['cf7sendwa_post_title_like'] = $_POST['search'];
		}
		$query = new WP_Query( $args );
		$data = array();
		if( $query->have_posts() ) {
			while( $query->have_posts() ) { $query->the_post();
				$row = array(
					'id' => get_the_ID(),
					'text' => get_the_title()
				);
				array_push($data, $row);								
			}
			wp_reset_postdata();
		}
		$results = array(
			'results' => $data
		);
		
		echo json_encode( $results );
	    wp_die();
    }
    
    /**
	 * Custom validation on mail sending disabled
	 * @since 0.11.4
	 * @access public
	 *
	 */	   
    public function config_custom_validation( $contact_form ) {	   
	    $disable_mail = get_option( 'cf7sendwa_disablemail', '0' );
	    if( is_admin() && $disable_mail == '1' ) {
		    $config_errors = get_post_meta( $contact_form->id(), '_config_errors', true );
		    if( is_array( $config_errors ) && !empty( $config_errors ) ) {
				$updated = false;
				foreach( $config_errors as $key=>$config ) {
					if( strpos( $key, 'mail.' ) !== false ) {
						unset( $config_errors[$key] );
						$updated = true;
					}
				}
				if( $updated ) {
					update_post_meta( $contact_form->id(), '_config_errors', $config_errors );
				}
		    }
	    }
    }
    
    /**
	 * WhatsApp setting panel
	 * @since 0.9.0
	 * @access public
	 *
	 */	   
    public function whatsapp_panel( $panels ) {

		$panels['cf7sendwa-settings-panel'] = array(
			'title' => __( 'WhatsApp', 'cf7sendwa' ),
			'callback' => array( $this, 'wpcf7_editor_panel_whatsapp' ),
		);
		
		$order = array( 'form-panel','mail-panel','cf7sendwa-settings-panel' );
		$new_order = [];		
		
		foreach( $order as $panel ) {
			$new_order[ $panel ] = $panels[ $panel ];
		}
		foreach( $panels as $tab=>$panel ) {
			if( !isset( $new_order[ $tab ] ) ) {
				$new_order[ $tab ] = $panel;
			}	
		}
		$panels = $new_order;
		
	    $disable_mail = get_option( 'cf7sendwa_disablemail', '0' );
		if( $disable_mail == '1' ) {
			unset( $panels['mail-panel'] );
		}
		
	    return $panels;
	    
    }
    
    /**
	 * WhatsApp setting panel callback
	 * @since 0.9.0
	 * @access public
	 *
	 */	   
    public function wpcf7_editor_panel_whatsapp( $post ) {
	    
		$this->editor_box_whatsapp( $post );
	
		echo '<br class="clear" />';
	
		$this->editor_box_whatsapp( $post, array(
			'id' => 'wpcf7-whatsapp-2',
			'name' => 'whatsapp_2',
			'title' => __( 'WhatsApp Autorespond', 'cf7sendwa' ),
			'use' => __( 'Use WhatsApp Autorespond', 'cf7sendwa' ),
		) );
    }
    private function editor_box_whatsapp( $post, $args='' ) {
	    $whatsapp_number = get_option( 'cf7sendwa_number', '628123456789' );
		$args = wp_parse_args( $args, array(
			'id' => 'wpcf7-whatsapp',
			'name' => 'whatsapp',
			'title' => __( 'WhatsApp Send', 'cf7sendwa' ),
			'use' => null,
		) );
		$id = esc_attr( $args['id'] );
		$whatsapp = wp_parse_args( $post->prop( $args['name'] ), array(
			'active' => false,
			'recipient' => '',
			'body' => '',
			'attachments' => '',
		) );
	    include 'partials/cf7-send-wa-form-settings.php';
    }
    public function cf7_form_properties( $properties, $cf7 ) {
		$properties = wp_parse_args( $properties, array(
			'whatsapp' => array(),
			'whatsapp_2' => array(),
		) );	    
	    return $properties;
    }
    public function wpcf7_collect_mail_tags_for_wa( $mailtags, $args, $contact_form ){
	    $mailtags[] = 'woo-orderdetail';
	    return $mailtags;
    }
    
    /**
	 * Save contact form custom settings
	 * @since 0.9.0
	 * @access public
	 *
	 */	   
    public function save_contact_form_settings( $contact_form, $args, $context ) {
		$args['whatsapp'] = isset( $_POST['wpcf7-whatsapp'] )
			? $_POST['wpcf7-whatsapp'] : array();

		$args['whatsapp_2'] = isset( $_POST['wpcf7-whatsapp-2'] )
			? $_POST['wpcf7-whatsapp-2'] : array();
			
	    $args = wp_unslash( $args );
		$properties = array();		
		if ( null !== $args['whatsapp'] ) {
			$properties['whatsapp'] = $args['whatsapp'];
			$properties['whatsapp']['active'] = true;
		}
		if ( null !== $args['whatsapp_2'] ) {
			$properties['whatsapp_2'] = $args['whatsapp_2'];
		}
	    $contact_form->set_properties( $properties );
		if ( 'save' == $context ) {
			$contact_form->save();
		}
    }
    
    public function add_custom_field_tags() {
		remove_action( 'wpcf7_init', 'wpcf7_add_form_tag_select', 10, 0 );
		add_action( 'wpcf7_init', [ $this, 'wpcf7_add_form_tag_select' ], 20, 0 );
		add_filter( 'wpcf7_validate_select_channel', 'wpcf7_select_validation_filter', 10, 2 );
		add_filter( 'wpcf7_validate_select_channel*', 'wpcf7_select_validation_filter', 10, 2 );
    }
    
	public function wpcf7_add_form_tag_select() {
		wpcf7_add_form_tag( array( 'select', 'select*', 'select_channel', 'select_channel*' ),
			'wpcf7_select_form_tag_handler',
			array(
				'name-attr' => true,
				'selectable-values' => true,
			)
		);
	}
    
}
