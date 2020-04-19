<?php 
$txt = '';
if( !is_null( $this->woo_cart ) ) {
	foreach( $this->woo_cart['items'] as $item ){
	    if( $txt != '' ) $txt .= "<br>";
		$txt .= $item['name'];
		if( !empty( $item['variations'] ) ) {
			$txt .= ' - ';
			$variations = '';
			foreach( $item['variations'] as $v ) {
				if( $variations != '' ) $variations .= ', ';
				$variations .= $v['key'].': '.$v['value'];
			}
			$txt .= $variations;
		}
		$txt .= ' @ ' . cf7sendwa_wc_price( $item['price'] );
		$txt .= ' x ' . $item['quantity'] . ' => ' . 
				cf7sendwa_wc_price( $item['price']*$item['quantity'] );
	}
	$txt .= "<br>";
	$txt .= "<strong>Subtotal</strong> " . cf7sendwa_wc_price( $this->woo_cart['subtotal'] );
	if( !empty( $this->woo_cart['coupons']  ) ){
		foreach ( $this->woo_cart['coupons'] as $c ){
			$txt .= "<br>";
			$txt .= '<strong>' . __( 'Coupon: ', 'woocommerce' ) . $c['code'] . '</strong> ' . cf7sendwa_wc_price( $c['amount'] );
		}		
	}
}

if( $this->woo_shippings['total'] > 0 ) {
	$txt .= "<br>";
	$txt .= "<strong>Shipping</strong>";
	foreach( $this->woo_shippings['lines'] as $s ) {
		$txt .= "<br>";
		$txt .= $s['label'] . ' ' . cf7sendwa_wc_price( $s['cost']+$s['tax_cost'] );
	}
	$txt .= "<br>";
	$txt .= $this->woo_shippings['address'];
}
$txt .= "<br>";
$txt .= "<strong>TOTAL</strong> " . cf7sendwa_wc_price( WC()->cart->total );
echo $txt;