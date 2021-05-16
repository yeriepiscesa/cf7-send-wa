<?php
$the_style = '';
if( $atts['max-width'] != '' ) {
	$the_style = ' style="width:' .$atts['max-width']. ';"';
}
?>
<div id="cf7sendwa-checkout"<?php echo $the_style ?>>
    <?php 
    $style_height = '';
    if( isset( $atts['max-height'] ) && $atts['max-height'] != '' ) {
        $style_height = ' style="max-height: ' . $atts['max-height'] . ';"';
    }
    ?>
	<div class="grid-100 parent-grid cf7sendwa-quickshop-checkout-container"<?php echo $style_height; ?>>
		<div class="cf7sendwa-quickshop-checkout-header <?php echo $this->current_product_checkout && $this->woo_is_active ? 'single-product':''; ?>">
			<table width="100%" border="0" class="table-cart">
				<thead>
					<?php do_action( 'cf7sendwa_quickshop_before_total_review' ); ?>		
					<tr>
						<td width="60%">Total</td>
						<td width="40%" class="cart-nominal" data-bind="html:price_total">0</td>
					</tr>
					<?php do_action( 'cf7sendwa_quickshop_after_total_review' ); ?>		
				</thead>
			</table>
		</div>
		<?php do_action( 'cf7sendwa_quickshop_before_order_review_list' ); ?>	
		
		<div class="wrap cf7sendwa-quickshop-checkout" <?php echo $this->current_product_checkout && $this->woo_is_active ?'style="display:none;"':''; ?>>
			<table width="100%" border="0" class="table-cart">
				<tbody data-bind="foreach:items">
					<tr>
						<td width="60%" class="cart-item">
							<a class="cf7sendwa-checkout-item-title" href="#" data-bind="click:$parent.gotoItem"><span data-bind="text:title"></span></a>
							<div data-bind="text:subtitle"></div>
							<span data-bind="text:qty"></span>x 
							<span data-bind="html:price_html"></span>
						</td>
						<td width="40%" class="cart-nominal">
							<div data-bind="html:subtotal_html"></div>
							<a class="cf7sendwa-checkout-item-remove" href="#" data-bind="click: $parent.removeItem"><i class="fa fa-window-close"></i> Remove</a>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<?php 
		do_action( 'cf7sendwa_quickshop_after_order_review_list' );
		if( $content != '' ) {
			echo do_shortcode( $content );
		}
		?>
	</div>
</div>