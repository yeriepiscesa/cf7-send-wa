<div class="product-items" data-total="<?php echo $products['total'] ?>" data-page="<?php echo $products['page'] ?>">
	<?php foreach( $products['results'] as $product ): 
				$grid1 = '60';
				$grid2 = '40';
				if( $product['prop']['type'] == 'variable' ) {
					$grid1 = '70';
					$grid2 = '30';
				} ?>
		<div class="product-item prd-<?php echo $product['prop']['id']; ?>">			
			<div class="grid-<?php echo $grid1 ?> tablet-grid-100 mobile-grid-100 item-block">
				<img src="<?php echo $product['prop']['images'][0]['src']; ?>" width="120" align="left">
				<div class="product-item-info">
					<h4><?php echo $product['name']; ?></h4>					
					<?php
					$quickshop_excerpt = get_option( 'quickshop_excerpt', '0' );						
					if( $quickshop_excerpt == '1' ) {
						echo '<div class="product-excerpt">' . $product['prop']['short_description'] . '</div>';	
					}
					?>
					<?php echo $product['prop']['price_html'] ?>
					<span class="stock-status <?php echo $product['prop']['stock_status']; ?>"><?php echo $product['prop']['stock_status']; ?></span>
				</div>
			</div>
			<div class="grid-<?php echo $grid2 ?> tablet-grid-100 mobile-grid-100 grid-parent item-block item-price">
				<?php if( $product['prop']['type'] != 'variable' ): ?>
				<div class="grid-50 mobile-grid-50 tablet-grid-50">
					<input type="number" name="item_qty" step="1" readonly="readonly" value="0" 
						data-stock="<?php echo $product['prop']['stock_status'] ?>"
						data-price="<?php echo $product['prop']['price'] ?>" 
						data-product_type="<?php echo $product['prop']['type'] ?>"
						data-product_id="<?php echo $product['prop']['id'] ?>"
						data-variation_id="<?php echo $product['prop']['variation_id'] ?>"
						class="input-text qty text" id="prd-qty-<?php echo $product['prop']['id']; ?>"
						style="display: inline-block; margin-top: 0px; width: 40%; border: 0px;">
				</div>
				<div class="grid-50 mobile-grid-50 tablet-grid-50">
					<div class="item-subtotal"></div>
				</div>
				<?php else: ?>
				<a class="button variant-option-button" data-var-id="<?php echo $product['prop']['id']; ?>" href="javascript:void(0);">
					<span class="angle-down"><i class="fa fa-angle-down"></i> <?php echo __( 'Select Options', 'cf7sendwa' ) ?></span>
					<span class="angle-up"><i class="fa fa-angle-up"></i> <?php echo __( 'Hide Options', 'cf7sendwa' ) ?></span>
				</a>
				<?php endif; ?>
			</div>
		</div>

		<?php if( $product['prop']['type'] == 'variable' ): ?>
			<?php foreach( $product['prop']['variations'] as $prd ): ?>
				<div class="product-item variations var-<?php echo $product['prop']['id']; ?>">
					<div class="grid-50 tablet-grid-100 mobile-grid-100 item-block">
						<div class="product-item-info">
							<h4><?php echo $prd['name']; ?></h4>
							<?php echo $prd['prop']['price_html'] ?>
							<span class="stock-status <?php echo $prd['prop']['stock_status']; ?>"><?php echo $prd['prop']['stock_status']; ?></span>
						</div>
					</div>
					<div class="grid-50 tablet-grid-100 mobile-grid-100 grid-parent item-block item-price">
						<div class="grid-50 mobile-grid-50 tablet-grid-50">
							<?php
							$var_txt = '';
							if(!empty($prd['prop']['pa_terms'])) {
								foreach($prd['prop']['pa_terms'] as $t){
									foreach( $t as $k=>$v ) {
										if( $var_txt != '' ) { $var_txt .= '-'; }
										$var_txt .= $v;
									}
								}
							}	
							$qty_id =  'prd-qty-'.$prd['prop']['variation_id'].'-'.$var_txt;
							?>
							<input type="number" name="item_qty" step="1" readonly="readonly" value="0" 
								data-stock="<?php echo $prd['prop']['stock_status'] ?>"
								data-price="<?php echo $prd['prop']['price'] ?>" 
								data-product_type="<?php echo $prd['prop']['type'] ?>"
								data-product_id="<?php echo $prd['prop']['id'] ?>"
								data-variation_id="<?php echo $prd['prop']['variation_id'] ?>"
								data-pa="<?php echo urlencode(json_encode( $prd['prop']['pa_terms'] )); ?>"
								class="input-text qty text" id="<?php echo $qty_id; ?>"
								style="display: inline-block; margin-top: 0px; width: 40%; border: 0px;">
						</div>
						<div class="grid-50 mobile-grid-50 tablet-grid-50">
							<div class="item-subtotal"></div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>		
		
	<?php endforeach; ?>
</div>