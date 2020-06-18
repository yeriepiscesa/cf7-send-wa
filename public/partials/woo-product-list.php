<div id="cf7sendwa-quickshop-container">
	<?php 
	if( $atts['products'] != '' ) {
		?><div id="quickshop-products"></div><?php
	} else {
		if( is_array( $product_categories ) && count( $product_categories ) > 1 ) {
			?><div class="cf7sendwa-woo-filter grid-100 grid-parent">
				<div class="filter-block grid-50">
					Category Filter
				</div>
				<div class="filter-block grid-50">
					<input type="text" id="cf7sendwa_woo_text_filter" placeholder="Type something">
				</div>
			</div><?php
			foreach( $product_categories as $cat ) {
				echo '<div id="cat-' . $cat['slug'] . '" class="product-cat-container">';
				echo 	'<h3 class="category-title">' . $cat['name'] . '</h3>';
				echo '</div>';
			}				
		} else {
			?><div class="cf7sendwa-woo-filter grid-100">
				<div class="filter-block">
					<input type="text" id="cf7sendwa_woo_text_filter" placeholder="Type something">
				</div>
			</div><?php
			$cat = $product_categories[0];
			echo '<div id="cat-' . $cat['slug'] . '" class="product-cat-container">';
			echo '</div>';
		}
	}
	?>
</div>