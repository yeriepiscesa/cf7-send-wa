<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://solusipress.com
 * @since      0.3.0
 *
 * @package    Cf7_Send_Wa
 * @subpackage Cf7_Send_Wa/admin/partials
 */
?>
<div class="wrap sp-admin-page solusipress-admin-container">
    
    <?php if( $settings_saved ) : ?>
    <div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible"> 
    <p><strong>Settings saved.</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>    
    <?php endif; ?>
    
    <h1 class="wp-heading-inline">CF7 Send to WhatsApp Settings</h1>
	<form method="post" class="sp-cf7sendwa-form">
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="cf7sendwa_number">WhatsApp Number</label></th>
					<td><input type="text" id="cf7sendwa_number" 
                               placeholder="628123456789" 
                               name="whatsapp_number" size="20"
                               value="<?php echo $whatsapp_number; ?>">
                        <p class="description">
                            Phone number must include the country code (eg. 62 for Indonesia)
                        </p>
                    </td>
				</tr>
				<tr>
					<th scope="row"><label for="cf7sendwa_globalform">Global Form</label></th>
					<td>
				        <select class="cf7-checkout-form" name="cf7sendwa_global_form" id="cf7sendwa_globalform" style="width: 300px;">
				        <?php 
					    $cf7_global = get_option( 'cf7sendwa_global_form', '' );    
					    if( $cf7_global != '' ) { 
					        $__p = get_post( $cf7_global );
					        echo '<option value="'. $__p->ID .'" selected="selected">' . $__p->post_title . '</option>'; 
					    } ?>
			        	</select>
			        	<p class="description">
				        	Displayed as floating green WhatsApp button, form will display as pop up when button click.
			        	</p>
			        	<label style="display:block;padding-top:10px;font-weight:600;">Button Position</label>
			        	<select classs="select2" name="cf7sendwa_global_position" style="width:300px; margin-top:10px;">
				        	<?php
					        $cf7_global_position = get_option( 'cf7sendwa_global_position', '' );	
					        $options = array(
						        'bottom-right' => 'Bottom Right',
						        'bottom-left' => 'Bottom Left',
						        'top-right' => 'Top Right',
						        'top-left' => 'Top Left'
					        );	
				        	echo '<option value="">Select options</option>';
					        foreach( $options as $k=>$v ) {
						        $selected = '';
						        if( $cf7_global_position == $k ) {
							        $selected = ' selected="selected"';
						        }
					        	echo '<option value="' . $k . '"' . $selected . '>' . $v . '</option>';	
				        	}
				        	?>
			        	</select>
			        	<label style="display:block;padding-top:10px;font-weight:600;">Button tooltip text</label>
			        	<input style="display:block;margin-top:5px" 
			        		   name="cf7sendwa_global_tooltip" value="<?php echo $cf7sendwa_global_tooltip; ?>" 
			        	       type="text" placeholder="Click to chat">
					</td>
				</tr>
				<tr>
                    <th scope="row"><label for="cf7sendwa_country">Default Country Code</label></th>
                    <td><input type="text" id="cf7sendwa_country" 
                               placeholder="62" 
                               name="default_country" size="3"
                               value="<?php echo $default_country; ?>">
                        <p class="description">
                            Default country code to use, when your user not defined country code on mobile number entry.
                        </p>
                    </td>
                </tr>
		        <tr>
			        <th scope="row"><label>Alternative Numbers</label>
				        <p class="description">
					        If you have multiple numbers like staff or customer service
				        </p>
			        </th>
			        <td>
					    <a href="#" data-bind="click: addChannel"
					       class="page-title-action">Add Channel</a>
				        <div style="width:500px;margin-top:10px;">
					        <table class="wp-list-table widefat fixed striped">
						        <tbody data-bind="foreach: channelItems">
							        <tr>
								        <td style="width:250px"><input type="text" style="width:100%" placeholder="Title" data-bind="value: $data.title"></td>
								        <td style="width:170px"><input type="text" style="width:100%" placeholder="WA Number" data-bind="value: $data.number"></td>
								        <td style="width:80px"><a class="button" data-bind="click: $parent.removeChannel">Remove</a></td>
							        </tr>
						        </tbody>
					        </table>
				        </div>
				        <p class="description">
					        You can use [select_channel field-name] tag to allow guest/user select WA number to send.
				        </p>
				        <input type="hidden" name="cf7sendwa_channel" id="cf7sendwa_channel" value="<?php echo $cf7sendwa_channel; ?>">
			        </td>
		        </tr>
				<tr>
					<th scope="row"><label for="cf7sendwa_disablemail">Disable mail sending</label></th>
					<td>
                        <?php
                        $checked1 = '';
                        if( $disable_mail == '1' ) {
                            $checked1 = ' checked="checked"';
                        }
                        ?>
                        <input type="checkbox" id="cf7sendwa_disablemail"<?php echo $checked1; ?>  
                               name="disable_send_mail" value="1">
                    </td>
				</tr>
		        <tr>
			        <th scope="row"><label for="cf7sendwa_fontawesome">Load Fontawesome</label></th>
			        <td>
				        <?php
                        $checked8 = '';
                        if( $cf7sendwa_fontawesome == '1' ) {
                            $checked8 = ' checked="checked"';
                        }
					    ?>				        
                        <input type="checkbox" id="cf7sendwa_fontawesome"<?php echo $checked8; ?>  
                               name="cf7sendwa_fontawesome" value="1">
                        <p style="display:inline" class="description">Check if your theme doesn't load font awesome</p>
			        </td>
		        </tr>
				<?php do_action( 'cf7sendwa_custom_whatsapp_settings' ); ?>
			</tbody>
		</table>
			
        <h3>Woocommerce Integration</h3>
        <table class="form-table">
	        <tbody>
		        <tr>
			        <th scope="row"><label for="cf7sendwa_woo_form">Checkout Form</label></th>
			        <td>
				        <select class="cf7-checkout-form" name="woo_checkout" id="cf7sendwa_woo_form" style="width: 300px;">
				        <?php 
					    if( $woo_checkout != '' ) { 
					        $__p = get_post( $woo_checkout );
					        echo '<option value="'. $__p->ID .'" selected="selected">' . $__p->post_title . '</option>'; 
					    } ?>
			        	</select>
						<p class="description">Contact Form which contain [cf7sendwa_woo_checkout] tag</p>
			        </td>
		        </tr>
		        <tr>
			        <th scope="row"><label for="cf7sendwa_woo_singl_form">Single Product Form</label></th>
			        <td>
				        <select class="cf7-checkout-form" name="woo_single_product"
					        data-bind="selectedOptions: single_product"  
					        id="cf7sendwa_woo_single_product" style="width: 300px;">
				        <?php 
					    if( $woo_single_product != '' ) { 
					        $__p = get_post( $woo_single_product );
					        echo '<option value="'. $__p->ID .'" selected="selected">' . $__p->post_title . '</option>'; 
					    } ?>
			        	</select>			        	
			        	<div data-bind="visible:single_product() != ''" style="padding-top:5px;">
				        	
				        	<label for="cf7sendwa_single_button"><strong>Button Text</strong></label><br>
				        	<input type="text" id="cf7sendwa_single_button" 
                               placeholder="Chat Seller" 
                               name="single_button" size="39"
                               value="<?php echo $single_button; ?>">
                            <br>
                            
                            <?php
	                        $button_hooks = apply_filters( 'cf7sendwa_single_button_locations', array(
		                        'woocommerce_before_add_to_cart_form' => 'Before add to cart form',
		                        'woocommerce_before_add_to_cart_button' => 'Before add to cart button',
		                        'woocommerce_before_add_to_cart_quantity' => 'Before add to cart quantity',
		                        'woocommerce_after_add_to_cart_quantity' => 'After add to cart quantity',
		                        'woocommerce_after_add_to_cart_button' => 'After add to cart button',
		                        'woocommerce_after_add_to_cart_form' => 'After add to cart form',
	                        ) );    
	                        ?>
				        	<label for="cf7sendwa_single_hook"><strong>Hook Location</strong></label><br>
				        	<select class="select2" name="cf7sendwa_single_hook" id="cf7sendwa_single_hook">
					        	<option value="">Select WooCommerce hook</option>
					        	<?php
						        foreach( $button_hooks as $key=>$val ) {
							        $_selected = '';
							        if( $woo_button_hook == $key ) {
								        $_selected = ' selected="selected"';
							        }
							        $opt = '<option value="' . $key . '"' . $_selected . '>' .$val. '</option>';
							        echo $opt;
						        }	
						        ?>
				        	</select>&nbsp;
				        	<?php
	                        $checked_wrapdiv = '';
	                        if( $single_button_wrap_div == '1' ) {
	                            $checked_wrapdiv = ' checked="checked"';
	                        }
					        ?>
				        	<label for="cf7sendwa_single_button_wrap_div">
				        		<input type="checkbox" value="1" name="single_button_wrap_div" id="cf7sendwa_single_button_wrap_div"<?php echo $checked_wrapdiv ?>>
				        		<strong>Wrap with div tag<strong>
				        	</label><br><br>
                            
                            
                            <label for="cf7sendwa_single_greet"><strong>Opening Text</strong><label><br>   
                            <textarea id="cf7sendwa_single_greet" rows="3" cols="60" name="single_product_greet"><?php echo $single_product_greet; ?></textarea><br>
                            <p class="description">You can use these tags for dynamic value: {{product_name}} {{product_sku}}</p>
			        	</div>
			        </td>
		        </tr>		        
		        <tr>
			        <th scope="row"><label>Full Width Cart Totals</label></th>
			        <td>
				        <?php
                        $checked3 = '';
                        if( $full_cart == '1' ) {
                            $checked3 = ' checked="checked"';
                        }
                        ?>
                        <input type="checkbox" id="cf7sendwa_fullcart"<?php echo $checked3; ?>  
                               name="full_cart" value="1">
                        <p style="display:inline" class="description">When checked, Cart totals container will be 100% width (default to 50%)</p>
			        </td>
		        </tr>
		        <tr>
			        <th scope="row"><label>Require Shipping</label></th>
			        <td>
				        <?php
                        $checked2 = '';
                        if( $require_shipping == '1' ) {
                            $checked2 = ' checked="checked"';
                        }
                        ?>
                        <input type="checkbox" id="cf7sendwa_requireshipping"<?php echo $checked2; ?>  
                               name="require_shipping" value="1">
                        <p style="display:inline" class="description">When checked, shippable cart must have shipping method</p>
			        </td>
		        </tr>
		        <tr>
			        <th scope="row"><label>After order redirect to</label></th>
			        <td>
				        <?php
					    $t_checked = '';
					    if( $woo_order_redirect == '' || $woo_order_redirect == 'thankyou' ) {
						    $t_checked = ' checked="checked"';
					    }    					    
					    $p_checked = '';
					    if( $woo_order_redirect == 'payment' ) {
						    $p_checked = ' checked="checked"';
					    }    
					    $n_checked = '';
					    if( $woo_order_redirect == 'none' ) {
						    $n_checked = ' checked="checked"';
					    }    
					    ?>
				        <input type="radio" name="woo_order_redirect" value="thankyou" id="redirect_thankyou"<?php echo $t_checked ?>> 
				        	<label for="redirect_thankyou">Thank You Page</label>
				        &nbsp;&nbsp;&nbsp;
				        <input type="radio" name="woo_order_redirect" value="payment" id="redirect_payment"<?php echo $p_checked ?>> 
				        	<label for="redirect_payment">Payment Page</label>
				        &nbsp;&nbsp;&nbsp;
				        <input type="radio" name="woo_order_redirect" value="none" id="redirect_none"<?php echo $n_checked ?>> 
				        	<label for="redirect_none">Disable</label>
			        </td>
		        </tr>
		        <?php do_action( 'cf7sendwa_custom_woocommerce_settings' ); ?>
	        </tbody>
        </table>

	    <h3>Quick Shop Options</h3>
        <table class="form-table">
	        <tbody>
		        <tr>
			        <th scope="row"><label for="quickshop_excerpt">Show Excerpt</label></th>
			        <td>
				        <?php
                        $checked4 = '';
                        if( $quickshop_excerpt == '1' ) {
                            $checked4 = ' checked="checked"';
                        }
					    ?>				        
                        <input type="checkbox" id="quickshop_excerpt"<?php echo $checked4; ?>  
                               name="quickshop_excerpt" value="1">
                        <p style="display:inline" class="description">Show product's short description</p>       
			        </td>
		        </tr>
		        <tr>
			        <th scope="row"><label for="quickshop_sku">Show SKU</label></th>
			        <td>
				        <?php
                        $checked7 = '';
                        if( $quickshop_sku == '1' ) {
                            $checked7 = ' checked="checked"';
                        }
					    ?>				        
                        <input type="checkbox" id="quickshop_sku"<?php echo $checked7; ?>  
                               name="quickshop_sku" value="1">
                        <p style="display:inline" class="description">Show product's SKU</p>       
			        </td>
		        </tr>
		        <tr>
			        <th scope="row"><label for="quickshop_outofstock">Show Out of Stock</label></th>
			        <td>
				        <?php
                        $checked5 = '';
                        if( $quickshop_outofstock == '1' ) {
                            $checked5 = ' checked="checked"';
                        }
					    ?>				        
                        <input type="checkbox" id="quickshop_outofstock"<?php echo $checked5; ?>  
                               name="quickshop_outofstock" value="1">
                        <p style="display:inline" class="description">When checked, out of stock product will be displayed</p>       
			        </td>
		        </tr>
		        <?php do_action( 'cf7sendwa_custom_quickshop_settings' ); ?>
	        </tbody>
        </table>
        
        <h3>3rd Party Integration</h3>
        <table class="form-table">
	        <tbody>
		        <tr>
			        <th scope="row"><label for="">Provider</label></th>
			        <td>
				        <select name="provider" id="provider" data-bind="value: provider">
					        <option value="">Select your Provider</option>
					        <option value="twilio">Twilio</option>
					        <option value="fonnte">Fonnte</option>
					        <option value="wablas">WABlas</option>
					        <option value="ruangwa">RuangWA</option>
					        <?php do_action( 'cf7sendwa_custom_api_provider_options' ); ?>
				        </select>
			        </td>
		        </tr>
	        </tbody>
        </table>
        
        <div id="cf7sendwa-twilio-settings" data-bind="visible: provider() == 'twilio'">
	        <p>Please create <a href="https://www.twilio.com/try-twilio" target="_blank">Twilio Account</a> to get API Access.</p>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="cf7sendwa_twilio_sid">Account SID</label></th>
	                    <td><input type="text" name="twilio_sid" size="40" 
	                               id="cf7sendwa_twilio_sid"
	                               value="<?php echo $twilio_sid; ?>"></td>
	                </tr>
					<tr>
						<th scope="row"><label for="cf7sendwa_twilio_token">Auth Token</label></th>
	                    <td><input type="text" name="twilio_token" size="40" 
	                               id="cf7sendwa_twilio_token"
	                               value="<?php echo $twilio_token; ?>"></td>
	                </tr>
					<tr>
						<th scope="row"><label for="cf7sendwa_twilio_from">WhatsApp From</label></th>
	                    <td><input type="text" name="twilio_from" size="20" 
	                               id="cf7sendwa_twilio_from"
	                               value="<?php echo $twilio_from; ?>">
	                        <p class="description">
	                            Twilio valid number or your own WhatsApps' approved number.
	                        </p>
	                    </td>
	                </tr>
	            </tbody>
	        </table>
        </div>
        
        <div id="cf7sendwa-fonnte-settings" data-bind="visible: provider() == 'fonnte'">
	        <p>Please create <a href="https://fonnte.com/" target="_blank">Fonnte Account</a> to get API Access.</p>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="cf7sendwa_fonnte_token">Token</label></th>
	                    <td><input type="text" name="fonnte_token" size="40" 
	                               id="cf7sendwa_fonnte_token"
	                               value="<?php echo $fonnte_token; ?>"></td>
	                </tr>
				</tbody>
			</table>
        </div>

        <div id="cf7sendwa-wablas-settings" data-bind="visible: provider() == 'wablas'">
	        <p>Please create <a href="https://wablas.com/" target="_blank">WABLAS Account</a> to get API Access.</p>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="cf7sendwa_wablas_domain">Domain API</label></th>
	                    <td><input type="text" name="wablas_domain" size="40" 
	                               id="cf7sendwa_wablas_domain" placeholder="https://ampel.wablas.com"
	                               value="<?php echo $wablas_domain; ?>"></td>
	                </tr>
					<tr>
						<th scope="row"><label for="cf7sendwa_wablas_token">Token</label></th>
	                    <td><input type="text" name="wablas_token" size="40" 
	                               id="cf7sendwa_wablas_token"
	                               value="<?php echo $wablas_token; ?>"></td>
	                </tr>
				</tbody>
			</table>
        </div>

        <div id="cf7sendwa-ruangwa-settings" data-bind="visible: provider() == 'ruangwa'">
	        <p>Please create <a href="https://ruangwa.com/" target="_blank">RuangWA Account</a> to get API Access.</p>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="cf7sendwa_ruangwa_token">Token</label></th>
	                    <td><input type="text" name="ruangwa_token" size="40" 
	                               id="cf7sendwa_ruangwa_token"
	                               value="<?php echo $ruangwa_token; ?>"></td>
	                </tr>
				</tbody>
			</table>
        </div>
        
        <?php do_action( 'cf7sendwa_custom_api_provider_form' ); ?>
                
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>		
    </form>
</div>