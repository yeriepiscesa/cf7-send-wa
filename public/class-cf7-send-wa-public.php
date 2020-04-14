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
        
        $use_twilio = get_option( 'cf7sendwa_use_twilio', '0' );
        if( $use_twilio == '1' ) {
            $this->use_twilio = true;
            $this->twilio_sid = get_option( 'cf7sendwa_twilio_sid', '' );
            $this->twilio_token = get_option( 'cf7sendwa_twilio_token', '' );
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
        if( get_option( 'cf7sendwa_disablemail', '0' ) == '1' && !empty( $this->numbers ) ) {
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
			
			var the_phone = cf7wa_numbers[ the_id ];
            <?php if( $this->use_twilio ): ?>
                $( '.wpcf7-response-output' ).css( 'display', 'none' );
                $.ajax({
                    url: cf7wa_ajaxurl,
                    type: 'POST',
                    data: { 
                        'action': 'send_twilio', 'to_number': the_phone, 
                        'message': the_text, 'security': cf7wa_security 
                    },
                    success: function( response ) {
                        $( '.wpcf7-response-output' ).css( 'display', 'block' );
                    }
                });
            <?php else: ?>
            the_text = window.encodeURIComponent( the_text );
			var url = 'https://api.whatsapp.com/send?phone=' + the_phone + '&text=' + the_text;
			var isSafari = !!navigator.userAgent.match(/Version\/[\d\.]+.*Safari/);
			var iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
			if( isSafari && iOS ) {
				location = url;
			} else {
				window.open( url, '_blank' );
			}			
            <?php endif; ?>
		}
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
    
    /*
	 * Send message to Twilio API
	 * @since	0.4.2
	 * @access	public
	 *
	 */
    public function send_twilio() {
        check_ajax_referer( 'cf7sendwa-twilio-action', 'security' );
        $twilio = new \Twilio\Rest\Client( $this->twilio_sid, $this->twilio_token );
        $inputs = array(
            "body" => $_POST['message'],
            "from" => "whatsapp:+" . get_option( 'cf7sendwa_twilio_from', '14155238886' )
        );
        if( !empty( $this->attachments ) ) {
	        $inputs[ 'mediaUrl' ] = $this->attachments;
        }
        $message = $twilio->messages
                 ->create(
                    "whatsapp:+" . $_POST['to_number'],
                    $inputs
                 );
        wp_die();
    }

	/**
	 * Prepare attachments
	 * @since 0.5.0
	 * @access public
	 */     
    public function prepare_attachments( $contact_form ) {
	    
	    if( $this->use_twilio ) {
		
		    $attachments = $this->get_attachments( $contact_form );
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
    
}
