<div id="cf7sendwa-quickshop-container">
	<?php 
	if( $atts['products'] != '' ) {
		?><div id="quickshop-products"></div><?php
	} else {
		if( is_array( $product_categories ) && count( $product_categories ) > 1 ) {
			
			if( $atts['filter'] == 'yes' ):
			?><div class="cf7sendwa-woo-filter grid-100 grid-parent">
				<div class="filter-block grid-50">
					<select id="cf7sendwa_woo_cat_filter" class="cf7sendwa-woo-categories">
						<option value="">Category</option>
						<?php foreach( $product_categories as $cat ): ?>
						<option value="<?php echo $cat['slug']; ?>"><?php echo $cat['name']; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="filter-block grid-50">
					<input type="text" id="cf7sendwa_woo_text_filter" placeholder="type to search">
				</div>
			</div><?php
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
</div>