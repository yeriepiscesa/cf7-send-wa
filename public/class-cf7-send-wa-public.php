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
	protected $bodies = array();
	protected $numbers = array();
    protected $use_twilio = false;
    protected $twilio_sid = null;
    protected $twilio_token = null;
    
    protected $woo_is_active = false;
    protected $woo_cart = null;
    protected $woo_shippings = null;
    protected $woo_order_id = null;
    protected $woo_order = false;
    
    protected $attachments = array();

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
		add_shortcode( 'cf7sendwa_woo_checkout', array( $this, 'render_woo_cart' ) );
        
        $use_twilio = get_option( 'cf7sendwa_use_twilio', '0' );
        if( $use_twilio == '1' ) {
            $this->use_twilio = true;
            $this->twilio_sid = get_option( 'cf7sendwa_twilio_sid', '' );
            $this->twilio_token = get_option( 'cf7sendwa_twilio_token', '' );
        }
        
        if( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	        $this->woo_is_active = true;
        }

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.3.0
	 */
	public function enqueue_styles() {
		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cf7-send-wa-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    0.3.0
	 */
	public function enqueue_scripts() {
		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cf7-send-wa-public.js', array( 'jquery' ), $this->version, false );
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

	public function render_contact_form( $atts ) {
        $html = '';
        wp_enqueue_script( 'underscore' );
        $shortcode = '[contact-form-7';
        if( isset( $atts['number'] ) && $atts['number'] != '' ) {
            $this->numbers[$atts['id']] = $atts['number'];
            unset( $atts['number'] );
        } else {
            $this->numbers[$atts['id']] = get_option( 'cf7sendwa_number', '628123456789' );
        }
        foreach( $atts as $key=>$val ) {
            $shortcode .= ' ' . $key .'="' . $val . '"';
        }
        $shortcode .= ']';
        $html = do_shortcode( $shortcode );
        array_push( $this->ids, intval($atts['id']) );
        $_mail = get_post_meta( $atts['id'], '_mail', true );
        if( $_mail && isset( $_mail['body'] ) ) {
            $this->bodies[$atts['id']] = $_mail['body'];
        }        
		return $html;
	}
    
	public function render_script_footer() {
		if( !empty( $this->ids ) && !$this->script_loaded ) : ob_start(); ?>
<script type="text/javascript">
var cf7wa_ids = <?php echo json_encode( $this->ids ); ?>; 
var cf7wa_numbers = <?php echo json_encode( $this->numbers ); ?>; 
var cf7wa_bodies = <?php echo json_encode( $this->bodies ) ?>;
<?php if( $this->use_twilio ): ?>
var cf7wa_security = '<?php echo wp_create_nonce( 'cf7sendwa-twilio-action' ); ?>';
var cf7wa_ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
<?php endif; ?>
(function( $ ){
	document.addEventListener( 'wpcf7mailsent', function( event ) {
		var the_id = event.detail.contactFormId;
		if( _.indexOf( cf7wa_ids, the_id ) ) {
			var inputs = event.detail.inputs;
			var api_response = event.detail.apiResponse;
			var the_text = cf7wa_bodies[the_id];
			var input_array = {};
			$.each( inputs, function( index, detail ) {
				the_text = the_text.replace( '[' + detail.name + ']', detail.value );
				if( detail.name.indexOf( '[]' ) >= 0 ) {
					var _key = detail.name.replace('[]','');
					if( input_array[ _key ] == undefined ) {
						input_array[ _key ] = [];
					}
					input_array[ _key ].push( detail.value );
				}
			} );	
			_.each( input_array, function( val, key, list ){
				the_text = the_text.replace( '[' + key + ']', val.join(", ") );
			} );
			
			<?php include 'partials/woo-order-details.php'; ?>
			
			var the_phone = cf7wa_numbers[ the_id ];
            <?php if( $this->use_twilio ): ?>
                $( '.wpcf7-response-output' ).css( 'display', 'none' );
                var twilio_send_data = {
                    'action': 'send_twilio', 'to_number': the_phone, 
                    'message': the_text, 'security': cf7wa_security 
                };
				if( api_response.attachments != undefined ) {
					if( api_response.attachments.length ) {
						twilio_send_data.attachments = api_response.attachments;
					}
                }
                $.ajax({
                    url: cf7wa_ajaxurl,
                    type: 'POST',
                    data: twilio_send_data,
                    success: function( response ) {
                        $( '.wpcf7-response-output' ).css( 'display', 'block' );
                        redirect_woo_order_received( api_response );
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
            the_text = window.encodeURIComponent( the_text );
			var url = 'https://api.whatsapp.com/send?phone=' + the_phone + '&text=' + the_text;
			var isSafari = !!navigator.userAgent.match(/Version\/[\d\.]+.*Safari/);
			var iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
			
			if( isSafari && iOS ) {
				location = url;
			} else {
				window.open( url, '_blank' );
			}		
			redirect_woo_order_received( api_response );
            <?php endif; ?>
            
		}
	} );
	function redirect_woo_order_received( api_response ){
        if( api_response.woo_order != undefined ) {
            $( '.woocommerce-checkout-review-order-table' ).html('');
            var interval = window.setInterval( function(){
	            document.location = api_response.redirect;
	            clearInterval( interval );
	        }, 3000 );
        }            
	}
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
    
    /*
	 * Send message to Twilio API
	 * @since	0.4.2
	 * @access	public
	 *
	 */
    public function send_twilio() {
        check_ajax_referer( 'cf7sendwa-twilio-action', 'security' );
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
			    if( wp_insert_attachment( $post_info, $new_file_path, $parent_post_id ) ) {
				    array_push( $this->attachments, $post_info['guid'] );
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
		$template = $contact_form->prop('mail');
		foreach ( (array) $uploaded_files as $name => $path ) {
			if ( false !== strpos( $template, "[${name}]" )
			and ! empty( $path ) ) {
				$attachments[] = $path;
			}
		}
		foreach ( explode( "\n", $template ) as $line ) {
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
		return $attachments;
    }
    
    
    /*
	 * Feedback ajax json echo 
	 * @since 0.5.0
	 * @access public
	 */
    public function feedback_ajax_json_echo( $response, $result ) {
	    
	    if( !empty( $this->attachments ) ) {
		    $response['attachments'] = $this->attachments;
	    }
	    
	    if( $this->woo_order ) {
		    $response['woo_order'] = $this->woo_order_id;
		    $response['redirect'] = $this->woo_order_received_url;
	    }
	    
	    return $response;
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
	        $template = plugin_dir_path( __FILE__ ) . 'template-checkout.php';
	        
        }
	    return $template;
    }
    
    /*
	 * Render Woocommerce cart at checkout page
	 * @since 0.6.0
	 * @access public
	 */
    public function render_woo_cart() {
	    $html = '';
        if( is_checkout() && get_option( 'cf7sendwa_woo_checkout', '' ) != '' ) {
	        ob_start();
	        include 'partials/cf7-send-wa-public-display.php';
	        $html = ob_get_contents();
	        ob_end_clean();
		}			
		return $html;    
    }
    
    /**
	 * Create Woocommerce Order 
	 * @since 0.6.0
	 * @access public
	 */
    public function create_woo_order( $contact_form ) {
		$woo_checkout_form = get_option( 'cf7sendwa_woo_checkout', '' );	    
		if( $contact_form->id() == $woo_checkout_form ) {
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
			$shipping = cf7sendwa_woo_get_shippings();
			$order_address = array(
	            'first_name' => $woo_order[ 'first_name' ],
	            'last_name'  => $woo_order[ 'last_name' ],
	            'email'      => $woo_order[ 'email' ],
	            'phone'      => $woo_order[ 'phone' ],
	            'address_1'  => $woo_order[ 'address' ],
	            'address_2'  => $shipping['address_parts']['address_2'], 
	            'city'       => $shipping['address_parts']['city'],
	            'postcode'   => $shipping['address_parts']['postcode'],
	        );
			$obj_order = cf7sendwa_woo_create_order( $order_address, $woo_settings['note'], $_posted_data );
			$this->woo_order = true;
			$this->woo_order_id = $obj_order->get_id();
			$this->woo_order_received_url = $obj_order->get_checkout_order_received_url();
			
			WC()->cart->empty_cart();
			WC()->session->set('cart', array());
		}
    }
}
