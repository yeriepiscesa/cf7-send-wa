<div id="cf7sendwa-quickshop-container">
	<?php 
	if( is_array( $product_categories ) && count( $product_categories ) > 1 ) {
		foreach( $product_categories as $cat ) {
			echo '<div id="cat-' . $cat['slug'] . '" class="product-cat-container">';
			echo 	'<h3 class="category-title">' . $cat['name'] . '</h3>';
			echo '</div>';
		}				
	} else {
		$cat = $product_categories[0];
		echo '<div id="cat-' . $cat['slug'] . '" class="product-cat-container">';
		echo '</div>';
	}
	?>
</div>