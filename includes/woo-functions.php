<?php
/**
 * convert price format
 * @since 0.6.0
 */	
function cf7sendwa_wc_price( $price, $args=array() ) {
	$args = apply_filters(
		'cf7sendwa_wc_price_args',
		wp_parse_args(
		  $args,
		  array(
		    'ex_tax_label'       => false,
		    'currency'           => '',
		    'decimal_separator'  => wc_get_price_decimal_separator(),
		    'thousand_separator' => wc_get_price_thousand_separator(),
		    'decimals'           => wc_get_price_decimals(),
		    'price_format'       => get_woocommerce_price_format(),
		  )
		)
	);
	
	$unformatted_price = $price;
	$negative          = $price < 0;
	$price             = apply_filters( 'raw_woocommerce_price', floatval( $negative ? $price * -1 : $price ) );
	$price             = apply_filters( 'formatted_woocommerce_price', number_format( $price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] ), $price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] );
	
	if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $args['decimals'] > 0 ) {
	$price = wc_trim_zeros( $price );
	}
	
	$formatted_price = ( $negative ? '-' : '' ) . sprintf( $args['price_format'],  get_woocommerce_currency_symbol( $args['currency'] ), $price );
	$return          = $formatted_price;
	
	if ( $args['ex_tax_label'] && wc_tax_enabled() ) {
		$return .= WC()->countries->ex_tax_or_vat();
	}	
	return apply_filters( 'cf7sendwa_wc_price', $return, $price, $args, $unformatted_price );	
	return $price;
}	

/**
 * Get Product Categories
 * @since 0.9.3
 */	
function cf7sendwa_woo_list_categories( $category ) {
	$product_categories = array();
	if( $category != '' ) {
		$categories = explode(",",$category);
		foreach( $categories as $cat ){
			$_term = get_term_by( 'slug', $cat, 'product_cat' );
			array_push( $product_categories, $_term );
		}
	} else {
	    $orderby = 'name';
	    $order = 'asc';
	    $hide_empty = true ;
	    $cat_args = array(
	        'orderby'    => $orderby,
	        'order'      => $order,
	        'hide_empty' => $hide_empty,
	    );
	    $product_categories = get_terms( 'product_cat', $cat_args );    
    }
    return $product_categories;
}


/**
 * Get Cart Items
 * @since 0.6.0
 */	
function cf7sendwa_woo_get_cart_items() {
	$return = array(
		'items' => array(),
		'coupons' => array(),
		'subtotal' => 0,
	);
	
    $cart_items = WC()->cart->get_cart();
    if( !empty( $cart_items ) ) {		
		foreach ( $cart_items as $key => $item ) {	
            $product = new WC_product( $item['product_id'] );
            $product_id = $product->get_id();
            $variation_id = null;
            $product_var = null;
            $product_type = $product->get_type();
            $name = $product->get_name();        
            $item_meta = [];
            if( isset( $item['variation_id'] ) && $item['variation_id'] != '' ) {
                $variation = new WC_Product_Variation( $item['variation_id'] );
                $product_var = $item['variation'];
                $product_type = $variation->get_type();
                foreach( $product_var as $k => $v ) {
                    $_k = str_replace('attribute_', '', $k);
                    $_term = get_term_by( 'slug', $v, $_k );
                    if( $_term ) {
                        $label = wc_attribute_label( $_term->taxonomy, $product );
                        array_push( $item_meta, [
                            'key' => $label,
                            'value' => $_term->name,
                        ] );
                    } else {
                        $label = wc_attribute_label( $_k, $product );
                        array_push( $item_meta, [
                            'key' => $label,
                            'value' => $v
                        ] );
                    }
                }
                $variation_id = $variation->get_id();
                $name = $variation->get_title();
                $weight = $variation->get_weight();
                $sku = $variation->get_sku();
                $price = $variation->get_price();
                $price_html = $variation->get_price_html();
                $attributes = $item['variation'];
            } else {
                $weight = $product->get_weight();    
                $sku = $product->get_sku();
                $price = $product->get_price();
                $price_html = $product->get_price_html();
            }
            array_push( $return['items'], array(
                'product_id' => $product_id,
                'product_type' => $product_type,
                'variation_id' => $variation_id,
                'name' => $name,
                'sku' => $sku,
                'weight' => $weight,
                'price' => $price,
                'quantity' => $item['quantity'],
                'product_var' => $product_var,
                'variations' => $item_meta			
            ) );				
		}		
	}
	
	$subtotal = WC()->cart->subtotal;
	if( $subtotal > 0 ) {
		$return['subtotal'] = $subtotal;
	}
	
	$coupons = WC()->cart->coupon_discount_totals;
	if( !empty( $coupons ) ) {
		foreach ( $coupons as $code => $amount ){
			array_push( $return['coupons'], array(
				'code' => $code,
				'amount' => $amount,
			) );
		}	
	}
	return $return;
	
}

/**
 * Get Cart Shippings
 * @since 0.6.0
 */	
function cf7sendwa_woo_get_shippings(){

	$customer = WC()->session->get( 'customer' );	
	$cart_totals = WC()->session->get( 'cart_totals' );
	$shipping_total = $cart_totals['shipping_total'];		
	$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );

	$return = array(
		'lines' => array(),
		'address' => null,
		'total' => $shipping_total
	);
	
	$shipping_dest = null;
	$package_id = '0';
	if ( WC()->session->__isset( 'shipping_for_package_'.$package_id ) ) {
		$pckg = WC()->session->get( 'shipping_for_package_'.$package_id );
		foreach( $pckg['rates'] as $shipping_rate_id => $shipping_rate ){
			if( in_array( $shipping_rate_id, $chosen_shipping_methods ) ) {
	            $label_name  = $shipping_rate->get_label();
	            $cost = $shipping_rate->get_cost();
	            $tax_cost    = $shipping_rate->get_shipping_tax();
	            array_push( $return['lines'], array(
		            'label' => $label_name,
		            'cost' => $cost,
		            'tax_cost' => $tax_cost
	            ) );
	            $shipping_dest = $package['destination'];
			}
		}
	}
	$_addr = '';
	$address_parts = array(
		'city' => '',
		'address_2' => '',
		'postcode' => '',
	);
	if( !is_null( $shipping_dest ) ) {
		$_addr = $shipping_dest['city'];
		if( $_addr != '' ) $_addr .= ', ';
		$_addr .= $shipping_dest['address_2'];
		if( $_addr != '' ) $_addr .= ' ';
		$_addr .= $shipping_dest['postcode'];
	} else {
		if( isset( $customer['city'] ) && $customer['city'] != '' ) {
			$address_parts['city'] = $customer['city'];
			$_addr = $customer['city'];
		}
		if( isset( $customer['address_2'] ) && $customer['address_2'] != '' ) {
			$address_parts['address_2'] = $customer['address_2'];
			if( $_addr != '' ) $_addr .= ', ';
			$_addr .= $customer['address_2'];
		}
		if( isset( $customer['postcode'] ) && $customer['postcode'] != '' ) {
			$address_parts['postcode'] = $customer['postcode'];
			if( $_addr != '' ) $_addr .= ' ';
			$_addr .= $customer['postcode'];
		}
	}
	$return['address'] = $_addr;
	$return['address_parts'] = $address_parts;

	return $return;
	
}

/**
 * Create Woocommerce Order
 * @since 0.6.0
 */	
function cf7sendwa_woo_create_order( $customer=null, $note=null, $posted_data=null ) {	
	if( is_null( $customer ) ) {
		return false;
	}	
	
	$cust = WC()->session->get( 'customer' );
	$cart_totals = WC()->session->get( 'cart_totals' );
	
	$checkout = WC_Checkout::instance();
	$order = new WC_Order();
	$order->set_created_via( 'contact-form-7' );
	
	$order->set_address( $customer, 'billing' );
    $order->set_address( $customer, 'shipping' );
	
    $order_vat_exempt = $cust['is_vat_exempt'] ? 'yes' : 'no';
    $order->add_meta_data( 'is_vat_exempt', $order_vat_exempt );
    
    $order->set_currency( get_woocommerce_currency() );
    $order->set_prices_include_tax( 'yes' === get_option( 'woocommerce_prices_include_tax' ) );
    
    $order->set_customer_ip_address( WC_Geolocation::get_ip_address() );
    $order->set_customer_user_agent( wc_get_user_agent() );
	if( !is_null( $note ) && $note != '' ) {
	    $order->set_customer_note( $note );
	}
	
	$carts = WC()->session->get('cart');
	if( is_array( $carts ) && !empty( $carts ) ) {
		foreach( $carts as $key => $item ) {
			$prd = wc_get_product( $item['product_id'] );
			$qty = $item['quantity'];
			$args = array();
			if( $item['variation_id'] != 0 ) {
				$args['variation'] = $item['variation'];
				$prd = wc_get_product( $item['variation_id'] );
			}
			$order->add_product( $prd, $qty, $args );						
		}
	}
    
    $order->set_discount_total( $cart_totals['discount_total'] );
    $order->set_discount_tax( $cart_totals['discount_tax'] );
    $order->set_cart_tax( $cart_totals['cart_contents_tax'] );
    
	/* shipping */    
	$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
	$package_id = '0';
	if ( WC()->session->__isset( 'shipping_for_package_'.$package_id ) ) {
		$pckg = WC()->session->get( 'shipping_for_package_'.$package_id );
		foreach( $pckg['rates'] as $shipping_rate_id => $shipping_rate ){
			if( in_array( $shipping_rate_id, $chosen_shipping_methods ) ) {
				$item                     = new WC_Order_Item_Shipping();
                $item->legacy_package_key = $package_key; // @deprecated For legacy actions.
                $item->set_props(
                    array(
                        'method_title' => $shipping_rate->get_label(),
                        'method_id'    => $shipping_rate->get_method_id(),
                        'instance_id'  => $shipping_rate->get_instance_id(),
                        'total'        => wc_format_decimal( $shipping_rate->get_cost() ),
                        'taxes'        => array(
                            'total' => $shipping_rate->get_taxes(),
                        ),
                    )
                );		            
	            foreach ( $shipping_rate->get_meta_data() as $key => $value ) {
                    $item->add_meta_data( $key, $value, true );
                }
                $order->add_item( $item );
			}
		}
	}
	$order->set_shipping_total( $cart_totals['shipping_total'] );
	$order->set_shipping_tax( $cart_totals['shipping_tax'] );
    
    $order->calculate_totals();	
	
	if( !is_null( $posted_data ) && !empty( $posted_data ) ) {
		foreach( $posted_data as $key=>$val ){
			$order->add_meta_data( $key, $val );
		}
	}
			
	do_action( 'cf7sendwa_before_woo_order_save', $obj_order, $posted_data );
    $order_id = $order->save();
    if( $order_id && isset( $cust['id'] ) ) {
		update_post_meta( $order_id, '_customer_user', $cust['id'] );	    
    }

	if( ! apply_filters( 'cf7sendwa_disable_woocommerce_email', false ) ) {
	    $mail_order = new WC_Email_New_Order();
	    $mail_order->trigger( $order_id, $order );
	    if( isset($customer['email']) && is_email( $customer['email'] ) ) {
		    $mail = new WC_Email_Customer_Invoice();
		    $mail->trigger( $order_id, $order );
	    }
    }
    return $order;
}