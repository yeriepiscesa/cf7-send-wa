<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://solusipress.com
 * @since      0.3.0
 *
 * @package    Cf7_Send_Wa
 * @subpackage Cf7_Send_Wa/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Cf7_Send_Wa
 * @subpackage Cf7_Send_Wa/public
 * @author     Yerie Piscesa <yerie@solusipress.com>
 */
class Cf7_Send_Wa_Public {

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
    
	protected $ids = array();
	protected $script_loaded = false;
	protected $numbers = array();
	protected $resends = array();
	protected $global_form = null;
	protected $global_btn_tooltip = '';
	protected $load_fontawesome = false;

    public $wa_submitted_text = '';
    public $wa_replaced_tags;
    protected $provider = '';

    protected $twilio_sid = null;
    protected $twilio_token = null;

    protected $fonnte_token = null;
    
    protected $wablas_domain = null;
    protected $wablas_token = null;

    protected $ruangwa_token = null;
    
    public $instance_count = 0;
    
    public $woo_is_active = false;
    public $woo_cart = null;
    
    public $quickshop_rendered = false;
    public $current_product_checkout = false;
    
    protected $woo_shippings = null;
    protected $woo_order_id = null;
    protected $woo_order = false;
    protected $woo_cart_empty = false;
    protected $woo_order_links = array(
	    'received' => null,
	    'payment' => null
    );
    
    protected $attachments = array();
    protected $attachment_types = array();
    protected $attachment_ids = array();
    protected $attachment_type = null;

	/* WooCommerce customer */
	protected $customer = null;
	
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.3.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_shortcode( 'contact-form-7-wa', array( $this, 'render_contact_form' ) );
		add_shortcode( 'cf7sendwa-received-link', array( $this, 'render_woo_received_link' ) );
		add_shortcode( 'cf7sendwa-payment-link', array( $this, 'render_woo_payment_link' ) );
		add_shortcode( 'cf7sendwa-skip-empty', array( $this, 'render_skip_empty' ) );
        
        $this->provider = get_option( 'cf7sendwa_provider', '' );
        switch( $this->provider ) {
	        case 'twilio':
	            $this->twilio_sid = get_option( 'cf7sendwa_twilio_sid', '' );
	            $this->twilio_token = get_option( 'cf7sendwa_twilio_token', '' );
	        	break;
	        case 'fonnte':
				$this->fonnte_token = get_option( 'cf7sendwa_fonnte_token', '' );     
	        	break;
	        case 'wablas':
				$this->wablas_domain = get_option( 'cf7sendwa_wablas_domain', '' );     
				$this->wablas_token = get_option( 'cf7sendwa_wablas_token', '' );     
	        	break;
	        case 'ruangwa':
				$this->ruangwa_token = get_option( 'cf7sendwa_ruangwa_token', '' );     
	        	break;
        }        
        $this->global_form = get_option( 'cf7sendwa_global_form', '' );
        $this->global_btn_tooltip = get_option( 'cf7sendwa_global_tooltip', '' );
        if(  $this->global_btn_tooltip == '' ) {
	        $this->global_btn_tooltip = 'Click to chat';
        }
		$fa_load = get_option( 'cf7sendwa_fontawesome', '0' );
		if( $fa_load == '1' ) {
			$this->load_fontawesome = true;
        }
        if( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	        $this->woo_is_active = true;
	        // REST API CALL ( Next Features )
	        /* 
			add_action( 'rest_api_init', function () {
			    register_rest_route( 'cf7sendwa/v1', '/products', array(
			        'method' => 'GET',
			        'callback' => array( $this, 'api_list_product' )
			    ) );
			} );
	        */
        }

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.3.0
	 */
	public function enqueue_styles() {
		wp_register_style( 'unsemantic-grid', plugin_dir_url( __FILE__ ) . 'css/unsemantic.grid.css' );
		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cf7-send-wa-public.css', array(), $this->version, 'all' );
		wp_register_style( 'jquery-modal', plugin_dir_url( dirname( __FILE__ ) ) . 'includes/assets/css/jquery.modal.min.css' );
		wp_register_style( 'select2', plugin_dir_url( dirname( __FILE__ ) ) . 'includes/assets/css/select2.min.css' );
		wp_register_style( 'fotorama', plugin_dir_url( dirname( __FILE__ ) ) . 'includes/assets/css/fotorama.min.css' );
		wp_register_style( 'tooltipster', plugin_dir_url( dirname( __FILE__ ) ) . 'includes/assets/css/tooltipster.bundle.min.css', array(), '4.2.8' );		
		if( $this->global_form != '' ) {
			wp_enqueue_style( 'tooltipster' );
		}
		if( $this->load_fontawesome ) {
			wp_register_style( 'fontawesome', 'https://use.fontawesome.com/releases/v5.13.0/css/all.css', array(), '5.13.0' );
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    0.3.0
	 */
	public function enqueue_scripts() {
		wp_register_script( 'cf7sendwa-commonlib', plugin_dir_url( dirname( __FILE__ ) ) . 'includes/assets/js/commonlib.js', array( 'jquery' ) );
		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cf7-send-wa-public.js', array( 'jquery' ), $this->version, true );
		wp_register_script( 'jquery-modal', plugin_dir_url( dirname( __FILE__ ) ) . 'includes/assets/js/jquery.modal.min.js', array( 'jquery' ), '0.9.1', false );
		wp_register_script( 'knockout', plugin_dir_url( dirname( __FILE__ ) ) . 'includes/assets/js/knockout.js' );
		wp_register_script( 'select2', plugin_dir_url( dirname( __FILE__ ) ) . 'includes/assets/js/select2.min.js' );
		wp_register_script( 'fotorama', plugin_dir_url( dirname( __FILE__ ) ) . 'includes/assets/js/fotorama.min.js' );
		wp_register_script( 'sticky', plugin_dir_url( dirname( __FILE__ ) ) . 'includes/assets/js/jquery.sticky.js' );
		wp_register_script( 'tooltipster', plugin_dir_url( dirname( __FILE__ ) ) . 'includes/assets/js/tooltipster.bundle.min.js', array( 'jquery' ), '4.2.8' );
		if( $this->global_form != '' ) {
			wp_enqueue_script( 'tooltipster' );
		}
	}
	
    public function check_skip_mail( $skip_mail, $contact_form ) {
        if( get_option( 'cf7sendwa_disablemail', '0' ) == '1' ) {
            $skip_mail = true;
        }
	    $woo = get_option( 'cf7sendwa_woo_checkout', '' );
        if( $contact_form->id() == $woo ) {
	        $skip_mail = true;
        }
        return $skip_mail;
    }
    
    public function filter_wpcf7_load_channel( $scanned_tag, $this_exec ) {
	    if( $scanned_tag['type'] == 'select_channel' || $scanned_tag['type'] == 'select_channel*' ) {
		    $channels = get_option( 'cf7sendwa_channel', '' );
		    if( $channels != '' ) {
			    $channels = json_decode( urldecode( $channels ), true );
			    foreach( $channels as $cnl ) {
				    $scanned_tag['values'][] = $cnl['number'];
				    $scanned_tag['labels'][] = $cnl['title'];
			    }
		    }
	    }
	    return $scanned_tag;
    }
    
    public $woo_popup_button = '';
	public function render_contact_form( $atts ) {
		
		$_atts = $atts;
		$atts = shortcode_atts( array(
			'popup' => 'false',
			'popupdelay' => '2',
			'buttontext' => 'Open Form',
			'buttonicon' => 'fab fa-whatsapp',	
			'buttonclass' => '',
			'style' => '',
			'class' => '',
			'modalwidth' => '',
		), $atts );
		$atts = array_merge( $atts, $_atts );
		
        $quickshop_unsemantic = get_option( 'quickshop_unsemantic', '0' );
		if( $quickshop_unsemantic == '0' ) {		    
	    	wp_enqueue_style( 'unsemantic-grid' );
	    }
		wp_enqueue_script( 'cf7sendwa-commonlib' );
        wp_enqueue_script( 'underscore' );
        if( $this->load_fontawesome ) {
	        wp_enqueue_style( 'fontawesome' );
        }
        
        $is_popup = false;
        if( $atts['popup'] == 'button' || $atts['popup'] == 'auto' ) {
	    	wp_enqueue_style( 'jquery-modal' );
	    	wp_enqueue_script( 'jquery-modal' );    
			$is_popup = true;
        }
        $shortcode = '[contact-form-7';
        if( isset( $atts['number'] ) && $atts['number'] != '' ) {
            $this->numbers[$atts['id']] = $atts['number'];
            unset( $atts['number'] );
        } else {
	        $wa = get_post_meta( $atts['id'], '_whatsapp', true );
	        if( $wa && $wa['recipient'] != '' ) {
		        $this->numbers[$atts['id']] = trim($wa['recipient']);
	        } else {
            	$this->numbers[$atts['id']] = get_option( 'cf7sendwa_number', '628123456789' );
            }
        }
        foreach( $atts as $key=>$val ) {
            $shortcode .= ' ' . $key .'="' . $val . '"';
        }
        $shortcode .= ']';
        
        $html = '';
		$selector = '#cf7sendwa-frm'.$atts['id'].'_'.$this->instance_count;
        if( $is_popup ) {
	        wp_enqueue_style( $this->plugin_name );
	        
	        $_style = ''; $_class = '';
	        if( trim( $atts['style'] ) != '' ) {
		        $_style = $atts['style'];
	        }
	        if( trim( $atts['class'] ) != '' ) {
		        $_class = ' '. $atts['class'];
	        }
	        $_width = '';
	        if( $atts['modalwidth'] != '' && is_numeric( $atts['modalwidth'] ) ) {
		        $_width = 'min-width: ' . $atts['modalwidth'] . 'px;';
	        }
	        $html .= '<div id="' . str_replace( '#', '', $selector) . '" class="modal'.$_class.'" style="'.$_width.'display:none;padding:0px;'.$_style.';">';
	        $html .= '<div class="cf7sendwa-form-content">';
	        if( isset( $atts['title'] ) && $atts['title'] != '' ) {
		        $html .= '<h3 class="cf7sendwa-modal-title">'.$atts['title'].'</h3>';
	        } else {
		        $__p = get_post( $atts['id'] );
		        $html .= '<h3 class="cf7sendwa-modal-title">' . $__p->post_title . '</h3>';
	        }
        }
        $html .= '<div class="cf7sendwa-cf7-container' . ( $is_popup?' form-popup':'' ) . '">'. do_shortcode( $shortcode ) . '</div>';
        if( $is_popup ) {
	        $html .= '</div></div>';
	        if( $atts['popup'] == 'auto' ) {
		        $_script = 'var _open = window.setInterval( function(){ $("'.$selector.'").modal(); window.clearInterval(_open); }, ' . $atts['popupdelay']*1000 . ' );';
		        $html .= '<script type="text/javascript">( function($){ $(document).ready(function(){ '.$_script.' }); } )(jQuery);</script>';
	        } else {
		        $_bclass = 'button';
		        if( $atts['buttonclass'] != '' ) {
			        $_bclass = $atts['buttonclass'];
		        }
		        $p_button = '';
		        $p_button = '<a href="'.$selector.'" rel="modal:open" class="'.$_bclass.'">';
		        if( $atts['buttonicon'] != '' ) {
		        	$p_button .= '<i class="'.$atts['buttonicon'].'"></i> ';
		        }
	        	$p_button .= $atts['buttontext'] .'</a>';
	        	
	        	$this->woo_popup_button = apply_filters( 'cf7sendwa_popup_button', $p_button, $selector, $atts );
	        	if( !isset( $atts['in_woo'] ) ) {
		        	$html .= $this->woo_popup_button;
	        	}
	        }
        }
        
        array_push( $this->ids, intval($atts['id']) );
        $_wa = get_post_meta( $atts['id'], '_whatsapp', true );
        if( $_wa && isset( $_wa['body'] ) && trim($_wa['body']) != '' ) {
	        $allow_resend = '0';
	        $resend_label = '';
	        if( isset( $_wa['allowresend'] ) ) {
		        $allow_resend = is_null( $_wa['allowresend'] ) ? '0' : $_wa['allowresend'];
	        }
	        if( isset( $_wa['resendlabel'] ) ) {
		    	$resend_label = is_null( $_wa['resendlabel'] ) ? '' : $_wa['resendlabel'];	    
	        }
	        $this->resends[ $atts['id'] ] = array(
	        	'allow' => $allow_resend,
	        	'label' => $resend_label
	        );
        }    
        $this->instance_count++;
        
        do_action( 'cf7sendwa_after_render_form', $this );
        
		return $html;
	}
	
	
    /*
	 * Evaluate autorespond message
	 * @since	0.9.0
	 * @access	public
	 */
	private function autorespond_message( $data, $req_post ) {
		$p = $req_post;
		$message = '';
		$wa = get_post_meta( $p['cf7_id'], '_whatsapp_2', true );
		$input_array = array();
		if( $wa && $wa['active'] == '1' ) {
			$message = trim( $wa['body'] );
			foreach( $p['cf7_inputs'] as $input ) {
				$message = str_replace( '['.$input['name'].']', $input['value'], $message );
				if( strpos( $input['name'], '[]' ) !== false ) {
					$_key = str_replace( '[]', '', $input['name'] );
					if( !isset( $input_array[ $_key ] ) ) {
						$input_array[ $_key ] = [];
					}
					array_push( $input_array[ $_key ], $input['value'] );
				}
			}
			if( !empty( $input_array ) ) {
				foreach( $input_array as $key=>$val ) {
					$message = str_replace( '['.$key.']', implode( ", ", $val ), $message );
				}
			}
			$message = str_replace( '[woo-orderdetail]', $p['woo_order_detail'], $message );
			if( $message != '' ) {
				if( isset($wa['recipient']) && $wa['recipient'] != '' ) {
					$data['to_number_2'] = $wa['recipient'];
					foreach( $p['cf7_inputs'] as $input ) {
						if( $data['to_number_2'] == '[' . $input['name'] . ']' ) {
							$data['to_number_2'] = $input['value'];
						}
					}
					$_tonumber = str_split( $data['to_number_2'] );
					if( $_tonumber[0] == '0' ) {
						unset($_tonumber[0]);
						$data['to_number_2'] = get_option( 'cf7sendwa_country', '62' ) . implode( '', $_tonumber );						
					}
				} else {
					$data['to_number_2'] = get_option( 'cf7sendwa_number', '' );					
				}
				$data['message_2'] = $message;		
			}
		}
		return $data;
	}
        
    /*
	 * Send message to Fonnte API
	 * @since	0.8.2
	 * @access	public
	 */
	private function _send_fonte( $inputs ){
        $url = 'https://fonnte.com/api/send_message.php';
        $curl = wp_remote_post( $url, array(
	    	'body' => $inputs,
	    	'headers' => array(
				'Authorization' => $this->fonnte_token
	    	)    
        ) );
        do_action( 'cf7sendwa_after_send_fonnte', $url, $inputs );
	}
    public function send_fonnte() {
        check_ajax_referer( 'cf7sendwa-api-action', 'security' );
		$inputs = [
		    'phone' => $_POST['to_number'],
		    'type' => 'text',
		    'text' => $_POST['message']
		];  
        $this->_send_fonte( $inputs );
        if( !empty( $_POST['attachments'] ) ) {
	        $inputs = [
	        	'phone' => $_POST['to_number'],
	        	'url' => $_POST['attachments'][0],
	        ];
			$mime_type = $_POST['attachment_type'];
			if( strpos( $mime_type, 'image' ) >= 0 ) {
				$type = 'image';
			} elseif( strpos( $mime_type, 'video' ) >= 0 ) {
				$type = 'video';
			} elseif( strpos( $mime_type, 'audio' ) >= 0 ) {
				$type = 'audio';
			} else {
				$type = 'file';
			}
	        $inputs['type'] = $type;
			$this->_send_fonte( $inputs );	        
        }
        
        /* autorespond */
		$data = $this->autorespond_message( array(), $_POST );
		if( !empty( $data ) ) {
			$inputs = array(
	            "phone" => $data['to_number_2'],
				'type' => 'text',
	            "text" => $data['message_2']
			);
	        $this->_send_fonte( $inputs );
		}
        
        wp_die();
	}
	
    /*
	 * Send message to Wablas API
	 * @since	0.8.3
	 * @access	public
	 */
	private function _send_wablas( $inputs, $action='send-message' ){
        $url = $this->wablas_domain . '/api/' . $action;
        $curl = wp_remote_post( $url, array(
	    	'body' => $inputs,
	    	'headers' => array(
				'Authorization' => $this->wablas_token
	    	)    
        ) );     
        do_action( 'cf7sendwa_after_send_wablas', $url, $inputs );
	}
	public function send_wablas() {
        check_ajax_referer( 'cf7sendwa-api-action', 'security' );
		$inputs = [
		    'phone' => $_POST['to_number'],
		    'message' => $_POST['message']
		];
        $this->_send_wablas( $inputs );
        if( !empty( $_POST['attachments'] ) ) {
	        $inputs = [
	        	'phone' => $_POST['to_number'],
	        	'caption' => null,
	        ];
			$mime_type = $_POST['attachment_type'];
			$action = '';
			if( strpos( $mime_type, 'image' ) >= 0 ) {
				$inputs['image'] = $_POST['attachments'][0];
				$action = 'send-image';
			} elseif( strpos( $mime_type, 'video' ) >= 0 ) {
				$action = 'send-video';
				$inputs['video'] = $_POST['attachments'][0];
			} else {
				$action = 'send-document';
				$inputs['document'] = $_POST['attachments'][0];
			}
			if( $action != '' ) {
				$this->_send_wablas( $inputs, $action );	        
			}
        }
        
        /* autorespond */
		$data = $this->autorespond_message( array(), $_POST );
		if( !empty( $data ) ) {
			$inputs = array(
	            "phone" => $data['to_number_2'],
	            "message" => $data['message_2']
			);
	        $this->_send_wablas( $inputs );
		}
		
        wp_die();
	}

    /*
	 * Send message to RuangWA API
	 * @since	0.8.3
	 * @access	public
	 */
	private function _send_ruangwa( $inputs, $action='send-message' ){
        $url = 'http://ruangwa.com/api/' . $action . '.php';
        $inputs['token'] = $this->ruangwa_token;
        $curl = wp_remote_post( $url, array(
	    	'body' => $inputs
        ) );        
        do_action( 'cf7sendwa_after_send_ruangwa', $url, $inputs );
	}
	public function send_ruangwa() {
        check_ajax_referer( 'cf7sendwa-api-action', 'security' );
		$inputs = [
		    'phone' => $_POST['to_number'],
		    'message' => $_POST['message']
		];
        $this->_send_ruangwa( $inputs );
        if( !empty( $_POST['attachments'] ) ) {
	        $inputs = [
	        	'phone' => $_POST['to_number'],
	        	'caption' => null,
	        ];
			$mime_type = $_POST['attachment_type'];
			$action = '';
			if( strpos( $mime_type, 'image' ) >= 0 ) {
				$inputs['image'] = $_POST['attachments'][0];
				$action = 'send-image';
			} elseif( strpos( $mime_type, 'video' ) >= 0 ) {
				$action = 'send-video';
				$inputs['video'] = $_POST['attachments'][0];
			} else {
				$action = 'send-document';
				$inputs['document'] = $_POST['attachments'][0];
			}
			if( $action != '' ) {
				$this->_send_ruangwa( $inputs, $action );	        
			}
        }
        
        /* autorespond */
		$data = $this->autorespond_message( array(), $_POST );
		if( !empty( $data ) ) {
			$inputs = array(
	            "phone" => $data['to_number_2'],
	            "message" => $data['message_2']
			);
	        $this->_send_ruangwa( $inputs );
		}
        
        wp_die();
	}
	
    /*
	 * Send message to Twilio API
	 * @since	0.4.2
	 * @access	public
	 *
	 */
    public function send_twilio() {
        check_ajax_referer( 'cf7sendwa-api-action', 'security' );
        $inputs = array(
            "Body" => $_POST['message'],
            "From" => "whatsapp:+" . get_option( 'cf7sendwa_twilio_from', '14155238886' ),
            "To" => "whatsapp:+" . $_POST['to_number'],
        );
        if( !empty( $_POST['attachments'] ) ) {
	        $inputs[ 'MediaUrl' ] = '';
	        foreach( $_POST['attachments'] as $media ){
		        if( $inputs[ 'MediaUrl' ] != '' ) {
			        break;
		        }
		        $inputs[ 'MediaUrl' ] .= $media;
	        }
        }
        $url = 'https://api.twilio.com/2010-04-01/Accounts/' . $this->twilio_sid . '/Messages.json';
        $curl = wp_remote_post( $url, array(
	    	'body' => $inputs,
	    	'headers' => array(
				'Authorization' => 'Basic ' . base64_encode($this->twilio_sid . ':' . $this->twilio_token)
	    	)    
        ) );
        
        /* autorespond */
		$data = $this->autorespond_message( array(), $_POST );
		if( !empty( $data ) ) {
			$inputs = array(
	            "Body" => $data['message_2'],
	            "From" => "whatsapp:+" . get_option( 'cf7sendwa_twilio_from', '14155238886' ),
	            "To" => "whatsapp:+" . $data['to_number_2'],
			);
	        $curl = wp_remote_post( $url, array(
		    	'body' => $inputs,
		    	'headers' => array(
					'Authorization' => 'Basic ' . base64_encode($this->twilio_sid . ':' . $this->twilio_token)
		    	)    
	        ) );
		}
		
        do_action( 'cf7sendwa_after_send_twilio', $url, $inputs );
        wp_die();
    }
    
    /**
	 * Send message to custom API
	 * @since 0.7.1
	 */
    public function cf7sendwa_api() {
        check_ajax_referer( 'cf7sendwa-api-action', 'security' );
        $form_id = $_POST['cf7_id'];
        $data = [
	    	'message' => $_POST['message'],
	    	'to_number' => $_POST['to_number'],
	    	'cf7_inputs' => $_POST['cf7_inputs'],   
        ];
        if( !empty( $_POST['attachments'] ) ) {
	        $data['attachments'] = $_POST['attachments'];
		}
		$data = $this->autorespond_message( $data, $_POST );
		$data = apply_filters( 'cf7sendwa_custom_api_data', $data );
		
        do_action( 'cf7sendwa_custom_send_api', $data, $form_id );
        do_action( 'cf7sendwa_custom_send_api_' . $form_id, $data );
        
        wp_die();        
    }

	/**
	 * Prepare attachments
	 * @since 0.5.0
	 * @access public
	 */     
    public function prepare_attachments( $contact_form ) {
	    
	    $attachments = $this->get_attachments( $contact_form );
	    $opt_attachments = array();
	    if( !empty( $attachments ) ) {
		    $wp_upload_dir = wp_upload_dir();
		    $parent_post_id = null;
		    foreach( $attachments as $file_path ) {
			    if( is_array( $file_path ) ) {
				    $file_path = $file_path[0];
			    }			    
				$file_name = basename( $file_path );
				$new_file_name = date('Ymdhis').'.'.uniqid().'-'.$file_name;
				$new_file_path = $wp_upload_dir['basedir'].'/cf7sendwa/' . $new_file_name;
			    copy( $file_path, $new_file_path );
				$file_type = wp_check_filetype( $file_name, null );
				$attachment_title = sanitize_file_name( pathinfo( $file_name, PATHINFO_FILENAME ) );
				$post_info = array(
					'guid'           => $wp_upload_dir['baseurl']. '/cf7sendwa/' . $new_file_name,
					'post_mime_type' => $file_type['type'],
					'post_title'     => $attachment_title,
					'post_content'   => '',
					'post_status'    => 'inherit',
				);		
			    if( $attachment_id = wp_insert_attachment( $post_info, $new_file_path, $parent_post_id ) ) {			    
				    array_push( $this->attachments, $post_info['guid'] );
				    array_push( $this->attachment_types, $post_info['post_mime_type'] );
				    array_push( $this->attachment_ids, $attachment_id );				    
				    $this->attachment_type = $post_info['post_mime_type'];
				    update_post_meta( $attachment_id, 'cf7sendwa_cf7_id', $contact_form->id() );
				    update_post_meta( $attachment_id, 'cf7sendwa_cf7_uniqid', uniqid() );
			    }
		    }
	    }
	    
    }
    
    /*
	 * Get attachments 
	 * @since 0.5.0
	 * @access private
	 */
    private function get_attachments( $contact_form ) {
		$attachments = array();
		if ( $submission = WPCF7_Submission::get_instance() ) {
			$uploaded_files = $submission->uploaded_files();
		}
		$template = $contact_form->prop('whatsapp');
		if( isset( $template['attachments'] ) && trim( $template['attachments'] ) == '' ) {
			$template = $contact_form->prop('mail');		
		}
		if( isset( $template['attachments'] ) ) {
			foreach ( (array) $uploaded_files as $name => $path ) {
				if ( false !== strpos( $template['attachments'], "[${name}]" )
				and ! empty( $path ) ) {
					$attachments[] = $path;
				}
			}
			foreach ( explode( "\n", $template['attachments'] ) as $line ) {
				$line = trim( $line );
				if ( '[' == substr( $line, 0, 1 ) ) {
					continue;
				}
				$path = path_join( WP_CONTENT_DIR, $line );
				if ( ! wpcf7_is_file_path_in_content_dir( $path ) ) {
					continue;
				}
				if ( is_readable( $path )
				and is_file( $path ) ) {
					$attachments[] = $path;
				}
			}
		}
		return $attachments;
    }
    
    /*
	 * Content tags replace value on submit
	 * @since 0.11.2
	 * @access public
	 */
    public function apply_content_tags_submit( $contact_form ) {
	    $wa = $contact_form->prop('whatsapp');
	    $body = '';
	    if( is_array( $wa ) && !empty( $wa ) ) {
			$body = trim( $wa['body'] );		    	
	    }
	    if( $body == '' ) {
			$mail = $contact_form->prop('mail');    
			$body = $mail['body'];
	    }
        
	    $mailtag = new WPCF7_MailTaggedText( $body );        
		if( strpos( $body, 'cf7sendwa-skip-empty' ) !== false ) {
	        $wa_text = $mailtag->replace_tags();
	        $this->wa_replaced_tags = $mailtag->get_replaced_tags();    
	        $_body = explode( "\n", do_shortcode( $body ) );
	        $body = '';
	        if( !empty($_body) ) {
		        foreach( $_body as $line ) {
			        if( $line != 'cf7sendwa__no__value' ) {
				        if( $line == '' ) $line = "\n";
				        elseif( $body != '' )  $body .= "\n"; 
				        $body .= $line;
			        }
		        }
	        }
	        $mailtag = new WPCF7_MailTaggedText( $body );   
		}
        $this->wa_submitted_text = $mailtag->replace_tags();
    }
    
    public function render_skip_empty( $atts, $content='' ) {
		$content = trim( $content );
		if( $content != '' && is_array( $this->wa_replaced_tags ) && !empty( $this->wa_replaced_tags ) ) {	
			foreach( $this->wa_replaced_tags as $tag => $val ) {								
				if( strpos( $content, $tag ) !== false && trim($val) == '' ) {
					$content = 'cf7sendwa__no__value';
					break;
				}						
			}
		}	   		
	    return $content;
    }
    
    /*
	 * Feedback ajax json echo 
	 * @since 0.5.0
	 * @access public
	 */
    public function feedback_ajax_json_echo( $response, $result ) {
	    
	    if( !empty( $this->attachments ) ) {
		    $response['attachments'] = array();
		    for( $i=0; $i<count( $this->attachments ); $i++ ) {
			    $url = home_url( '/cf7sendwa-media/' );
			    $uniqid = get_post_meta( $this->attachment_ids[$i], 'cf7sendwa_cf7_uniqid', true );
			    $url = $url . $uniqid;
			    array_push( $response['attachments'], $url );
		    }
		    $response['attachment_type'] = $this->attachment_type;
	    }
	    
	    if( $this->woo_order ) {
		    $response['woo_order'] = $this->woo_order_id;
		    $response['redirect'] = $this->woo_order_received_url;
		    $response['woo_links'] = $this->woo_order_links;
	    }
	    $response['woo_received_html'] = $this->order_woo_received_html;
	    $response['woo_payment_html'] = $this->order_woo_payment_html;
	     
	    $response['wa_text'] = $this->wa_submitted_text;
	    $this->wa_submitted_text = '';
	    $response['message'] = do_shortcode( $response['message'] );
	    
	    return $response;
	    
    }
    
    /**
	 * Render order received link
	 * @since 0.8.1
	 * @access public
	 */   
    public function render_woo_received_link( $atts ) {
	    $html = '';
	    if( !is_null( $this->woo_order_links['received'] ) ) {
			$atts = shortcode_atts( array(
				'title' => 'Received Order',
			), $atts );
		    $html = '__woo_received__' . $atts['title'] . '__close_a__';
	    }	   
	    return $html;
    }
    
    /**
	 * Render order payment link
	 * @since 0.8.1
	 * @access public
	 */   
    public function render_woo_payment_link( $atts ) {
	    $html = '';
	    if( !is_null( $this->woo_order_links['payment'] ) ) {
			$atts = shortcode_atts( array(
				'title' => 'Pay Now',
			), $atts );
			$html = '__woo_payment__' . $atts['title'] . '__close_a__';
	    }
	    return $html;
    }

    /*
	 * Switch Woocommerce Checkout Page
	 * @since 0.6.0
	 * @access public
	 */
    public function switch_woo_checkout( $template ) {
        if( is_checkout() && get_option( 'cf7sendwa_woo_checkout', '' ) != '' && 
            !is_checkout_pay_page() && !is_wc_endpoint_url( 'order-received' ) ) {
	        $this->woo_cart = cf7sendwa_woo_get_cart_items();
	        $this->woo_shippings = cf7sendwa_woo_get_shippings();
            remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
            add_filter( 'woocommerce_show_page_title', '__return_false' );
	        $template = apply_filters( 'cf7sendwa_product_checkout_template', plugin_dir_path( __FILE__ ) . 'template-checkout.php', $this );
        }
	    return $template;
    }
    
    public function do_shortcode_inside_form( $form ){
	    $form = do_shortcode( $form );
	    return $form;
    }
    
    public function custom_cf7_tags() {
	    $this->woo_checkout_cart_tag();
	    $this->woo_quickshop_tag();
    }
    
    /*
	 * Add custom tag [cf7sendwa_woo_checkout]
	 * @since 0.6.4
	 * @access public
	 */
	public function woo_checkout_cart_tag() {
		add_shortcode( 'cf7sendwa_woo_checkout', array( $this, 'cf7sendwa_woo_checkout_render' ) ); // backwards compatibility
		add_shortcode( 'cf7sendwa-checkout', array( $this, 'cf7sendwa_woo_checkout_render' ) );
	}	
	public function cf7sendwa_woo_checkout_render( $atts, $content='' ) {
	    $html = '';
        if( is_checkout() && get_option( 'cf7sendwa_woo_checkout', '' ) != '' ) {
	        wp_enqueue_script( 'cf7sendwa-commonlib' );
	        ob_start();
	        include apply_filters( 'cf7sendwa_woo_checkout_template', 'partials/cf7-send-wa-public-display.php' );
	        $html .= ob_get_contents();
	        ob_end_clean();
		} else {
			if( $this->quickshop_rendered ) {
				$atts = shortcode_atts( array(
					'id' => '',
					'cart_label' => 'Add to Cart', 
					'sticky' => 'no', // or yes
					'max-width' => '',
                    'max-height' => '',
					'top' => '',
					'bottom' => '',
					'stickyend' => '.site-footer', // selector that indicate end of sticky
					'viewport_bottom' => 300, 
					'redirect' => 'cart',
					'button_append_to' => '',
					'loader_selector' => '',
					'shipping' => ''
				), $atts, 'cf7sendwa-checkout');
				$html = '';
				wp_localize_script( $this->plugin_name, 'cf7sendwa_qsreview', apply_filters( 'cf7sendwa_quickshop_checkout_atts', $atts ) );
				if( $atts['sticky'] == 'yes' ) {
					wp_enqueue_script( 'sticky' );
				}
				ob_start();
				include apply_filters( 'cf7sendwa_order_review_template', 'partials/cf7-send-wa-quickshop-checkout.php' );
				$html = ob_get_contents();
				ob_end_clean();
			} else {
				$html = 'No quickshop rendered before checkout tag!';
			}
		}			
		return $html;    		
	}
	
    /**
	 * Create Woocommerce Order 
	 * @since 0.6.0
	 * @access public
	 */
    public function create_woo_order( $contact_form ) {
	    
	    if( isset( $_POST['_cf7sendwa_basic_form'] ) ) {
		    return true;
		}
		
		$woo_checkout_form = get_option( 'cf7sendwa_woo_checkout', '' );	    
		// instant checkout from quickshop
		$woo_quickshop = false;
		if( isset( $_POST['quickshop_cart'] ) ) {
			$cart = json_decode( str_replace( "\\", "", $_POST['quickshop_cart'] ), true );
			$woo_quickshop = true;
		}
		if( $contact_form->id() == $woo_checkout_form || $woo_quickshop ) {
			$submission = WPCF7_Submission::get_instance();
			if ( ! $submission || ! $posted_data = $submission->get_posted_data() ) {
				return;
			}
			$_posted_data = array();
			foreach( $posted_data as $k => $v ) {
				$_key = str_replace( '_wpcf7', '', $k );
				if( $k == $_key ) {
					$_posted_data[ $k ] = $v;
				}
			}
			$f1 = $contact_form->additional_setting( 'woo_checkout_first_name' );
			$f2 = $contact_form->additional_setting( 'woo_checkout_last_name' );
			$f3 = $contact_form->additional_setting( 'woo_checkout_email' );
			$f4 = $contact_form->additional_setting( 'woo_checkout_phone' );
			$f5 = $contact_form->additional_setting( 'woo_checkout_address' );
			$f6 = $contact_form->additional_setting( 'woo_checkout_order_note' );
			$woo_settings = array(
				'first_name' => !empty( $f1 ) ? $f1[0]:'',
				'last_name' => !empty( $f2 ) ? $f2[0]:'',
				'email' => !empty( $f3 ) ? $f3[0]:'',
				'phone' => !empty( $f4 ) ? $f4[0]:'',
				'address' => !empty( $f5 ) ? $f5[0]:'',
				'note' => !empty( $f6 ) ? $f6[0]:''
			);
			
			$woo_order = array();
			foreach( $woo_settings as $key=>$val ){
				if( isset( $posted_data[ $val ] ) ) {
					$woo_order[ $key ] = $posted_data[ $val ];
					unset( $_posted_data[$val] );
				}
			}
			if( isset( $_posted_data['item_qty'] ) ) {
				unset( $_posted_data['item_qty'] );
			}
				
			$order_address = array(
	            'first_name' => $woo_order[ 'first_name' ],
	            'last_name'  => $woo_order[ 'last_name' ],
	            'email'      => $woo_order[ 'email' ],
	            'phone'      => $woo_order[ 'phone' ],
	            'address_1'  => $woo_order[ 'address' ],
	        );
	        
            WC()->session = new WC_Session_Handler();
            WC()->session->init();
    		$customer = WC()->session->get( 'customer' );
	        if( $woo_quickshop ) {
				if( is_array( $customer ) && !empty( $customer ) ) {
					if( trim($order_address['address_1']) == '' ) {
						if( $customer['shipping_address_1'] == '' ) {
							$order_address['address_1'] = $customer['address_1'];
						} else {
							$order_address['address_1'] = $customer['shipping_address_1'];
						}
					}	
					if( trim($order_address['email']) == '' ) {
						$order_address['email'] = $customer['email'];
					}			
				}
			} else {
                WC()->cart = new WC_Cart();
                WC()->cart->set_cart_contents( WC()->session->get('cart') );
				$shipping = cf7sendwa_woo_get_shippings();
				$order_address['address_2'] = $shipping['address_parts']['address_2'];
				$order_address['city'] = $shipping['address_parts']['city'];
				$order_address['postcode'] = $shipping['address_parts']['postcode'];
			}
	        
	        $posted_data = apply_filters( 'cf7sendwa_checkout_cf7_data', $_posted_data );
	        
	        if( $woo_quickshop ) {
		        
		        unset( $posted_data['quickshop_cart'] );
		        
				$obj_order = new WC_Order();
				$obj_order->set_created_via( 'contact-form-7' );
			    $obj_order->set_customer_ip_address( WC_Geolocation::get_ip_address() );
			    $obj_order->set_customer_user_agent( wc_get_user_agent() );
				if( !isset( $order_address['address_1'] ) || 
				    ( isset( $order_address['address_1'] ) && trim($order_address['address_1']) == '' ) ) {
					$order_address['address_1'] = $shipping['address'];					
				}
				$obj_order->set_address( $order_address, 'billing' );
			    $obj_order->set_address( $order_address, 'shipping' );
			    
				foreach( $cart['items'] as $item ) {
					$_args = array();
					$_product = wc_get_product( $item['prop']['product_id'] );
					if( $item['prop']['product_type'] == 'variation' ) {
						$_args['variation'] = $item['prop']['pa'];
						$_product = wc_get_product( $item['prop']['variation_id'] );
					}
					$obj_order->add_product( $_product, $item['qty'], $_args );
				}	
				$obj_order->calculate_totals();	
										
				if( isset( $woo_order['note'] ) && $woo_order['note'] != '' ) {
					$obj_order->set_customer_note( $woo_order['note'] );
				}

				if( !is_null( $posted_data ) && !empty( $posted_data ) ) {
					foreach( $posted_data as $key=>$val ){
						$obj_order->add_meta_data( $key, $val );
					}
				}
				
				do_action( 'cf7sendwa_before_woo_order_save', $obj_order, $posted_data );
			    $order_id = $obj_order->save();
				
			    if( $order_id && is_array( $customer ) && isset( $customer['id'] ) ) {
					update_post_meta( $order_id, '_customer_user', $customer['id'] );	    
			    }
							    
				if( ! apply_filters( 'cf7sendwa_disable_woocommerce_email', false ) ) {
				    $mail_order = new WC_Email_New_Order();
				    $mail_order->trigger( $order_id, $obj_order );
				    if( isset($order_address['email']) && is_email( $order_address['email'] ) ) {
					    $mail = new WC_Email_Customer_Invoice();
					    $mail->trigger( $order_id, $obj_order );
				    }
				}
				
	        } else {
		        $__note = null;
		        if( isset( $woo_order['note'] ) && $woo_order['note'] != '' ) {
			    	$__note = $woo_order['note'];
			    }
				$obj_order = cf7sendwa_woo_create_order( $order_address, $__note, $posted_data );
			}
			
			
			$this->woo_order = true;
			$this->woo_order_id = $obj_order->get_id();
			
			do_action( 'cf7sendwa_after_woo_order', $this->woo_order_id, $posted_data );
			
			$order_redirect = get_option( 'cf7sendwa_woo_order_redirect', '' );
			if( $order_redirect == '' || $order_redirect == 'thankyou' ) {
				$this->woo_order_received_url = $obj_order->get_checkout_order_received_url();
			} elseif( $order_redirect == 'payment' ) {
				$this->woo_order_received_url = $obj_order->get_checkout_payment_url();
			} elseif( $order_redirect == 'none' ) {
				$this->woo_order_received_url = 'none';	
				$this->woo_order_links['received'] = $obj_order->get_checkout_order_received_url();
				$this->woo_order_links['payment'] = $obj_order->get_checkout_payment_url();
			}
			
			if( !$woo_quickshop ) {
				WC()->cart->empty_cart();
				WC()->session->set('cart', array());
			}
		}

    }
    
    /**
	 * Validate if cart exists for woocommerce order checkout 
	 * @since 0.6.3
	 * @access public
	 */
    public function validate_cart_exists( $result, $tag ) {
		$submission = WPCF7_Submission::get_instance();
		$woo_checkout_form = get_option( 'cf7sendwa_woo_checkout', '' );
		if( $submission->get_contact_form()->id() == $woo_checkout_form ) {
			if( WC()->cart->get_cart_contents_count() == 0 ) {
				$tag->name = "cf7sendwa_woo_checkout_".$submission->get_contact_form()->id();
				$this->woo_cart_empty = true;		
				$result->invalidate( $tag, 'Empty Cart' );
			}			
		}
	    return $result;
    }
    
    /**
	 * Error validation message on cart empty 
	 * @since 0.6.3
	 * @access public
	 */
    public function set_validation_error( $message, $status ) {
	    if(  $this->woo_cart_empty && $status == 'validation_error' ) {
	    	$message = apply_filters( 'cf7sendwa_empty_cart_message', __( 'Your cart is empty, please add one or more items to the cart', 'cf7sendwa' ) );
	    }
	    return $message;
    }
    
    /**
	 * Check if cart need for shipping
	 * @since 0.6.5
	 * @access public
	 */
    public function check_need_shipping() {
		$woo_checkout_form = get_option( 'cf7sendwa_woo_checkout', '' );
		if( $woo_checkout_form != '' && is_checkout() ) {
			if( WC()->cart->needs_shipping() && get_option( 'cf7sendwa_requireshipping', '0' ) == '1' ) {
				$shippings = cf7sendwa_woo_get_shippings();
				if( empty( $shippings['lines'] ) ) {
					wp_redirect( wc_get_cart_url() . '?cf7wa=need_shipping' );
					die();					
				}
			}
		}		    
    }
    
    /**
	 * Display custom WooCommerce notification 
	 * @since 0.6.5
	 * @access public
	 */
    public function woo_custom_notice() {
	    if( isset( $_GET['cf7wa'] ) ){
			switch( $_GET['cf7wa'] ){
				case 'need_shipping':
					$text = 'You must provide shipping method before checkout';
					wc_add_notice( apply_filters( 'cf7sendwa_need_shipping_notice', $text ), 'notice' );
					break;
			}		    
	    }
    }
    
    /**
	 * Display WooCommerce cart total in full width 
	 * @since 0.6.5
	 * @access public
	 */
    public function woo_custom_cart_total_css() {
	    if( is_cart() && get_option( 'cf7sendwa_fullcart', '0' ) == '1' ) {
			?><style type="text/css">
.woocommerce .cart-collaterals .cart_totals, .woocommerce-page .cart-collaterals .cart_totals {
	width: 100%;
}			
</style><?php
		}
    }
    
    /**
	 * Load customer info on WooCommerce checkout
	 * @since 0.8.0
	 * @access public
	 */
	public function woo_checkout_load_customer_info( $scanned_tag, $replace ) {
		if( is_checkout() && get_option( 'cf7sendwa_woo_checkout', '' ) != '' ) {
	    	if( is_null( $this->customer ) && is_user_logged_in() ) {
	        	$this->customer = new WC_Customer( get_current_user_id() );
	        }
			$contact_form = WPCF7_ContactForm::get_instance( get_option( 'cf7sendwa_woo_checkout' ) );
			$fields = array( 'first_name', 'last_name', 'email', 'phone', 'address', 'order_note' );
			$the_value = '';			
			$cust_billing = $this->customer->billing;
			foreach( $fields as $field ) {
				$tag = $contact_form->additional_setting( 'woo_checkout_'.$field );
				if( !empty($tag) && $scanned_tag['name'] == $tag[0] ) {
					if( $field == 'address' ) {
						$field = 'address_1';
					}
					if( isset( $cust_billing[ $field ] ) ) {
						$the_value = $cust_billing[ $field ];
					}
				}	
			}
			if( $scanned_tag['basetype'] != 'textarea') {
				$scanned_tag['values'][] = $the_value;
			} else {
	 			$scanned_tag['content'] = $the_value;				
			}
		}
		return $scanned_tag;
	} 
	
	/*
     * Get list product for Quick Shop 
     * @since 0.10.0
     * @access public
     */
	public function web_list_product() {
		if( $this->woo_is_active ) {
			$args = array();
	        if( isset( $_POST['args'] ) ) {
		        $args = $_POST['args'];
	        }
	        $products = Cf7_Send_Wa_Products::list_all( $args );
	        include apply_filters( 'cf7sendwa_product_list_item_template', 'partials/woo-product-list-item.php', $args );
		}
		die();
	}
	/*
     * Get list product using REST API
     * @since 0.10.2
     * @access public
     */
	public function api_list_product($request) {
		$args = $request->get_params();
		$products = Cf7_Send_Wa_Products::list_all( $args );
		return $products;
	}
	
	/*
     * Add to cart for Quick Shop 
     * @since 0.10.0
     * @access public
     */
	public function quickshop_add_to_cart() {
		if( $this->woo_is_active ) {
			$added = 0;
			if( isset( $_POST['quickshop_cart'] ) ) {
				WC()->cart->empty_cart();
				WC()->session->set('cart', array());  
				$cart = json_decode( str_replace( "\\", "", $_POST['quickshop_cart'] ), true );
				foreach( $cart['items'] as $item ) {
					$_variation_id = null;
					$_variation = null;
					$_product_id = $item['prop']['product_id'];
					if( $item['prop']['product_type'] == 'variation' ) {
	                    $_variation = [];
	                    foreach( $item['prop']['pa'] as $k=>$v ) {
	                        $_variation[ 'attribute_' . $k ] = $v;
	                    }
	                    $_variation_id = $item['prop']['variation_id'];
	                }
					WC()->cart->add_to_cart( $_product_id, $item['qty'], $_variation_id, $_variation );
					$added++;
				}			
			}
			
			if( $added > 0 ) {
				global $woocommerce;
				if( $_POST['redirect'] == 'checkout' ){
					$redirect_page_url = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : $woocommerce->cart->wc_get_checkout_url();				
				} else {
					$redirect_page_url = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : $woocommerce->cart->get_cart_url();				
				}
				$result = array(
					'redirect_url' => $redirect_page_url
				);	
				echo json_encode( $result );
			}
		}
		die();		
	}
    
    /*
	 * Add custom tag [cf7sendwa_woo_quickshop]
	 * @since 0.6.4
	 * @access public
	 */
	public function woo_quickshop_tag() {
		add_shortcode( 'cf7sendwa-quickshop', array( $this, 'cf7sendwa_woo_quickshop_render' ) );
	}	
	public function cf7sendwa_woo_quickshop_render( $atts ) {
		
		if( !is_checkout() && !$this->quickshop_rendered ) {
			
		    $atts = shortcode_atts( array(
                'render' => 'list',
                'columns' => '2',
                'colors' => '#ffffff,#f9f9f9',
                'mobile-columns' => '2',
				'category' => '',
		        'filter' => 'no',
		        'products' => '',
		        'detail' => 'yes',
		        'editableqty' => 'no',
		        'orderby' => '',
		        'order' => '',
		        'limit' => '10',
		        'paging' => 'auto',
		        'mode' => 'standard'
			), $atts, 'cf7sendwa-quickshop' );
			
			if( $atts['products'] == 'current' ) {
				$atts['mode'] = 'silent';
			}
			
		    $product_categories = array();
		    
		    $categories = cf7sendwa_woo_list_categories( $atts['category'] );
		    foreach( $categories as $cat ) {
		        array_push( $product_categories, [
		            'id' => $cat->term_id,
		            'slug' => $cat->slug,
		            'name' => $cat->name,
		        ] );
		    }
		    
		    if( isset( $atts['products'] ) && $this->woo_is_active && is_product() && strtolower( $atts['products'] ) == 'current' ) {
			    $atts['products'] = get_the_ID();
			    $atts['is_current_product'] = 'yes';
			    $this->current_product_checkout = true;
		    }
		    if( isset( $atts['mode'] ) && $atts['mode'] == 'silent' ) {
			    $atts['is_current_product'] = 'yes';
			    $this->current_product_checkout = true;
		    }
		    
	    	wp_enqueue_style( 'unsemantic-grid' );
			
			if( $atts['filter'] == 'yes' ) {
				wp_enqueue_style( 'select2' );
				wp_enqueue_script( 'select2' );
			}
			if( $atts['detail'] == 'yes' ) {
				wp_enqueue_style( 'fotorama' );
				wp_enqueue_script( 'fotorama' );
		    	wp_enqueue_style( 'jquery-modal' );
		    	wp_enqueue_script( 'jquery-modal' );    
			}
			
		    wp_enqueue_style( $this->plugin_name );
			wp_enqueue_script( 'underscore' );
			wp_enqueue_script( 'knockout' );
			$_arr = array(
				'base_url'			 => home_url( '/' ),
				'ajaxurl'			 => admin_url( 'admin-ajax.php' ), 
				'is_mobile'			 => wp_is_mobile() ? '1':'0',
		        'currency' 			 => get_woocommerce_currency_symbol(),
		        'decimal_separator'  => wc_get_price_decimal_separator(),
		        'thousand_separator' => wc_get_price_thousand_separator(),
		        'decimals'           => wc_get_price_decimals(),
		        'price_format'       => get_woocommerce_price_format(),
		        'categories'         => $product_categories,
		        'security' 			 => wp_create_nonce( 'cf7sendwa-rest-request-nonce' ),
				'quickshop_atts' 	 => $atts,
                'assets_dir'         => plugin_dir_url( dirname(__FILE__) ) .'includes/assets/'
			);
			if( is_page() || is_shop() || is_single() || is_product_category() || is_product_tag() ) {
                $_arr['cart'] = cf7sendwa_woo_get_cart_items();
            }			
			wp_localize_script( $this->plugin_name, 'cf7sendwa', $_arr );
			wp_enqueue_script( 'cf7sendwa-commonlib' );
			wp_enqueue_script( $this->plugin_name );
			
		    do_action( 'cf7sendwa_before_product_list', $_arr );

		    $html = '';
		    ob_start();
		    include apply_filters( 'cf7sendwa_product_list_template', 'partials/woo-product-list.php' );
		    $html = ob_get_contents();
		    ob_end_clean();
		    $this->quickshop_rendered = true;
		} else {
			$html = 'Cannot use multiple quick shop in a page.';
		}    
		
		return $html;    		
	}
	
	public function floating_button( $btn, $selector, $atts ){
		if( $atts['id'] == $this->global_form ) {
			$cf7sendwa_global_position = get_option( 'cf7sendwa_global_position', '' );
			$style = ' style="right:0px; bottom: 0px;"';
			$_top = 0;
			if( is_admin_bar_showing() ) {
				$_top = $_top + 30;
			}
			switch( $cf7sendwa_global_position ){
				case 'bottom-left':
					$style = ' style="left:0px; bottom: 0px;"';
					break;
				case 'top-left':
					$style = ' style="left:0px; top: '.$_top.'px;"';
					break;
				case 'top-right':
					$style = ' style="right:0px; top: '.$_top.'px;"';
					break;
			}
			$btn = '<div class="cf7sendwa-floating-button"' . $style . '>
				<div class="cf7sendwa-float-button-wrapper">
				<a href="'.$selector.'" rel="modal:open" class="cf7sendwa-btn-link">
					<img class="tooltip" 
					     title="'. $this->global_btn_tooltip .'"
					     src="' .plugin_dir_url( dirname( __FILE__ ) ) . 'includes/assets/img/whatsapp.svg' . '"></a>
				</div>
			</div>';	
		}
		return $btn;
	}
	
	public function render_global_form(){
		if( $this->global_form != '' ) {
			wp_enqueue_style( $this->plugin_name );
			add_filter( 'cf7sendwa_popup_button', array( $this, 'floating_button' ), 10, 3 );
			echo do_shortcode( '[contact-form-7-wa id="' . $this->global_form . '" popup="button"]' );
		}
	}
	
	public function cf7_wa_button() {
		if( is_product() ) {			
			$form_id = get_option( 'cf7sendwa_woo_single_product', '' );
			if( $form_id != '' ) {
				wp_enqueue_style( $this->plugin_name );
				$text = get_option( 'cf7sendwa_single_button', 'Chat Seller' );
				$wrap_div = get_option( 'cf7sendwa_single_button_wrap_div', '0' );
				echo '<' . ($wrap_div == '1' ? 'div':'span') . ' class="cf7sendwa-single-product-button">';				
				$form = do_shortcode( '[contact-form-7-wa id="' . $form_id . '" in_woo="yes" popup="button" buttontext="' . $text . '"]' );
				echo $this->woo_popup_button;
				echo '</' . ($wrap_div == '1' ? 'div':'span') . '>';
				add_action( 'wp_footer', function() use( $form ) {
					echo $form;					
				}, 999 );
			}
		}
	}
	
	public function render_script_footer() {
		$cf7sendwa_is_custom_api = has_action( 'cf7sendwa_custom_send_api' );
		$cf7sendwa_custom_apis = [];
		if( !empty( $this->ids ) ) {
			foreach( $this->ids as $_id ) {
				if( has_action( 'cf7sendwa_custom_send_api_' . $_id ) ) {
					array_push( $cf7sendwa_custom_apis, $_id );
				}				
			}
		}
		if( !empty( $this->ids ) && !$this->script_loaded ) : ob_start(); ?>
<script type="text/javascript">
var cf7wa_ids = <?php echo json_encode( $this->ids ); ?>; 
var cf7wa_country = '<?php echo get_option( 'cf7sendwa_country', '62' ) == '' ? get_option( 'cf7sendwa_country' ) : '62'; ?>';
var cf7wa_numbers = <?php echo json_encode( $this->numbers ); ?>; 
var cf7wa_resends = <?php echo json_encode( $this->resends ) ?>;
var cf7wa_global_form = '<?php echo $this->global_form; ?>';
var cf7wa_single_product = '<?php echo get_option( 'cf7sendwa_woo_single_product', '' ); ?>';

<?php if( $this->provider != '' || $cf7sendwa_is_custom_api || !empty( $cf7sendwa_custom_apis ) ): ?>
var cf7wa_security = '<?php echo wp_create_nonce( 'cf7sendwa-api-action' ); ?>';
var cf7wa_ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
var cf7wa_custom_apis = <?php echo json_encode( $cf7sendwa_custom_apis ); ?>;
<?php endif; ?>

(function( $ ){
	<?php if( $this->quickshop_rendered ): ?>
	function quickshop_get_cart_text() {
		var vm = ko.toJS(Woo_QuickShop_Cart.getVM());
		var wa_txt = '';
		if( vm ) {
			_.each( vm.items, function( item, index, list ){
				if( wa_txt != '' ) {
					wa_txt += "\n\n";
				}
				var title = item.title;
				var sku = item.prop.sku; 
				if( item.subtitle != '' ) {
					title = item.title + ' - ' + item.subtitle;
				}
				if( sku != undefined && sku != '' ) {
					wa_txt += 'SKU: '+sku;
					wa_txt += "\n";
				}
				wa_txt += "*"+title+"*" + "\n" + ' @ ' + item.price_html + 'x' + item.qty + ' => ' + item.subtotal_html;
			} );
			wa_txt += "\n" + "-----------------------------------------";
			wa_txt += "\n" + '*TOTAL* ' + vm.price_total;
			wa_txt += "\n" + "-----------------------------------------";
		}	
		if( wa_txt != '' ) {
			wa_txt = "-----------------------------------------" + "\n" + wa_txt;
		}
		wa_txt = Hooks.apply_filters( 'cf7sendwa_checkout_order_item', wa_txt );
		return wa_txt;
	}
	<?php endif; ?>
	document.addEventListener( 'wpcf7mailsent', function( event ) {
		var the_id = event.detail.contactFormId;		
		if( _.indexOf( cf7wa_ids, the_id ) >= 0 ) {			
			var inputs = event.detail.inputs;
			var api_response = event.detail.apiResponse;
			var the_text = api_response.wa_text;
			<?php 
			$woo_order = '';
			if( $this->quickshop_rendered ) {
				?>the_text = the_text.replace( '[woo-orderdetail]', quickshop_get_cart_text() ); <?php
			} else {					
				include apply_filters( 'cf7sendwa_woo_order_details_template', 'partials/woo-order-details.php' ); 
			}
			?>
			var the_phone = cf7wa_numbers[ the_id ];
			_.each( inputs, function( detail, index, list ){
				if( '[' + detail.name + ']' == the_phone ) {
					the_phone = detail.value;
				}				
			} );
			
			if( the_phone.substr(0, 1) == '0' ) {
                the_phone = cf7wa_country + the_phone.substring(1);
            }
            
            var frm_id = $(event.target).attr('id');
            			
            <?php if( $this->provider != '' || $cf7sendwa_is_custom_api || !empty( $cf7sendwa_custom_apis ) ): ?>  
            	
            	$( '.wpcf7-response-output' ).wrap( '<div id="cf7sendwa_element_'+the_id+'" style="display:none;"></div>' );              
                var cf7sendwa_send_data = { 
	                'to_number': Hooks.apply_filters( 'cf7sendwa_to_number', the_phone, { 'frm_id': frm_id, 'inputs': inputs } ), 
	                'message': Hooks.apply_filters( 'cf7sendwa_text_message', the_text, { 'frm_id': frm_id, 'inputs': inputs } ), 
	                'security': cf7wa_security,
	                'cf7_id': the_id,
	                'cf7_inputs': inputs,
	                'woo_order_detail':'<?php echo $woo_order; ?>'
	            };
	            
	            <?php if( $this->quickshop_rendered ): ?>
	            cf7sendwa_send_data.woo_order_detail = quickshop_get_cart_text();
	            <?php endif; ?>
	            
	            if( api_response.woo_order != undefined ) {
		            cf7sendwa_send_data.order_id = api_response.woo_order;
		            cf7sendwa_send_data.order_links = api_response.woo_links;
	            }
                <?php if( $cf7sendwa_is_custom_api || !empty( $cf7sendwa_custom_apis ) ): ?>
                	cf7sendwa_send_data.action = 'cf7sendwa_api';
                <?php else: ?>
                	cf7sendwa_send_data.action = 'send_<?php echo $this->provider; ?>';
                <?php endif; ?>
				if( api_response.attachments != undefined ) {
					if( api_response.attachments.length ) {
						cf7sendwa_send_data.attachments = api_response.attachments;
					}
                }
                
                var $btn = $( '#'+frm_id ).find( 'button' );
                var btn_text = $btn.html();
                $btn.attr( 'disabled', true );
                $btn.html( btn_text + '&nbsp;<img src="<?php echo plugin_dir_url( dirname(__FILE__) ) .'includes/assets/img/ajax-loader.gif' ; ?>">' );
                $.ajax({
                    url: cf7wa_ajaxurl,
                    type: 'POST',
                    data: cf7sendwa_send_data,
                    success: function( response ) {
                        $( '.wpcf7-response-output' ).unwrap();
                        redirect_woo_order_received( api_response );
						$btn.attr( 'disabled', false );
						$btn.html( btn_text );
                    }
                });
                
            <?php else: ?>
            
	            if( api_response.attachments != undefined ) {
			        if( api_response.attachments.length ) {
				        the_text += "\n\n"+"*Attachments*";
			            _.each( api_response.attachments, function( url, index, list ){
				            the_text += "\n"+url;
			            } );
		            }
	            }
	            
	            the_text = window.encodeURIComponent( Hooks.apply_filters( 'cf7sendwa_text_message', the_text ) );
				var url = 'https://api.whatsapp.com/send?phone=' + 
						   Hooks.apply_filters( 'cf7sendwa_to_number', the_phone, { 'frm_id': frm_id, 'inputs': inputs } ) + 
						   '&text=' + Hooks.apply_filters( 'cf7sendwa_text_message', the_text, { 'frm_id': frm_id, 'inputs': inputs } );
				var isSafari = !!navigator.userAgent.match(/Version\/[\d\.]+.*Safari/);
				var iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
				if( isSafari && iOS ) {
					location = url;
				} else {
					window.open( url, '_blank' );
				}		
				redirect_woo_order_received( api_response );
			
            <?php endif; ?>
            
			Hooks.do_action( 'cf7sendwa_after_mailsent', { cf7event: event, phone: the_phone, text: the_text } );
		}
		if( $( '#cf7sendwa_quickshop_cart' ).length && $( '#cf7sendwa_quickshop_cart' ).val() != '' ) {
			var vm = Woo_QuickShop_Cart.getVM();
			vm.items.removeAll();
			$( '.item-subtotal' ).each( function(index){
				$( this ).html( 'Rp 0' );
			} );
		}		
	} );
	function redirect_woo_order_received( api_response ){
        if( api_response.woo_order != undefined ) {
            $( '.woocommerce-checkout-review-order-table' ).html('');
            if( api_response.redirect != 'none' ) {
	            var interval = window.setInterval( function(){
		            document.location = api_response.redirect;
		            clearInterval( interval );
		        }, 3000 );
	        }
        }            
	}
	$(document).ready( function(){
		if( cf7wa_global_form != '' ) {
			$('.tooltip').tooltipster({
				theme:'tooltipster-noir'
			});			
		}
		<?php if( $this->woo_is_active && is_product() ): ?>
		if( $( '.cf7sendwa-single-product-button' ).length && cf7wa_single_product != '' ) {
			<?php 
			$product = get_product( get_the_ID() ); 
			$greeting = get_option( 'cf7sendwa_single_product_greet', '' );	
			$greeting = str_replace( '{{product_name}}', $product->get_name(), $greeting );
			$greeting = str_replace( '{{product_sku}}', $product->get_sku(), $greeting );
			$greeting = urlencode( $greeting );			
			?>
			$( 'input[name="_wpcf7"]' ).each( function(){
				if( $(this).val() == cf7wa_single_product ) {
					var $frm = $(this).closest('form');
					var greet_text = '<?php echo $greeting ?>';	
					greet_text = greet_text.replace( /\+/g, '%20' );
					$frm.find( 'textarea' ).val( decodeURIComponent( greet_text ) );
					Hooks.add_action( 'cf7sendwa_after_mailsent', function({}){
						var _int = window.setInterval( function(){
							$frm.find( 'textarea' ).val( decodeURIComponent( greet_text ) );
							window.clearInterval( _int );
						}, 3000 );
					} );
				}
			} );
		}
		<?php endif; ?>
		if( $( '.cf7sendwa-cf7-container .wpcf7-submit' ).length ) {
			$( '.cf7sendwa-cf7-container .wpcf7-submit' ).each( function(){
				var $btn = $(this);				
				var $frm = $btn.parents("form");
				var frm_id = $frm.find( 'input[name=_wpcf7]' ).val();
				if( _.indexOf( cf7wa_ids, frm_id ) >= 0 ) {
					if( $btn.prop('tagName') == 'BUTTON' ) {
						var btn_label = $btn.html();
						var new_html = '<i class="fab fa-whatsapp"></i>&nbsp;' + btn_label;
						$btn.html( new_html );
						if( !$frm.find( '#cf7sendwa_quickshop_cart' ).length ) {
							$btn.addClass( 'cf7-basic-submit' );
						}
					}
				}
			} );
		}
		Hooks.add_action( 'cf7sendwa_after_mailsent', function( options ){
			var the_id = options.cf7event.detail.contactFormId;
			var woo_order_message = '';		
			if( cf7wa_resends.hasOwnProperty( the_id ) ) {
				if( options.cf7event.detail.apiResponse.woo_links ) {
					var r = options.cf7event.detail.apiResponse.woo_links.received;
					var p = options.cf7event.detail.apiResponse.woo_links.payment;	
					var message = options.cf7event.detail.apiResponse.message;
                    message = message.replace( '__woo_received__', '<a href="' + r + '" class="cf7sendwa-links" target="_blank">' );
					message = message.replace( '__woo_payment__', '<a href="' + p + '" class="cf7sendwa-links" target="_blank">' );
					message = message.replaceAll( /__close_a__/ig, '</a>' );
					options.cf7event.detail.apiResponse.message = "";					
					woo_order_message = message;
				}
				
				document.addEventListener( 'wpcf7reset', function( event ) {
                    if( woo_order_message != '' ) {
                        $( options.cf7event.detail.apiResponse.into + ' .wpcf7-response-output' ).html( woo_order_message );
                        woo_order_message = '';
                    }
                } );
				
				if( cf7wa_resends[the_id]['allow'] == '1' ) {
					var label = 'Resend WA Message';
					if( cf7wa_resends[the_id]['label'] != '' ) {
						label = cf7wa_resends[the_id]['label'] ;
					}
					var the_url = 'https://wa.me/'+options.phone+'?text=' + options.text;
					var html = ' <a class="cf7sendwa-resend-link" href="' + the_url + '" target="_blank">' + label + '</a>';
					var interval = window.setInterval( function(){						
						$( options.cf7event.detail.apiResponse.into + ' .wpcf7-response-output' ).append( html );
						clearInterval( interval );
					}, 3000 );
				}
			}
		} );
		Hooks.add_filter( 'cf7sendwa_to_number', function( val, options ){
			var $find = $( '#' + options.frm_id + ' .wpcf7-select_channel' );
			if( $find.length ) {
				var phone = $find.val();
				return phone;
			}
			return val;
		} );
	} );
})(jQuery);
</script>
		<?php  
        $script = ob_get_contents();
        $script = str_replace("\n"," ",$script);
        $script = str_replace("\t"," ",$script);
        ob_end_clean();
        echo $script;
        endif;
		$this->script_loaded = true;
	}  	
    
}