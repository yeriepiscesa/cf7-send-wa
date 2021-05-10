<?php
function cf7sendwa_quickshop_item_qty( $product, $args ) {
    ob_start();
    include plugin_dir_path( __FILE__ ) . 'woo-item-qty.php';
    $html = ob_get_contents();
    ob_end_clean(); 
    return $html;
}
?>
<div class="product-items page-<?php echo $products['page']; ?>" data-total="<?php echo $products['total'] ?>">
	<?php foreach( $products['results'] as $product ): ?>
		<div class="product-item prd-<?php echo $product['prop']['id']; ?><?php echo isset( $args['is_current_product'] ) && $args['is_current_product'] =='yes' ? ' current-single':''; ?>">						
			<div class="sp-mobile-flex-content item-block">
				<a href="#" class="woo-link-detail"><img src="<?php echo $product['prop']['images'][0]['src']; ?>" width="100" align="left" border="0"></a>
				<div class="product-item-info">
                    <div class="product-item-heading-wrap">                        
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
                    <?php echo cf7sendwa_quickshop_item_qty( $product, $args ); ?>
				</div>
			</div>			
			
			<textarea style="display:none;" class="woo-product-prop"><?php echo json_encode($product['prop']); ?></textarea>
		</div>

		<?php if( $product['prop']['type'] == 'variable' ): 
				$product_weight = $product['prop']['weight'];
				?>
			<?php foreach( $product['prop']['variations'] as $prd ): ?>
				<div class="product-item variations var-<?php echo $product['prop']['id']; ?>">
					<div class="item-block">
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
					<div class="item-block item-price">
						<div class="cf7sendwa-div-wrapper">
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
								style="display: inline-block; margin-top: 0px;">
						</div>
						
						<?php if( !isset( $args['is_current_product'] ) ) : ?>
						<div class="cf7sendwa-div-wrapper">
							<div class="item-subtotal"></div>
						</div>
						<?php endif; ?>
						
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
		}
	echo '</div>';
}
?>