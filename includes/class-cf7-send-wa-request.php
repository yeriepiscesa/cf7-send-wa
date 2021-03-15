<?php
class Cf7_Send_Wa_Request {
	
    public static $multisite_subdir = false;
    public static $multisite_path = null;
    
    private $url_path = array();

	public function __construct() {
		$this->dispatch_url();	
		add_action( 'init', array( $this, 'render_media' ) );
	}
	
	private function dispatch_url() {
        
		$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 
                "https" : "http") . "://" . $_SERVER['HTTP_HOST'] .  
                $_SERVER['REQUEST_URI']; 
		$url_parsed = wp_parse_url( $url );		
		if( isset( $url_parsed['path'] ) && $url_parsed['path'] != '/' ) {            
			$_paths = explode( '/', $url_parsed['path'] );            
            $path_prefix = '';
            if( is_multisite() ) {                
                if( defined( 'SUBDOMAIN_INSTALL' ) && !SUBDOMAIN_INSTALL ) {
                    $path_prefix = stripslashes( str_replace( '/', '', get_blog_details()->path ) );
                    self::$multisite_subdir = true;
                    self::$multisite_path = $path_prefix;
                }
            }          
            foreach( $_paths as $p ) {
                if( $p != '' ) {
                    if( $path_prefix != '' && $path_prefix == $p )
                        continue;
                    array_push( $this->url_path, $p ); 
                }
            }            
		} 		       
	}		
	
	public function render_media() {
        if( !empty( $this->url_path ) && $this->url_path[0] == 'cf7sendwa-media' ) {
            if( isset( $this->url_path[1] ) ) {
                $key = $this->url_path[1];       
                
                global $wpdb;
                $sql = $wpdb->prepare( "
                	select a.post_id, b.post_mime_type, b.guid  
                	from {$wpdb->postmeta} a 
                	left join {$wpdb->posts} b on a.post_id=b.ID  
                	where a.meta_key='cf7sendwa_cf7_uniqid' and a.meta_value=%s", 
                $key );
 				$row = $wpdb->get_row( $sql );  
 				if( $row ) {
	 				header( 'Content-Type: ' . $row->post_mime_type );
                    echo file_get_contents( $row->guid );
	                die();	 				
 				}
            }
        }
    }	
}
new Cf7_Send_Wa_Request();