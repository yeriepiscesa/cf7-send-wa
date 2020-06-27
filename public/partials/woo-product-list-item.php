<div class="product-items page-<?php echo $products['page']; ?>" data-total="<?php echo $products['total'] ?>">
	<?php foreach( $products['results'] as $product ): 
				$grid1 = '60';
				$grid2 = '40';
				if( $product['prop']['type'] == 'variable' ) {
					$grid1 = '70';
					$grid2 = '30';
				} ?>
		<div class="product-item prd-<?php echo $product['prop']['id']; ?>">			
			<div class="grid-<?php echo $grid1 ?> tablet-grid-100 mobile-grid-100 item-block">
				<a href="#" class="woo-link-detail"><img src="<?php echo $product['prop']['images'][0]['src']; ?>" width="120" align="left" border="0"></a>
				<div class="product-item-info">
					<h4><a href="#" class="woo-link-detail"><?php echo $product['name']; ?></a></h4>	
					<?php
					if( get_option( 'quickshop_sku', '0' ) == '1' && $product['prop']['sku'] != '' ) {
						echo '<div class="cf7sendwa-product-sku">SKU: ' . $product['prop']['sku'] . '</div>';
					}	
						
					$quickshop_excerpt = get_option( 'quickshop_excerpt', '0' );						
					if( $quickshop_excerpt == '1' ) {
						echo '<div class="product-excerpt">' . $product['prop']['short_description'] . '</div>';	
					}
					?>
					<?php echo $product['prop']['price_html'] ?>
					
					<?php if( get_option( 'quickshop_outofstock', '0' ) == '1' ) : ?>
					<span class="stock-status <?php echo $product['prop']['stock_status']; ?>"><?php echo $product['prop']['stock_status']; ?></span>
					<?php endif; ?>
				</div>
			</div>
			<div class="grid-<?php echo $grid2 ?> tablet-grid-100 mobile-grid-100 grid-parent item-block item-price">
				<?php if( $product['prop']['type'] != 'variable' ): ?>
				<div class="grid-50 mobile-grid-50 tablet-grid-50">
					<?php
					$n_readonly = '';
					if( $args['editableqty'] == 'no' ) {
						$n_readonly = ' readonly="readonly"';
					}						
					?>
					<input type="number" name="item_qty" step="1"<?php echo $n_readonly ?> value="0" 
						data-sku="<?php echo $product['prop']['sku'] ?>" 
						data-stock="<?php echo $product['prop']['stock_status'] ?>"
						data-price="<?php echo $product['prop']['price'] ?>" 
						data-weight="<?php echo $product['prop']['weight'] ?>" 
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
				<button class="button variant-option-button" data-var-id="<?php echo $product['prop']['id']; ?>">
					<span class="angle-down"><i class="fa fa-angle-down"></i> <?php echo __( 'Select Options', 'cf7sendwa' ) ?></span>
					<span class="angle-up"><i class="fa fa-angle-up"></i> <?php echo __( 'Hide Options', 'cf7sendwa' ) ?></span>
				</button>
				<?php endif; ?>
			</div>
			<textarea style="display:none;" class="woo-product-prop"><?php echo json_encode($product['prop']); ?></textarea>
		</div>

		<?php if( $product['prop']['type'] == 'variable' ): 
				$product_weight = $product['prop']['weight'];
				?>
			<?php foreach( $product['prop']['variations'] as $prd ): ?>
				<div class="product-item variations var-<?php echo $product['prop']['id']; ?>">
					<div class="grid-50 tablet-grid-100 mobile-grid-100 item-block">
						<div class="product-item-info">
							<h4><?php echo $prd['name']; ?></h4>
							<?php
							if( get_option( 'quickshop_sku', '0' ) == '1' && $prd['prop']['sku'] != '' ) {
								echo '<div class="cf7sendwa-product-sku"><span class="cf7sendwa-product-sku-label">SKU: </span>' . $prd['prop']['sku'] . '</div>';
							}	
							?>
							<?php echo $prd['prop']['price_html'] ?>
									
							<?php if( get_option( 'quickshop_outofstock', '0' ) == '1' ) : ?>
							<span class="stock-status <?php echo $prd['prop']['stock_status']; ?>"><?php echo $prd['prop']['stock_status']; ?></span>
							<?php endif; ?>
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
                            $var_txt = str_replace( " ", "", $var_txt );
							$qty_id =  'prd-qty-'.$prd['prop']['variation_id'].'-'.$var_txt;
							$n_readonly = '';
							if( $args['editableqty'] == 'no' ) {
								$n_readonly = ' readonly="readonly"';
							}
							$_weight = floatval( $prd['prop']['weight'] );
							if( $_weight <= 0 ) {
								$_weight = $product_weight;
							}
							?>
							<input type="number" name="item_qty" step="1"<?php echo $n_readonly; ?> value="0" 
								data-stock="<?php echo $prd['prop']['stock_status'] ?>"
								data-sku="<?php echo isset($prd['prop']['sku'])?$prd['prop']['sku']:$product['prop']['sku'] ?>" 
								data-price="<?php echo $prd['prop']['price'] ?>" 
								data-weight="<?php echo $_weight ?>"  								 
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
<?php 
if( $args['paging'] != 'disable' ) {	
	echo '<div class="cf7sendwa-quickshop-paging">';
		if( $args['paging'] == 'auto' && $products['pages'] > $products['page'] ) {
			echo '<div class="quickshop-load-more" data-next="' . ($products['page']+1) . '">...</div>';
		} elseif( $args['paging'] == 'loadmore' && $products['pages'] > $products['page'] ) {
			echo '<a id="' . $args['category'] . '-page-' .$products['page']. '" href="#" class="button quickshop-load-more-link" data-next="' . ($products['page']+1) . '">Load More</a>';		
		} elseif( $args['paging'] == 'numbers' ) {
			//echo 'paging here';		
		}
	echo '</div>';
}
?>