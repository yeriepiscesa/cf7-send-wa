<div id="cf7sendwa-quickshop-container">
	<?php 
	if( $atts['products'] != '' ) {
		?><div id="quickshop-products"></div><?php
	} else {
		if( is_array( $product_categories ) && count( $product_categories ) > 1 ) {
			
			if( $atts['filter'] == 'yes' ):
			    ob_start();
                ?>
                <div class="cf7sendwa-woo-filter sp-flex-content">
                    <div class="filter-block sp-flex-first width-50">
                        <select id="cf7sendwa_woo_cat_filter" class="cf7sendwa-woo-categories">
                            <option value="">Category</option>
                            <?php foreach( $product_categories as $cat ): ?>
                            <option value="<?php echo $cat['slug']; ?>"><?php echo $cat['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-block sp-flex-last width-50">
                        <input type="text" id="cf7sendwa_woo_text_filter" placeholder="type to search">
                    </div>
                </div><?php
                $product_filter_block = ob_get_contents();
                ob_end_clean();             
                echo apply_filters( 'cf7sendwa_quickshop_product_filter', $product_filter_block, $product_categories );;
			endif;
			
			foreach( $product_categories as $cat ) {
				echo '<div id="cat-' . $cat['slug'] . '" class="product-cat-container">';
				echo 	'<h3 class="category-title">' . $cat['name'] . '</h3>';
				echo '</div>';
			}				
		} else {
			if( $atts['filter'] == 'yes' ):
				?><div class="cf7sendwa-woo-filter grid-100">
					<div class="filter-block">
						<input type="text" id="cf7sendwa_woo_text_filter" placeholder="type to search">
					</div>
				</div><?php
			endif;
			$cat = $product_categories[0];
			echo '<div id="cat-' . $cat['slug'] . '" class="product-cat-container">';
			echo '</div>';
		}
	}
	?>
	<div id="cf7sendwa-quickshop-unsticky-spot" style="display:none;"></div>
	<div data-bind="template:{name:'product-detail', data: viewdetail}"></div>
	<script type="text/html" id="product-detail">
		<div id="cf7sendwa-product-detail" class="product-item-detail modal" style="max-width: 1000px; padding:0px;">
		    <div class="sp-product-detail sp-flex-content">
		        <div class="detail-image sp-flex-first width-50 fotorama" data-bind="html:images">
		        </div>
		        <div class="detail-content sp-flex-last width-50">
                    <div class="product-item-detail-desc">
                        <h2 data-bind="text: title"></h2>
                        <div data-bind="html: price"></div>
                        <div class="cf7sendwa-product-sku" data-bind="html:sku"></div>
                        <div data-bind="html: excerpt" class="product-item-description"></div>
                        <div data-bind="html: attributes"></div>
                        <span data-bind="text: stock" class="stock-status"></span>
                        <div data-bind="html: description"></div>    
                    </div>
		        </div>
		    </div>    
		</div>
	</script>	
	
</div>
