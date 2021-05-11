<div class="item-block item-price sp-mobile-flex-content">
    <?php if( $product['prop']['type'] != 'variable' ): ?>

        <div class="cf7sendwa-div-wrapper">
            <?php
            $n_readonly = '';
            if( $args['editableqty'] == 'no' ) {
                $n_readonly = ' readonly="readonly"';
            }
            $def_value = "0";
            if( isset( $args['is_current_product'] ) && $args['is_current_product'] =='yes' ) {
                $def_value = "1";
            }
            ?>
            <input type="number" name="item_qty" step="1"<?php echo $n_readonly ?> value="<?php echo $def_value; ?>" 
                data-sku="<?php echo $product['prop']['sku'] ?>" 
                data-stock="<?php echo $product['prop']['stock_status'] ?>"
                data-price="<?php echo $product['prop']['price'] ?>" 
                data-weight="<?php echo $product['prop']['weight'] ?>" 
                data-product_type="<?php echo $product['prop']['type'] ?>"
                data-product_id="<?php echo $product['prop']['id'] ?>"
                data-variation_id="<?php echo $product['prop']['variation_id'] ?>"
                class="input-text qty text" id="prd-qty-<?php echo $product['prop']['id']; ?>"
                style="display: inline-block; margin-top: 0px;">
        </div>

        <?php if( !isset( $args['is_current_product'] ) ) : ?>
        <div class="cf7sendwa-div-wrapper">
            <div class="item-subtotal"></div>
        </div>
        <?php endif; ?>

    <?php else: ?>
    <button class="button variant-option-button" data-var-id="<?php echo $product['prop']['id']; ?>">
        <span class="angle-down"><i class="dashicons dashicons-arrow-down-alt2"></i> <?php echo __( 'Select Options', 'cf7sendwa' ) ?></span>
        <span class="angle-up"><i class="dashicons dashicons-arrow-up-alt2"></i> <?php echo __( 'Hide Options', 'cf7sendwa' ) ?></span>
    </button>
    <?php endif; ?>
</div>