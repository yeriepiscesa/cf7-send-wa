// number spinner
function wcqib_refresh_quantity_increments() {
    jQuery("div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)").each(function(a, b) {
        var c = jQuery(b);
        c.addClass("buttons_added"), c.children().first().before('<a href="javascript:void(0);" class="button sp-woopos-minus">-</a>'), c.children().last().after('<a href="javascript:void(0);" class="button sp-woopos-plus">+</a>')
    });
}
String.prototype.getDecimals || (String.prototype.getDecimals = function() {
    var a = this,
        b = ("" + a).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
    return b ? Math.max(0, (b[1] ? b[1].length : 0) - (b[2] ? +b[2] : 0)) : 0
}), jQuery(document).on("click", ".sp-woopos-plus, .sp-woopos-minus", function() {
    var a = jQuery(this).closest(".quantity").find(".qty"),
        b = parseFloat(a.val()),
        c = parseFloat(a.attr("max")),
        d = parseFloat(a.attr("min")),
        e = a.attr("step");
    b && "" !== b && "NaN" !== b || (b = 0), "" !== c && "NaN" !== c || (c = ""), "" !== d && "NaN" !== d || (d = 0), "any" !== e && "" !== e && void 0 !== e && "NaN" !== parseFloat(e) || (e = 1), jQuery(this).is(".sp-woopos-plus") ? c && b >= c ? a.val(c) : a.val((b + parseFloat(e)).toFixed(e.getDecimals())) : d && b <= d ? a.val(d) : b > 0 && a.val((b - parseFloat(e)).toFixed(e.getDecimals())), a.trigger("change")
});
/* Main Class */
function Woo_QuickShop_Cart() {
	var self = this;
	self.products = ko.observableArray();
	self.items = ko.observableArray();
	self.total = ko.pureComputed( function(){
		var total = 0;
		_.each( self.items(), function( item,index,list ){
			total = total + ( item.price * item.qty() );
		} );
		return total;
	} );
	self.price_total = ko.pureComputed( function(){			
		return cf7sendwa.currency + ' ' + jQuery.number( self.total(), cf7sendwa.decimals, cf7sendwa.decimal_separator, cf7sendwa.thousand_separator );
	} );

	self.weight_total = ko.pureComputed( function(){
		var total = 0;
		_.each( self.items(), function( item, index, list ){
			var _w = parseFloat( item.prop.weight );
			if( isNaN( _w ) ) _w = Hooks.apply_filters( 'cf7sendwa_base_weight', 1000 );
			total = total + ( _w * item.qty() );
		} );
		return total;
	} );
	self.weight_total_html = ko.pureComputed( function(){
		return jQuery.number( self.weight_total(), cf7sendwa.decimals, cf7sendwa.decimal_separator, cf7sendwa.thousand_separator ) + ' ' + Hooks.apply_filters( 'cf7sendwa_weight_units', 'gr' );
	} );
	
	self.gotoItem = function(e){
		if( this.constructor.name == 'Woo_QuickShop_Cart_Item' ) {
			var id = this.id;
			var $target = jQuery( '#'+id ).closest( '.product-item' );
			if( $target.hasClass( 'variations' ) ) {
				var product_id = jQuery( '#'+id ).attr( 'data-product_id' );
				var $btn = jQuery( '.variant-option-button[data-var-id=' + product_id + ']' );
				if( !$btn.hasClass( 'active' ) ) {
					$btn.trigger( 'click' );
				}
			}
			jQuery( 'body' ).scrollTo( $target, 400, { offset: Hooks.apply_filters( 'cf7sendwa_order_review_item_click_offset', -50 ) } );
			var _x = window.setInterval(function(){
				$target.fadeOut(250).fadeIn(250);
				window.clearInterval( _x );
			}, 300);
			Hooks.do_action( 'cf7sendwa_after_order_review_item_click', { 'item': $target } );
		}
	}
	self.removeItem = function(e){
		if( this.constructor.name == 'Woo_QuickShop_Cart_Item' ) {
			var id = this.id;
            self.items.remove( this );
            jQuery('#'+id).val(0);
            jQuery('#'+id).trigger( 'change' );
        }
	}
	
	self.viewdetail = ko.observable({
		title:'Product Title',
		sku:'',
		images: '',
		price: '',
		excerpt:'',
		description:'',
		attributes: '',
		stock:''
	});
}
function Woo_QuickShop_ProductItem( cls, catId, title, prop ) {
	var self = this;
	self.cls = cls; // class container
	self.catId = catId; // category container id
	self.title = title; // product title
	self.prop = prop;
}
function Woo_QuickShop_Cart_Item( id, title, subtitle, qty, price, prop ){
	var self = this;
	self.id = id;
	self.title = title;
	self.subtitle = subtitle;
	self.qty = ko.observable(qty);
	self.price = price;
	self.subtotal = ko.pureComputed( function(){
		return self.qty() * self.price;
	} );
	self.price_html = ko.pureComputed( function(){
		return jQuery.number(self.price, cf7sendwa.decimals, cf7sendwa.decimal_separator, cf7sendwa.thousand_separator);
	} );
	self.subtotal_html = ko.pureComputed( function(){
		return cf7sendwa.currency + ' ' + jQuery.number(self.subtotal(), cf7sendwa.decimals, cf7sendwa.decimal_separator, cf7sendwa.thousand_separator );
	} );
	self.prop = prop;
}

(function( $ ) {
	'use strict';	
	var frm_ids = [];
    function wrap_spinner( id ) {
        $( '#'+id ).css( 'display','inline-block' )
                   .css( 'margin-top', '0px' )
                   .css( 'border', '0px' );
        $( '#'+id ).attr( 'step', '1' )
        $( '#'+id ).addClass( 'input-text qty text' );
        $( '#'+id ).wrap( '<div class="quantity buttons_added"></div>' );		
        $( '#'+id ).parent().prepend( '<a href="javascript:void(0);" class="button sp-woopos-minus">-</a>' );
        $( '#'+id ).parent().append( '<a href="javascript:void(0);" class="button sp-woopos-plus">+</a>' );
    } 
	
	var ajax_search_txt = '';
	var qty_buttons = [];
	var vm = new Woo_QuickShop_Cart();
    $.extend( Woo_QuickShop_Cart, {
        getVM: function(){
            return vm;
        }
    } );
    
    var sticky_num = 0;
    var sticky_state = {};
    function do_initiate_sticky( $sticker, spot_selector, property ) {
	    var sticker_id = $sticker.attr( 'id' );
	    if( sticker_id == undefined ) {
		    sticker_id = 'cf7sendwa-stick-'+sticky_num;
		    $sticker.attr( 'id', sticker_id );
		    sticky_num++;
	    }
	    if( !sticky_state[ sticker_id ] ) {
		    sticky_state[ sticker_id ] = {
				'mode': 'unstick',
				'prop': {}
			};
	    }
        $(window).on('resize scroll', function() {
	        if( $( spot_selector ).length ) {
	            var spot = $( spot_selector ).offset().top;
	            var viewport = $( this ).scrollTop() + parseInt(property.viewport_bottom);
	            var s_id = $sticker.attr( 'id' );
	            if( $sticker.length ) {
	                if( viewport >= spot ) {
		                if( sticky_state[ s_id ].mode == 'sticky' ) {
	                		$sticker.unstick( sticky_state[ s_id ].prop );
	                		sticky_state[ s_id ].mode = 'unstick';
	                	}
	                } else {
		                if( sticky_state[ s_id ].mode == 'unstick' ) {
							var _top = 0;
							var _bottom = 0;
							if( $( '#wpadminbar' ).length && cf7sendwa.is_mobile == '0' ) {
								_top = 30;
							}
						    if( property.top != '' ) {
							    _top = _top + parseInt(property.top);
						    }
						    if( property.bottom != '' ) {
							    _bottom = parseInt(property.bottom);
						    }
						    var prop = {
			                    'topSpacing': _top,
			                    'bottomSpacing': _bottom
		                    };
		                    $sticker.sticky( prop );
	                		sticky_state[ s_id ].mode = 'sticky';
	                		sticky_state[ s_id ].prop = prop;
		                }
	                }
				}
				
	        }
		} );
	}
	
	function quickshop_checkout_button( $element, has_form, html_hidden ){
		var button_html = '<button class="button cf7sendwa-add-to-cart">' + cf7sendwa_qsreview.cart_label + '</button>';
		if( !has_form ) {
			$element.append( button_html );
		}
		$element.append( html_hidden );			
	}
	
	$( document ).ready( function() {
		ko.applyBindings( vm );
		if( $( '#quickshop-products' ).length ) {
			var $qs = $( '#quickshop-products' );
			if( cf7sendwa.quickshop_atts.mode != 'silent' ) {
				$qs.loading();
			}
			var el_id = $qs.attr("id");
			load_products( el_id, '', function(el_id, cat_slug, data_count){
				if( cf7sendwa.quickshop_atts.mode != 'silent' ) {
					$qs.loading( 'stop' );
				}
			} );
		} else {
			$( '.product-cat-container' ).each( function( index, element ){
				var delay = Math.floor( Math.random() * 200 ) + ( index * 200 );
				var ajaxRun = window.setInterval( function(){
					var $el = $( element );	
					var el_id = $el.attr('id');
					var cat_slug = el_id.replace( 'cat-', '' );		
					$('#'+el_id).loading();
					load_products( el_id, cat_slug, function(el_id, cat_slug, data_count){
						$( '#'+el_id ).loading( 'stop' );
						if( data_count < 1 ) {
							$( '#'+el_id ).remove();
							$( '#cf7sendwa_woo_cat_filter option[value="'+cat_slug+'"]' ).remove();
						}	
						$( '#'+ el_id +'.product-cat-container' ).css( 'min-height', '0px' );
					} );
					clearInterval(ajaxRun);	
				}, delay );
			} );
		}
		
		var html_hidden = '<input type="hidden" name="quickshop_cart" id="cf7sendwa_quickshop_cart" value="">';		
		$( '.cf7sendwa-cf7-container' ).each( function(){
			var $frm = $( this ).find( 'form' );
			if( $frm.length ) {				
				if( $frm.find( '#cf7sendwa-quickshop-container' ).length ) {
					frm_ids.push( $frm.find( 'input[name=_wpcf7]' ).val() );
					quickshop_checkout_button( $frm.find( '.cf7sendwa-quickshop-checkout' ), true, html_hidden );
					Hooks.do_action( 'cf7sendwa_after_cf7_submit', { 'frm': $frm } );
				}
			}
		} );
		if( $( '#cf7sendwa-checkout' ).length ) {
			var $checkout = $( '#cf7sendwa-checkout' );
			var $frm = $checkout.find( 'form' );
			if( $frm.length == 0 ) {
				$frm = $checkout.closest( 'form' );
			}
			if( $frm.length ) {
				var frm_id = $frm.find( 'input[name=_wpcf7]' ).val();
				if( _.indexOf( frm_ids, frm_id ) == -1 ) {
					frm_ids.push( frm_id );
					quickshop_checkout_button( $frm, true, html_hidden );
				}
			} else {
				var $_el = $checkout.find( '.cf7sendwa-quickshop-checkout-container' );
				quickshop_checkout_button( Hooks.apply_filters( 'cf7sendwa_quickshop_button_container', $_el ), false, html_hidden );
			}
		};
		
		$( 'body' ).on( 'change', '.product-item .qty', product_qty_change );
		$( 'body' ).on( 'keyup', '.product-item .qty', product_qty_change );
		$( 'body' ).on( 'focus', '.product-item .qty', function(evt){
            $(this).select();
        } );
		
		$( 'body' ).on( 'click', '.variant-option-button', function(evt){
			evt.preventDefault();
			var var_id = $(this).attr('data-var-id');
			if( cf7sendwa.quickshop_atts.render == 'grid' ) {
				$( '.product-item-variations-var-'+var_id ).modal();
			} else {
				var $tgglEl = $( '.variations.var-'+var_id );
				$(this).toggleClass( 'active' );
				$tgglEl.slideToggle();			
				if( $(this).hasClass('active') ) {
					$(this).find( '.angle-down' ).hide();	
					$(this).find( '.angle-up' ).show();	
				} else {
					$(this).find( '.angle-down' ).show();	
					$(this).find( '.angle-up' ).hide();	
				}
			}	
		} );
		$( 'body' ).on( 'click', '.wpcf7-form-control.wpcf7-submit', function(evt){
			var $form = $(this).closest( 'form' );
			var $base = $form.find( '.cf7-basic-submit' );
			if( $base.length ) {
			   Hooks.do_action( 'cf7sendwa_before_basic_form_submit' );
			} else {
				var quickshop = ko.toJS( vm );
				delete quickshop.products;
				delete quickshop.viewdetail;
				$( '#cf7sendwa_quickshop_cart' ).val( ko.toJSON(quickshop) );
				if( quickshop && quickshop.total <= 0 ) {
					evt.preventDefault();
				}
			}
		} );
		
		// text filter 
		$( '#cf7sendwa_woo_text_filter' ).keyup( function( evt ){
			var text = $(this).val();
			if( (evt.which >= 65 && evt.which <= 90) || (evt.which >= 48 && evt.which <= 57) ) {				
				if( text.length >= 3 ) {
					var matches = _.filter( ko.toJS(vm.products()), function(item){
						var r = item.title.toLowerCase().search( text.toLowerCase() ) > -1 ? true : false;
						return r;
					} );
					if( matches.length > 0 ) {
						$( '#cf7sendwa-quickshop-container .product-item' ).hide();
						_.each( matches, function(item, index, list){
							$( '#cf7sendwa-quickshop-container ' + item.cls ).show();
						} );
					} else {
						$( '#cf7sendwa-quickshop-container .product-item' ).hide();
					}
				} 
			} else if( text == '' ) {
				$( '#cf7sendwa-quickshop-container .product-item' ).show();
			}
		} );
		
		// category filter 
		$( '.cf7sendwa-woo-categories' ).on( 'change', function(evt){
			var cat = $(this).val();
			if( cat == '' ) {
				$( '#cf7sendwa-quickshop-container .product-cat-container' ).show();
			} else {
				$( '#cf7sendwa-quickshop-container .product-cat-container' ).hide();	
				$( '#cf7sendwa-quickshop-container #cat-' + cat +'.product-cat-container' ).show();
			}
		} );
		
		
		// add to cart from quickshop
		$( 'body' ).on( 'click', '.cf7sendwa-add-to-cart', function(evt){
			var $this_button = $(this);
			$this_button.attr('disabled', true);
			var quickshop = ko.toJS( vm );	
			delete quickshop.products;
			delete quickshop.viewdetail;
			$( '#cf7sendwa_quickshop_cart' ).val( ko.toJSON(quickshop) );			
			if( quickshop && quickshop.total > 0 ) {
                var btn_text = $this_button.html();
                $this_button.attr( 'disabled', true );
                $this_button.loading( {message: 'Sending...'} );
				$.ajax( {
					url: cf7sendwa.ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						'action':'cf7sendwa_add_to_cart',
						'quickshop_cart': $( '#cf7sendwa_quickshop_cart' ).val(),
						'security': cf7sendwa.security,
						'redirect': cf7sendwa_qsreview.redirect
					},
					success: function(response){
						if( response ) {
							document.location = response.redirect_url;
                            $this_button.attr( 'disabled', false );
                            $this_button.html( btn_text );
						} else {
							$this_button.attr('disabled', false);;	
						}	
						var _timer = setInterval( function(){
                            $this_button.loading( 'toggle' );
                        }, 2000 );											
					}
				} );
			} else {
				$this_button.attr('disabled', false);;
			}
		} );
		
	    if( cf7sendwa_qsreview.sticky == 'yes' ) { // sticky checkout
		    do_initiate_sticky( $( '.cf7sendwa-quickshop-checkout-container' ), cf7sendwa_qsreview.stickyend, {
				'top': cf7sendwa_qsreview.top,
				'bottom': cf7sendwa_qsreview.bottom,
				'viewport_bottom': cf7sendwa_qsreview.viewport_bottom    
		    } );
	    }
        if( $( '.cf7sendwa-sticky' ).length ) {            
            do_initiate_sticky( $( '.cf7sendwa-sticky' ), cf7sendwa_qsreview.stickyend, {
                'top': cf7sendwa_qsreview.top,
                'bottom': cf7sendwa_qsreview.bottom,
                'viewport_bottom': cf7sendwa_qsreview.viewport_bottom    
            } );
        }
	    
	    if( $( '#cf7sendwa_woo_ajax_filter' ).length ) {
			$( '#cf7sendwa_woo_ajax_filter' ).keydown(function(evt){
				if( evt.which == 13 ) {
					evt.preventDefault();
					var text = $(this).val();
					do_ajax_search( text );
				}						
			});
            $( '#cf7sendwa_woo_ajax_filter' ).keyup( function(evt){
                if( evt.keyCode == 8 || evt.keyCode == 46 ) {
                    if( $(this).val() == '' ) {
                        do_ajax_search( '' );
                    }    
                }
            } );			
			$( '#cf7sendwa_woo_ajax_filter_button' ).click( function(evt){
				var text = $( '#cf7sendwa_woo_ajax_filter' ).val();
				do_ajax_search( text );
			} );
	    }
	    
	    //initialize cart
	    do_initiate_cart();
	    
    } );
    
    
    function do_initiate_cart() {
        if( cf7sendwa.cart !== undefined && cf7sendwa.cart.items.length ) {
            _.each( cf7sendwa.cart.items, function( item, index ){
                var item_id = 'prd-qty';
                var pa_terms = {};
                var subtitle = '';
                if( item.variation_id ) {
                    item_id += '-' + item.variation_id;
                    _.each( item.product_var, function( val, key ){
                        item_id += '-' + val;
                        pa_terms[ key.replace( 'attribute_', '' ) ] = val;
                    } );
                } else {
                    item_id += '-'+item.product_id;
                }
                if( item.variations.length ) {
                    _.each( item.variations, function( attributes, index ) {
                        if( subtitle != '' ) {
                            subtitle += ', ';
                        }
                        subtitle += attributes.key + ': ' + attributes.value;
                    } );
                }
                if( $( '#'+item_id ).length ) {
                    $( '#'+item_id ).val( item.quantity );
                    $( '#'+item_id ).trigger( 'change' );
                } else {
                    var prop = {
                        'product_id': item.product_id,
                        'product_type': item.product_type,
                        'variation_id': item.variation_id,
                        'sku': item.sku,
                        'weight': item.weight,
                        'pa': pa_terms
                    };
                    var cart_item = new Woo_QuickShop_Cart_Item( item_id, item.name, subtitle, item.quantity, item.price, prop );
                    vm.items.push(cart_item);
                }
            } );
        }  
   	}  

    function product_qty_change( evt ) {
		var qty = parseInt( $(this).val() );
		if( isNaN(qty) ) {
			qty = 0;
			$(this).val(qty);
		}
		var stock_status = $( this ).attr( 'data-stock' );
		if( stock_status == 'outofstock' ) {
			$(this).val(0);
			return false;	
		}
		var price = parseFloat( $(this).attr( 'data-price' ) );
		var $item = $(this).closest( '.item-price' );
		var subtotal = price * qty;
		var el_subtotal = $item.find( '.item-subtotal' );
		el_subtotal.html( cf7sendwa.currency+' '+jQuery.number(subtotal, cf7sendwa.decimals, cf7sendwa.decimal_separator, cf7sendwa.thousand_separator) );

		var id = $(this).prop( 'id' );
		var match = ko.utils.arrayFirst(vm.items(), function(item) {
		    return id === item.id;
		});
		if( !match && qty > 0 ){
			var $product = $( this ).closest( '.product-item' ).find( '.product-item-info h4' );
			var product_title = $product.text();
			var subtitle = '';
			var pa = $(this).attr( 'data-pa' );
			var pa_terms = {};
			if( pa ) {
				pa = jQuery.parseJSON( decodeURIComponent( pa ) );
				_.each(pa, function(item, index, list){
					_.each( item, function(val,key){
						pa_terms[key] = val;
					} );
				});
				var $prd_parent = $( '.product-item.prd-' + $(this).attr( 'data-product_id' ) + ' .product-item-info h4' );
				subtitle = product_title;
				product_title = $prd_parent.text();
			}
			var prop = {
				'product_id': $( this ).attr( 'data-product_id' ),
				'product_type': $( this ).attr( 'data-product_type' ),
				'variation_id': $( this ).attr( 'data-variation_id' ),
				'sku': $( this ).attr( 'data-sku' ),
				'weight': $( this ).attr( 'data-weight' ),
				'pa': pa_terms
			};
			var cart_item = new Woo_QuickShop_Cart_Item( id, product_title, subtitle, qty, price, prop );				
			vm.items.push(cart_item);
		} else if(match) {
			match.qty(qty);
			if( match.qty() == 0 ) {
				vm.items.remove(match);
			}
		}
    }
    
    $( 'body' ).on( 'click', '.woo-link-detail', function(evt){
	    evt.preventDefault();
	    if( cf7sendwa.quickshop_atts.detail == 'yes' ) {
		    var selector = $(this).attr( 'data-el-cls' );
			var detail = _.find( ko.toJS(vm.products()), function(item){
				return item.cls == selector;
			} );	    
			var view = (function( detail ){
				var obj = {};
				obj.title = detail.title;
				var __sku = detail.prop.sku;
				if( __sku != '' ) {
					__sku = '<span class="cf7sendwa-product-sku-label">SKU: </span>' + __sku;
				}
				obj.sku = __sku;
				obj.price = detail.prop.price_html;
				obj.excerpt = detail.prop.short_description;
				if( detail.prop.full_description.trim() == '' ) {
					obj.description = '';
				} else {
					obj.description = '<div class="product-item-detail-description">' + detail.prop.full_description + '</div>';
				}
				var img = '';
				if( detail.prop.images.length ) {
					var img_src = detail.prop.images[0].src;	
					img = '<img src="'+ img_src +'">';
					if( detail.prop.gallery.length ) {
						_.each( detail.prop.gallery, function( item ){
							img += '<img src="' + item + '">';
						} );
					}
				}
				obj.images = img;
				obj.attributes = detail.prop.attributes;
				obj.stock = detail.prop.stock_status;
				return obj;
			})(detail);
			vm.viewdetail( view );
		    $('#cf7sendwa-product-detail').modal();
		    $('#cf7sendwa-product-detail .fotorama' ).fotorama();
		}
    } );
    
    function load_products( el_id, cat_slug, callback ) {
	    if( cat_slug != '' ) {
		    cf7sendwa.quickshop_atts.category = cat_slug;
	    }
	    var products_page = 1;
	    if( _.has( cf7sendwa.quickshop_atts, 'page' ) ) {
		    products_page = cf7sendwa.quickshop_atts.page;
	    }
		$.ajax( {
			url: cf7sendwa.ajaxurl,
			type: 'POST',
			dataType: 'html',
			data: { 
				'action':'cf7sendwa_products', 
				'args': cf7sendwa.quickshop_atts,
				'security': cf7sendwa.security,
				'cf7sendwa_search': ajax_search_txt
			},
			success: function( response ) {
				$( '#'+el_id ).append( response );	
				var data_count = parseInt( $( '#'+el_id + ' .product-items' ).attr( 'data-total' ) );	
				
				var _cart = {};
				var _cart_subtot = {};
				if( vm.items().length ){
					var __cart = ko.toJS( vm.items );
					_.each( __cart, function( item, index, list ){
						_cart[ item.id ] = item.qty;	
						_cart_subtot[ item.id ] = item.subtotal_html;
					} );
				}
				
				$( '#'+el_id+' .item-subtotal' ).html( cf7sendwa.currency + ' 0' );
				$( '#'+el_id+' .product-items.page-'+ products_page + ' .qty' ).each( function(index, element) {
					var qty_id = $(element).attr('id');
					if( _.indexOf( qty_buttons, qty_id ) == -1 ) {
						wrap_spinner( qty_id );
						qty_buttons.push( qty_id );
						if( _cart[qty_id] != undefined ) {
							$( '#'+qty_id ).val( _cart[qty_id] );
							var $item = $('#'+qty_id).closest( '.item-price' );
							$item.find( '.item-subtotal' ).html( _cart_subtot[qty_id] );
						}
					} else {
						var $prd_item = $(element).closest( '.product-item' );
						$prd_item.remove();
					}
				} );		
                if( cf7sendwa.quickshop_atts.render == 'list' ) {
					$( '#'+el_id+' .product-items.page-' + products_page + ' .variations' ).hide();
					$( '#'+el_id+' .product-items.page-' + products_page + ' .variant-option-button .angle-up' ).hide();
				}
				if( typeof callback == 'function' ) {
					callback( el_id, cat_slug, data_count );
				}
				// add to item 
				var catId = '';
				var $container = $( '#'+el_id ).closest( '.product-cat-container' );
				if( $container.length ) {
					catId = $container.attr( 'id' );
				}
				if( cf7sendwa.quickshop_atts.filter == 'yes' || cf7sendwa.quickshop_atts.detail == 'yes' ) {
					$( '#'+el_id+' .product-items .product-item:not(.variations)' ).each( function(index){
						var $h4 = $(this).find( '.product-item-info h4' );
						var cls = $(this).attr('class');
						var clsArr = cls.split(" ");
						cls = "."+clsArr.join(".");
						var title = $h4.text();
						var $prop = $(this).find( '.woo-product-prop' );
						$( cls ).find( 'a.woo-link-detail' ).attr( 'data-el-cls', cls );
						vm.products.push( new Woo_QuickShop_ProductItem( cls, catId, title, jQuery.parseJSON($prop.val()) ) );
						$prop.remove();
						
						var _colors = cf7sendwa.quickshop_atts.colors;
						if( _colors != '' ) {
							_colors = _colors.split(',');
							if( index %2 != 0 ) {
								$(this).css( 'background-color', _colors[1] );	
							} else {
								$(this).css( 'background-color', _colors[0] );	
							}
						}
					} );				
				}	
				
				if( cf7sendwa.quickshop_atts.paging == 'loadmore' ) {
					if( cf7sendwa.quickshop_atts.page == undefined ) {
						cf7sendwa.quickshop_atts.page = 1;
					}
					$( '#'+cat_slug+'-page-'+cf7sendwa.quickshop_atts.page ).click( function(evt){
						evt.preventDefault();
						var $link = $(this);
						var link_id = $link.attr('id');
						var page = $link.attr( 'data-next' );
						var $cat_item = $link.closest('.product-cat-container');
						var el_id = $cat_item.attr('id');
						if( el_id != undefined ) {
							var cat_slug = el_id.replace( 'cat-', '' );
							var loader_id = link_id+'-loader';
							$link.after( '<div id="'+loader_id+'">&nbsp;</div>' );
							$( '#'+loader_id ).css( 'height', '50px' );
							$link.remove();
							$( '#'+loader_id ).loading();
							cf7sendwa.quickshop_atts.page = page;
							load_products( el_id, cat_slug, function(){
								$( '#'+loader_id ).loading('toggle');
								$( '#'+loader_id ).remove();
							} );
						}
					} );
				} else if( cf7sendwa.quickshop_atts.paging == 'auto' ) {
					// fetch next page 
					if( $( '#' + el_id  +' .quickshop-load-more' ).length ) {
						var $next = $( '#' + el_id  +' .quickshop-load-more' );
						var page = $next.attr( 'data-next' );
						cf7sendwa.quickshop_atts.page = page;
						$next.remove();
						load_products( el_id, cat_slug, callback );
					}
				}
			}
		} );
    }
	function do_ajax_search( txt ) {
		ajax_search_txt = txt;
		qty_buttons = [];
		cf7sendwa.quickshop_atts.page = 1;
		$( '.product-items' ).remove();
		$( '.cf7sendwa-quickshop-paging' ).remove();
		if( $( '#quickshop-products' ).length ) {
			var $qs = $( '#quickshop-products' );		
				
			$qs.loading();			
			var el_id = $qs.attr("id");
			load_products( el_id, '', function(el_id, cat_slug, data_count){
				$qs.loading( 'stop' );
			} );
		} else {
			$( '.product-cat-container' ).each( function( index, element ){
				var delay = Math.floor( Math.random() * 200 ) + ( index * 200 );
				var ajaxRun = window.setInterval( function(){
					var $el = $( element );	
					var el_id = $el.attr('id');
					var cat_slug = el_id.replace( 'cat-', '' );		
					$('#'+el_id).loading();
					load_products( el_id, cat_slug, function(el_id, cat_slug, data_count){
						$( '#'+el_id ).loading( 'stop' );
						$( '#'+ el_id +'.product-cat-container' ).css( 'min-height', '0px' );
					} );
					clearInterval(ajaxRun);	
				}, delay );
			} );
		}
        if( $( '#cf7sendwa-quickshop-container' ).length ) {
            var _scroll = window.setInterval( function(){
                $( 'body' ).scrollTo( $( '#cf7sendwa-quickshop-container' ), 400, { offset: Hooks.apply_filters( 'cf7sendwa_scroll_to_quickshop_container_offset', -100 ) } );
                clearInterval( _scroll );
            }, 500 );
        }		
	}
    
    // disable input zoom
    var $viewportMeta = $('meta[name="viewport"]');
    if( $viewportMeta.length ) {
	    $viewportMeta.attr('content', 'width=device-width,initial-scale=1,maximum-scale=1');
    }
    
	// select2
	if( $( '.cf7sendwa-woo-categories' ).length ) {
		$( '.cf7sendwa-woo-categories' ).select2( {
			placeholder: "Select Category", 
			allowClear: true
		} );
	}
})( jQuery );