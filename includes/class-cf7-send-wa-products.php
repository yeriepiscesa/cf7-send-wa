<?php	
class Cf7_Send_Wa_Products {
	
	public static function tax_settings() {
        $settings = [
            'rates' => WC_Tax::get_rates(),
            'prices_inc_tax' => get_option( 'woocommerce_prices_include_tax' ),
            'tax_based_on' => get_option( 'woocommerce_tax_based_on' ),
            'shipping_tax_class' => get_option( 'woocommerce_shipping_tax_class' ),
            'tax_rounding' => get_option( 'woocommerce_tax_round_at_subtotal' ),
            'tax_display_shop' => get_option( 'woocommerce_tax_display_shop' ),
            'tax_display_cart' => get_option( 'woocommerce_tax_display_cart' ),
            'tax_total_display' => get_option( 'woocommerce_tax_total_display' )
        ];
        return $settings;
    }
    
    public static function attributes_info( $product ) {
        
        $attributes = $product->get_attributes();
        $display_result = '<table class="sp-product-attributes"><tbody>';        
        if( $product->has_weight() ) {
            $wu = get_option('woocommerce_weight_unit');
            $display_result .= '<tr class="woocommerce-product-attributes-item">';
            $display_result .= '<td class="label">Berat</td>';
            $display_result .= '<td>' . $product->get_weight() . $wu . '</td>';
            $display_result .= '</tr>';
        }
        if( $product->has_dimensions() ) {
            $display_result .= '<tr class="woocommerce-product-attributes-item">';
            $display_result .= '<td class="label">Dimensi</td>';
            $display_result .= '<td>' . $product->get_dimensions() . '</td>';
            $display_result .= '</tr>';
        }
        
        if( $attributes ) {
            foreach ( $attributes as $attribute ) {
                $display_result .= '<tr class="woocommerce-product-attributes-item">';
                $name = $attribute->get_name();
                if ( $attribute->is_taxonomy() ) {
                    $terms = wp_get_post_terms( $product->get_id(), $name, 'all' );
                    $cwtax = $terms[0]->taxonomy;
                    $cw_object_taxonomy = get_taxonomy($cwtax);
                    if ( isset ($cw_object_taxonomy->labels->singular_name) ) {
                        $tax_label = $cw_object_taxonomy->labels->singular_name;
                    } elseif ( isset( $cw_object_taxonomy->label ) ) {
                        $tax_label = $cw_object_taxonomy->label;
                        if ( 0 === strpos( $tax_label, 'Product ' ) ) {
                            $tax_label = substr( $tax_label, 8 );
                        }
                    }
                    $display_result .= '<td class="label">' .$tax_label . '</td>';
                    $tax_terms = array();
                    foreach ( $terms as $term ) {
                        $single_term = esc_html( $term->name );
                        array_push( $tax_terms, $single_term );
                    }
                    $display_result .= '<td>' . implode(', ', $tax_terms) . '</td>';
                } else {
                    $display_result .= '<td class="label">'.$name . '</td>';
                    $display_result .= '<td>' . esc_html( implode( ', ', $attribute->get_options() ) ) . '</td>';
                }
                $display_result .= '</tr>';
            }
        }
        $display_result .= '</tbody></table>';
        
        return $display_result;
    }
    
    public static function list_all( $args=array() ) {
        
        $ts = self::tax_settings();
        $defaults = [
            'status' => 'publish',
            'type' => [ 'simple', 'variable' ],
            'limit' => $args['limit'],
            'page' => $args['page'],
            'paginate' => true,
            'orderby' => 'date',
            'order' => 'DESC'            
        ];
		
		$args = array_merge( $defaults, $args );        
        if( isset( $args['category'] ) ) {
            $args['category'] = [ $args['category'] ];
        }
        if( isset( $args['includes'] ) ) {
            $_includes = explode( ',', $args['includes'] );
            $args['include'] = $_includes;
        }
        //if( get_option( 'solusipress_pos_show_instock' ) == 'yes' ) {
            $args['stock_status'] = 'instock';
        //}
        $wc_products = wc_get_products( $args );
        $data = [];
        
        foreach( $wc_products->products as $product ) {
            $product_id = $product->get_id();          
            if( $product->get_type() == 'variable' ) {
                $rows = [];
                $available_variations = $product->get_available_variations();
                foreach( $available_variations as $var ) {
                    $item_meta = [];
                    $variation = new WC_Product_Variation( $var['variation_id'] );    
                    $product_var = $var['attributes'];   
                    $no_values = [];
                    $pa_terms = [];
                    foreach( $product_var as $k => $v ) {
                        if( $v == '' ) { 
                            array_push( $no_values, $k ); 
                        } else {
                            $_k = str_replace('attribute_', '', $k);
                            $_term = get_term_by( 'slug', $v, $_k );
                            if( $_term ) {
                                $label = wc_attribute_label( $_term->taxonomy, $product );
                                array_push( $item_meta, [
                                    'key' => $label,
                                    'value' => $_term->name
                                ] );
                                array_push( $pa_terms,[
	                                $_k => $v
                                ] );
                            } else {
                                $label = wc_attribute_label( $_k, $product );
                                array_push( $item_meta, [
                                    'key' => $label,
                                    'value' => $v
                                ] );
                                array_push( $pa_terms,[
	                                $_k => $v
                                ] );
                            }
                        }
                    }
                    if( !empty( $no_values ) ) {
                        
                        $_item_meta = $item_meta;
                        $_pa_terms = $pa_terms;
                        foreach( $no_values as $taxonomy ) {
                            $_tax = str_replace( 'attribute_', '', $taxonomy );
                            $label = wc_attribute_label( $_tax, $product );
                            $terms = wc_get_product_terms( $product_id, $_tax );    
                            foreach( $terms as $term ) {
                                array_push( $_item_meta, [
                                    'key' => $label,
                                    'value' => $term->name,
                                ] );
	                            array_push( $_pa_terms,[
	                                $_tax => $term->slug
	                            ] );
                                array_push( $rows, self::setProductData( $variation, $ts, $product, $_item_meta, [], $_pa_terms ) );
                                $_item_meta = $item_meta;
                                $_pa_terms = $pa_terms;
                            }
                        }
                        
                    } else {
                        array_push( $rows, self::setProductData( $variation, $ts, $product, $item_meta, [], $pa_terms ) );                        
                    }
                    
                }
                
                if( count( $rows ) >= 1 ) {
                    array_push( $data, self::setProductData( $product, $ts, null, [], $rows ) );                    
                }

            } else {                
                array_push( $data, self::setProductData( $product, $ts ) );
            }

        }
        
        $cat_id = '';
        if( !is_null( $category ) ) {
            $cat = get_term_by( 'slug', $category, 'product_cat' );
            if ( $cat instanceof WP_Term ) {
                $cat_id = $cat->term_id;
            }            
        }
        
        return [
            'total' => $wc_products->total,
            'pages' => $wc_products->max_num_pages,
            'category' => $cat_id,
            'results' => $data
        ];
    }
    
    public static function setProductData( $product, $ts, $parent=null, $item_meta=[], $variations=[], $pa_terms=[] ) { // $ts = tax settings
        $parent_type = '';
        if( is_null( $parent ) ) {
            $product_id = $product->get_id();
            $variation_id = null;
            $attributes = self::attributes_info( $product );
            $name = $product->get_name();
        } else {
            $product_id = $parent->get_id();
            $variation_id = $product->get_id();
            $attributes = '';
            if( !empty( $item_meta ) ) {
	            $title = '';
	            foreach( $item_meta as $meta ) {
		        	if( $title != '') $title .= ', ';
		        	$title .= $meta['key'].': '. $meta['value'];
	            }
	            $name = $title;
            } else {
            	$name = $product->get_title();
            }
            $parent_type = $parent->get_type();
        }
        
        $price = $product->get_price();
        $sku = $product->get_sku();
        $taxes = WC_Tax::calc_tax( $price, $ts['rates'], $ts['prices_inc_tax'] == 'yes' ? true:false );
        $tax = 0;
        if( is_array( $taxes ) && !empty( $taxes ) ) {
            $tax = $taxes[1];
        }
        $price_with_tax = $price + $tax;
        $regular_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();
        $price_html = $product->get_price_html();
        
        if( $product->get_type() == 'variation' ) {
            if( $product->get_image_id() == $parent->get_image_id() ) {
                $img_src = '';                
            } else {
                $img_id = $product->get_image_id();
                $img_src = wp_get_attachment_image_url( $img_id );            
            }
        } else {
            $img_src = get_the_post_thumbnail_url( $product_id );
        }
        
        $weight = $product->get_weight();    
        $manage_stock = $product->get_manage_stock();
        $stock_quantity = $product->get_stock_quantity();
        $stock_status = $product->get_stock_status();
        
        $full_desc = '';
        $image_gallery = [];
        if( is_null( $parent ) ) {
            
            $short_desc = $product->get_short_description();
            $full_desc = $product->get_description();
            $image_ids = $product->get_gallery_image_ids();
            if( is_array( $image_ids ) && !empty( $image_ids ) ) {
                foreach( $image_ids as $id ) {
                    $img = wp_get_attachment_image_src( $id, 'full' );
                    array_push( $image_gallery, $img[0] );
                }
            }
            
        } else {
            $short_desc = $parent->get_short_description();
        }
        $categories = wc_get_product_cat_ids( $product_id );
        $row = [
            'name' => $name,
            'qty' => 0,
            'prop' => [
                'id' => $product_id,    
                'sku' => $sku,
                'type' => $product->get_type(),
                'parent_type' => $parent_type,
                'variation_id' => $variation_id,
                'price' => $price,
                'sale_price' => $sale_price,
                'regular_price' => $regular_price,
                'tax' => $tax,
                'price_with_tax' => $price_with_tax,
                'price_html' => $price_html,
                'images' => [ [ 'src' => $img_src ] ],
                'gallery' => $image_gallery,
                'manage_stock' => $manage_stock,
                'stock_status' => $stock_status,
                'stock_quantity' => $stock_quantity,
                'short_description' => $short_desc,
                'full_description' => $full_desc, 
                'permalink' => $product->get_permalink(),
                'weight' => $weight,
                'categories' => $categories,
                'attributes' => $attributes,
                'item_data' => $item_meta,
            ]
        ];   
        if( !empty( $variations ) ) {
	        $row['prop']['variations'] = $variations;
        }             
        if( !empty( $pa_terms ) ) {
	        $row['prop']['pa_terms'] = $pa_terms;
	    }
        return $row;
    }  
	
}