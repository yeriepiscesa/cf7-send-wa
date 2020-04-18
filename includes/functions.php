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
 * Get Cart Items
 * @since 0.6.0
 */	
function cf7sendwa_woo_get_cart_items() {
	$return = array(
		'items' => array(),
		'coupons' => array(),
		'subtotal' => 0,
	);
	foreach ( WC()->cart->get_cart() as $key => $item ) {
        $product = new WC_product( $item['product_id'] );
        $name = $product->get_name();
        $item_meta = [];
        if( isset( $item['variation_id'] ) && $item['variation_id'] != '' ) {
            $variation = new WC_Product_Variation( $item['variation_id'] );
            $product_var = $item['variation'];
            foreach( $product_var as $k => $v ) {
                $_k = str_replace('attribute_', '', $k);
                $_term = get_term_by( 'slug', $v, $_k );
                if( $_term ) {
                    $label = wc_attribute_label( $_term->taxonomy, $product );
                    array_push( $item_meta, [
                        'key' => $label,
                        'value' => $_term->name
                    ] );
                } else {
                    $label = wc_attribute_label( $_k, $product );
                    array_push( $item_meta, [
                        'key' => $label,
                        'value' => $v
                    ] );
                }
            }
            $name = $variation->get_title();
            $weight = $variation->get_weight();
            $price = $variation->get_price();
            $price_html = $variation->get_price_html();
            $attributes = $item['variation'];
        } else {
            $weight = $product->get_weight();    
            $price = $product->get_price();
            $price_html = $product->get_price_html();
        }
		array_push( $return['items'], array(
			'name' => $name,
			'price' => $price,
			'quantity' => $item['quantity'],
			'variations' => $item_meta			
		) );
	}
	
	$subtotal = WC()->cart->subtotal;
	if( $subtotal > 0 ) {
		$return['subtotal'] = $subtotal;
	}
	
	$coupons = WC()->cart->coupon_discount_totals;
	foreach ( $coupons as $code => $amount ){
		array_push( $return['coupons'], array(
			'code' => $code,
			'amount' => $amount,
		) );
	}	
	return $return;
	
}

/**
 * Get Cart Shippings
 * @since 0.6.0
 */	
function cf7sendwa_woo_get_shippings(){

	$customer = WC()->session->get( 'customer' );	
	$shipping_total = WC()->cart->shipping_total;	
	$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
	$shipping_packages = WC()->cart->get_shipping_packages();

	$return = array(
		'lines' => array(),
		'address' => null,
		'total' => $shipping_total
	);

	foreach( $shipping_packages as $package_id => $package ) {
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
				}
			}
		}
	}
	$_addr = '';
	if( isset( $customer['city'] ) && $customer['city'] != '' ) {
		$_addr = $customer['city'];
	}
	if( isset( $customer['address_2'] ) && $customer['address_2'] != '' ) {
		if( $_addr != '' ) $_addr .= ', ';
		$_addr .= $customer['address_2'];
	}
	if( isset( $customer['postcode'] ) && $customer['postcode'] != '' ) {
		if( $_addr != '' ) $_addr .= ' ';
		$_addr .= $customer['postcode'];
	}
	$return['address'] = $_addr;

	return $return;
	
}