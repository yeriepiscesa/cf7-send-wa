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
                               value="<?= $whatsapp_number ?>">
                        <p class="description">
                            Phone number must include the country code (eg. 62 for Indonesia)
                        </p>
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
                        <input type="checkbox" id="cf7sendwa_disablemail"<?= $checked1 ?>  
                               name="disable_send_mail" value="1">
                    </td>
				</tr>
			</tbody>
		</table>
			
        <h3>Woocommerce Integration</h3>
        <table class="form-table">
	        <tbody>
		        <tr>
			        <th scope="row"><label>Checkout Form</label></th>
			        <td>
				        <select class="cf7-checkout-form" name="woo_checkout" style="width: 300px;">
				        <?php 
					    $cf7_woo = get_option( 'cf7sendwa_woo_checkout', '' );    
					    if( $cf7_woo != '' ) { 
					        $__p = get_post( $cf7_woo );
					        echo '<option value="'. $__p->ID .'" selected="selected">' . $__p->post_title . '</option>'; 
					    } ?>
			        	</select>
						<p class="description">Contact Form which contain [cf7sendwa_woo_checkout] tag</p>
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
                        <input type="checkbox" id="cf7sendwa_fullcart"<?= $checked3 ?>  
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
                        <input type="checkbox" id="cf7sendwa_requireshipping"<?= $checked2 ?>  
                               name="require_shipping" value="1">
                        <p style="display:inline" class="description">When checked, shippable cart must have shipping method</p>
			        </td>
		        </tr>
	        </tbody>
        </table>
        
        <h3>Twilio Integration</h3>
        <p>Please create <a href="https://www.twilio.com/try-twilio" target="_blank">Twilio Account</a> to get Account SID &amp; Auth Token.</p>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="cf7sendwa_twilio_sid">Account SID</label></th>
                    <td><input type="text" name="twilio_sid" size="40" 
                               id="cf7sendwa_twilio_sid"
                               value="<?= $twilio_sid ?>"></td>
                </tr>
				<tr>
					<th scope="row"><label for="cf7sendwa_twilio_token">Auth Token</label></th>
                    <td><input type="text" name="twilio_token" size="40" 
                               id="cf7sendwa_twilio_token"
                               value="<?= $twilio_token ?>"></td>
                </tr>
				<tr>
					<th scope="row"><label for="cf7sendwa_use_twilio">Use Twilio</label></th>
					<td>
                        <?php
                        $checked2 = '';
                        if( $use_twilio == '1' ) {
                            $checked2 = ' checked="checked"';
                        }
                        ?>
                        <input type="checkbox" id="cf7sendwa_use_twilio"<?= $checked2 ?>  
                               name="use_twilio" value="1">
                    </td>
				</tr>
				<tr>
					<th scope="row"><label for="cf7sendwa_twilio_from">WhatsApp From</label></th>
                    <td><input type="text" name="twilio_from" size="20" 
                               id="cf7sendwa_twilio_from"
                               value="<?= $twilio_from ?>">
                        <p class="description">
                            Twilio valid number or your own WhatsApps' approved number.
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>		
    </form>
</div>