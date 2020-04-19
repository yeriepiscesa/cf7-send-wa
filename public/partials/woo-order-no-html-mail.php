<?php 
$txt = '';
if( !is_null( $this->woo_cart ) ) {
	foreach( $this->woo_cart['items'] as $item ){
	    if( $txt != '' ) $txt .= "\n";
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
	$txt .= "\n";
	$txt .= "Subtotal " . cf7sendwa_wc_price( $this->woo_cart['subtotal'] );
	if( !empty( $this->woo_cart['coupons']  ) ){
		foreach ( $this->woo_cart['coupons'] as $c ){
			$txt .= "\n";
			$txt .= __( 'Coupon: ', 'woocommerce' ) . $c['code']. cf7sendwa_wc_price( $c['amount'] );
		}		
	}
}

if( $this->woo_shippings['total'] > 0 ) {
	$txt .= "\n";
	$txt .= "Shipping";
	foreach( $this->woo_shippings['lines'] as $s ) {
		$txt .= "\n";
		$txt .= $s['label'] . ' ' . cf7sendwa_wc_price( $s['cost']+$s['tax_cost'] );
	}
	$txt .= "\n";
	$txt .= $this->woo_shippings['address'];
}
$txt .= "\n";
$txt .= "TOTAL " . cf7sendwa_wc_price( WC()->cart->total );
echo $txt;