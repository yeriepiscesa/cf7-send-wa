<?php
$the_style = '';
if( $atts['max-width'] != '' ) {
	$the_style = ' style="width:' .$atts['max-width']. ';"';
}
?>
<div id="cf7sendwa-checkout"<?php echo $the_style ?>>
	<div class="cf7sendwa-quickshop-checkout-container">
		<div class="grid-100 parent-grid cf7sendwa-quickshop-checkout">
			<table width="100%" border="0" class="table-cart" style="overflow-x:auto;">
				<thead>
					<tr>
						<td class="cart-item">Total</td>
						<td class="cart-nominal" data-bind="html:price_total">Rp. 0</td>
					</tr>
				</thead>
				<tbody data-bind="foreach:items">
					<tr>
						<td width="60%" class="cart-item">
							<div data-bind="text:title"></div>
							<div data-bind="text:subtitle"></div>
							<span data-bind="text:qty"></span>x 
							<span data-bind="html:price_html"></span>
						</td>
						<td width="40%" class="cart-nominal" data-bind="html:subtotal_html"></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>